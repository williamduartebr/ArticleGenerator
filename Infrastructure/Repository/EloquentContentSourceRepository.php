<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Infrastructure\Repository;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Src\ArticleGenerator\Domain\Entity\ContentSource;
use Src\ArticleGenerator\Domain\Entity\ContentSourceType;
use Src\ArticleGenerator\Domain\Repository\ContentSourceRepositoryInterface;
use Src\ArticleGenerator\Domain\Exception\DomainException;
use Src\ArticleGenerator\Infrastructure\Eloquent\ContentSource as EloquentContentSource;
use DateTimeImmutable;

/**
 * Implementação Eloquent da interface ContentSourceRepositoryInterface
 */
class EloquentContentSourceRepository implements ContentSourceRepositoryInterface
{
    /**
     * Prefixo para as chaves de cache
     * 
     * @var string
     */
    private const CACHE_PREFIX = 'source:';
    
    /**
     * Tempo de expiração do cache em segundos (1 hora)
     * 
     * @var int
     */
    private const CACHE_TTL = 3600;
    
    /**
     * Converte um modelo Eloquent para uma entidade de domínio
     * 
     * @param EloquentContentSource $model Modelo Eloquent
     * @return ContentSource Entidade de domínio
     */
    private function toDomainEntity(EloquentContentSource $model): ContentSource
    {
        // Mapeia o type para o enum ContentSourceType
        $type = ContentSourceType::from($model->type);
        
        // Cria uma nova entidade de domínio com os dados do modelo
        return new ContentSource(
            $model->id,
            $model->name,
            $model->url,
            $type,
            $model->trust_score,
            $model->topics ?? [],
            $model->last_crawled_at,
            $model->is_active,
            $model->usage_count,
            $model->last_used_at
        );
    }
    
    /**
     * Converte uma entidade de domínio para um modelo Eloquent
     * 
     * @param ContentSource $entity Entidade de domínio
     * @return EloquentContentSource Modelo Eloquent
     */
    private function toEloquentModel(ContentSource $entity): EloquentContentSource
    {
        // Busca o modelo existente ou cria um novo
        if ($entity->id !== null) {
            try {
                $model = EloquentContentSource::findOrFail($entity->id);
            } catch (ModelNotFoundException $e) {
                $model = new EloquentContentSource();
                $model->id = $entity->id;
            }
        } else {
            $model = new EloquentContentSource();
        }
        
        // Atualiza os atributos
        $model->name = $entity->getName();
        $model->url = $entity->getUrl();
        $model->type = $entity->getType()->value;
        $model->trust_score = $entity->getTrustScore();
        $model->topics = $entity->getTopics();
        $model->last_crawled_at = $entity->getLastCrawledAt();
        $model->is_active = $entity->isActive();
        $model->usage_count = $entity->getUsageCount();
        $model->last_used_at = $entity->getLastUsedAt();
        
        return $model;
    }
    
    /**
     * Limpa o cache de uma fonte específica
     * 
     * @param string $id ID da fonte
     * @return void
     */
    private function clearCache(string $id): void
    {
        Cache::forget(self::CACHE_PREFIX . $id);
        Cache::forget(self::CACHE_PREFIX . 'all');
        Cache::forget(self::CACHE_PREFIX . 'count');
        Cache::forget(self::CACHE_PREFIX . 'count:active');
    }
    
    /**
     * {@inheritdoc}
     */
    public function save(ContentSource $source): ContentSource
    {
        try {
            DB::beginTransaction();
            
            $model = $this->toEloquentModel($source);
            $model->save();
            
            // Atualiza o ID da entidade caso tenha sido gerado
            if ($source->id === null) {
                $source = new ContentSource(
                    $model->id,
                    $source->getName(),
                    $source->getUrl(),
                    $source->getType(),
                    $source->getTrustScore(),
                    $source->getTopics(),
                    $source->getLastCrawledAt(),
                    $source->isActive(),
                    $source->getUsageCount(),
                    $source->getLastUsedAt()
                );
            }
            
            DB::commit();
            
            // Limpa o cache
            if ($model->id) {
                $this->clearCache($model->id);
            }
            
            return $source;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new DomainException(
                "Erro ao salvar fonte de conteúdo: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function delete(ContentSource $source): bool
    {
        if ($source->id === null) {
            return false;
        }
        
        try {
            DB::beginTransaction();
            
            $result = EloquentContentSource::where('id', $source->id)->delete();
            
            DB::commit();
            
            // Limpa o cache
            $this->clearCache($source->id);
            
            return $result > 0;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new DomainException(
                "Erro ao excluir fonte de conteúdo: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function findById(string $id): ?ContentSource
    {
        try {
            // Tenta recuperar do cache
            return Cache::remember(
                self::CACHE_PREFIX . $id,
                self::CACHE_TTL,
                function () use ($id) {
                    $model = EloquentContentSource::find($id);
                    
                    if ($model === null) {
                        return null;
                    }
                    
                    return $this->toDomainEntity($model);
                }
            );
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar fonte de conteúdo: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function findAll(int $page = 1, int $perPage = 15): array
    {
        try {
            // Para listas paginadas, não usamos cache para evitar problemas com paginação
            $models = EloquentContentSource::query()
                ->orderBy('name')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            
            return $models->map(function (EloquentContentSource $model) {
                return $this->toDomainEntity($model);
            })->all();
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao listar fontes de conteúdo: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function findByType(ContentSourceType $type, int $page = 1, int $perPage = 15): array
    {
        try {
            $models = EloquentContentSource::byType($type->value)
                ->orderBy('name')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            
            return $models->map(function (EloquentContentSource $model) {
                return $this->toDomainEntity($model);
            })->all();
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar fontes de conteúdo por tipo: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function findByMinTrustScore(float $minTrustScore, int $page = 1, int $perPage = 15): array
    {
        try {
            $models = EloquentContentSource::byMinTrustScore($minTrustScore)
                ->orderBy('trust_score', 'desc')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            
            return $models->map(function (EloquentContentSource $model) {
                return $this->toDomainEntity($model);
            })->all();
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar fontes de conteúdo por confiabilidade: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function findActive(int $page = 1, int $perPage = 15): array
    {
        try {
            $models = EloquentContentSource::active()
                ->orderBy('name')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            
            return $models->map(function (EloquentContentSource $model) {
                return $this->toDomainEntity($model);
            })->all();
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar fontes de conteúdo ativas: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function findByTopic(string $topic, int $page = 1, int $perPage = 15): array
    {
        try {
            $models = EloquentContentSource::byTopic($topic)
                ->orderBy('name')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            
            return $models->map(function (EloquentContentSource $model) {
                return $this->toDomainEntity($model);
            })->all();
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar fontes de conteúdo por tópico: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getTrustedByType(ContentSourceType $type, float $minTrustScore = 70.0, int $limit = 5): array
    {
        try {
            $cacheKey = self::CACHE_PREFIX . 'trusted:' . $type->value . ':' . $minTrustScore . ':' . $limit;
            
            return Cache::remember(
                $cacheKey,
                self::CACHE_TTL,
                function () use ($type, $minTrustScore, $limit) {
                    $models = EloquentContentSource::byType($type->value)
                        ->byMinTrustScore($minTrustScore)
                        ->orderBy('trust_score', 'desc')
                        ->limit($limit)
                        ->get();
                    
                    return $models->map(function (EloquentContentSource $model) {
                        return $this->toDomainEntity($model);
                    })->all();
                }
            );
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar fontes de conteúdo confiáveis por tipo: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function markAsUsed(ContentSource $source): ContentSource
    {
        if ($source->id === null) {
            throw new DomainException(
                "Não é possível marcar como utilizada uma fonte sem ID",
                'REPOSITORY_ERROR'
            );
        }
        
        try {
            DB::beginTransaction();
            
            $model = EloquentContentSource::findOrFail($source->id);
            $model->incrementUsage();
            
            // Atualiza a entidade de domínio
            $updatedSource = $source->markAsUsed();
            
            DB::commit();
            
            // Limpa o cache
            $this->clearCache($source->id);
            
            return $updatedSource;
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            throw new DomainException(
                "Fonte de conteúdo não encontrada: {$source->id}",
                'ENTITY_NOT_FOUND'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            throw new DomainException(
                "Erro ao marcar fonte de conteúdo como utilizada: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function markAsCrawled(ContentSource $source): ContentSource
    {
        if ($source->id === null) {
            throw new DomainException(
                "Não é possível marcar como extraída uma fonte sem ID",
                'REPOSITORY_ERROR'
            );
        }
        
        try {
            DB::beginTransaction();
            
            $model = EloquentContentSource::findOrFail($source->id);
            $model->markAsCrawled();
            
            // Atualiza a entidade de domínio
            $updatedSource = $source->markAsCrawled();
            
            DB::commit();
            
            // Limpa o cache
            $this->clearCache($source->id);
            
            return $updatedSource;
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            throw new DomainException(
                "Fonte de conteúdo não encontrada: {$source->id}",
                'ENTITY_NOT_FOUND'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            throw new DomainException(
                "Erro ao marcar fonte de conteúdo como extraída: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function findNeedsCrawling(int $daysThreshold = 7, int $limit = 10): array
    {
        try {
            $models = EloquentContentSource::needsCrawling($daysThreshold)
                ->limit($limit)
                ->get();
            
            return $models->map(function (EloquentContentSource $model) {
                return $this->toDomainEntity($model);
            })->all();
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar fontes de conteúdo que precisam ser extraídas: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function activate(ContentSource $source): ContentSource
    {
        if ($source->id === null) {
            throw new DomainException(
                "Não é possível ativar uma fonte sem ID",
                'REPOSITORY_ERROR'
            );
        }
        
        try {
            DB::beginTransaction();
            
            $model = EloquentContentSource::findOrFail($source->id);
            $model->activate();
            
            // Atualiza a entidade de domínio
            $updatedSource = $source->activate();
            
            DB::commit();
            
            // Limpa o cache
            $this->clearCache($source->id);
            
            return $updatedSource;
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            throw new DomainException(
                "Fonte de conteúdo não encontrada: {$source->id}",
                'ENTITY_NOT_FOUND'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            throw new DomainException(
                "Erro ao ativar fonte de conteúdo: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function deactivate(ContentSource $source): ContentSource
    {
        if ($source->id === null) {
            throw new DomainException(
                "Não é possível desativar uma fonte sem ID",
                'REPOSITORY_ERROR'
            );
        }
        
        try {
            DB::beginTransaction();
            
            $model = EloquentContentSource::findOrFail($source->id);
            $model->deactivate();
            
            // Atualiza a entidade de domínio
            $updatedSource = $source->deactivate();
            
            DB::commit();
            
            // Limpa o cache
            $this->clearCache($source->id);
            
            return $updatedSource;
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            throw new DomainException(
                "Fonte de conteúdo não encontrada: {$source->id}",
                'ENTITY_NOT_FOUND'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            throw new DomainException(
                "Erro ao desativar fonte de conteúdo: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function search(string $search, int $page = 1, int $perPage = 15): array
    {
        try {
            $models = EloquentContentSource::search($search)
                ->orderBy('name')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            
            return $models->map(function (EloquentContentSource $model) {
                return $this->toDomainEntity($model);
            })->all();
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar fontes de conteúdo: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getMostTrusted(int $limit = 10): array
    {
        try {
            $cacheKey = self::CACHE_PREFIX . 'most-trusted:' . $limit;
            
            return Cache::remember(
                $cacheKey,
                self::CACHE_TTL,
                function () use ($limit) {
                    $models = EloquentContentSource::active()
                        ->orderBy('trust_score', 'desc')
                        ->limit($limit)
                        ->get();
                    
                    return $models->map(function (EloquentContentSource $model) {
                        return $this->toDomainEntity($model);
                    })->all();
                }
            );
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar fontes de conteúdo mais confiáveis: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        try {
            return Cache::remember(
                self::CACHE_PREFIX . 'count',
                self::CACHE_TTL,
                function () {
                    return EloquentContentSource::count();
                }
            );
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao contar fontes de conteúdo: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function countActive(): int
    {
        try {
            return Cache::remember(
                self::CACHE_PREFIX . 'count:active',
                self::CACHE_TTL,
                function () {
                    return EloquentContentSource::active()->count();
                }
            );
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao contar fontes de conteúdo ativas: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
}