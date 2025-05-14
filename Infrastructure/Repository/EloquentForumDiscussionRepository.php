<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Infrastructure\Repository;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Src\ArticleGenerator\Domain\Entity\ForumDiscussion;
use Src\ArticleGenerator\Domain\Entity\ForumCategory;
use Src\ArticleGenerator\Domain\Repository\ForumDiscussionRepositoryInterface;
use Src\ArticleGenerator\Domain\ValueObject\VehicleReference;
use Src\ArticleGenerator\Domain\Exception\DomainException;
use Src\ArticleGenerator\Infrastructure\Eloquent\ForumDiscussion as EloquentForumDiscussion;

/**
 * Implementação Eloquent da interface ForumDiscussionRepositoryInterface
 */
class EloquentForumDiscussionRepository implements ForumDiscussionRepositoryInterface
{
    /**
     * Prefixo para as chaves de cache
     * 
     * @var string
     */
    private const CACHE_PREFIX = 'discussion:';
    
    /**
     * Tempo de expiração do cache em segundos (1 hora)
     * 
     * @var int
     */
    private const CACHE_TTL = 3600;
    
    /**
     * Converte um modelo Eloquent para uma entidade de domínio
     * 
     * @param EloquentForumDiscussion $model Modelo Eloquent
     * @return ForumDiscussion Entidade de domínio
     */
    private function toDomainEntity(EloquentForumDiscussion $model): ForumDiscussion
    {
        // Converte o category para o enum ForumCategory
        $category = ForumCategory::from($model->category);
        
        // Cria uma nova entidade de domínio com os dados do modelo
        return new ForumDiscussion(
            $model->id,
            $model->title,
            $model->content,
            $model->tags ?? [],
            $model->forum_url,
            $category,
            $model->published_at,
            $model->usage_count,
            $model->last_used_at,
            $model->relevance_score
        );
    }
    
    /**
     * Converte uma entidade de domínio para um modelo Eloquent
     * 
     * @param ForumDiscussion $entity Entidade de domínio
     * @return EloquentForumDiscussion Modelo Eloquent
     */
    private function toEloquentModel(ForumDiscussion $entity): EloquentForumDiscussion
    {
        // Busca o modelo existente ou cria um novo
        if ($entity->id !== null) {
            try {
                $model = EloquentForumDiscussion::findOrFail($entity->id);
            } catch (ModelNotFoundException $e) {
                $model = new EloquentForumDiscussion();
                $model->id = $entity->id;
            }
        } else {
            $model = new EloquentForumDiscussion();
        }
        
        // Atualiza os atributos
        $model->title = $entity->getTitle();
        $model->content = $entity->getContent();
        $model->tags = $entity->getTags();
        $model->forum_url = $entity->getForumUrl();
        $model->category = $entity->getCategory()->value;
        $model->published_at = $entity->getPublishedAt();
        $model->usage_count = $entity->getUsageCount();
        $model->last_used_at = $entity->getLastUsedAt();
        $model->relevance_score = $entity->getRelevanceScore();
        
        return $model;
    }
    
    /**
     * Limpa o cache de uma discussão específica
     * 
     * @param string $id ID da discussão
     * @return void
     */
    private function clearCache(string $id): void
    {
        Cache::forget(self::CACHE_PREFIX . $id);
        Cache::forget(self::CACHE_PREFIX . 'all');
        Cache::forget(self::CACHE_PREFIX . 'count');
    }
    
    /**
     * {@inheritdoc}
     */
    public function save(ForumDiscussion $discussion): ForumDiscussion
    {
        try {
            DB::beginTransaction();
            
            $model = $this->toEloquentModel($discussion);
            $model->save();
            
            // Atualiza o ID da entidade caso tenha sido gerado
            if ($discussion->id === null) {
                $discussion = new ForumDiscussion(
                    $model->id,
                    $discussion->getTitle(),
                    $discussion->getContent(),
                    $discussion->getTags(),
                    $discussion->getForumUrl(),
                    $discussion->getCategory(),
                    $discussion->getPublishedAt(),
                    $discussion->getUsageCount(),
                    $discussion->getLastUsedAt(),
                    $discussion->getRelevanceScore()
                );
            }
            
            DB::commit();
            
            // Limpa o cache
            if ($model->id) {
                $this->clearCache($model->id);
            }
            
            return $discussion;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new DomainException(
                "Erro ao salvar discussão: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function delete(ForumDiscussion $discussion): bool
    {
        if ($discussion->id === null) {
            return false;
        }
        
        try {
            DB::beginTransaction();
            
            $result = EloquentForumDiscussion::where('id', $discussion->id)->delete();
            
            DB::commit();
            
            // Limpa o cache
            $this->clearCache($discussion->id);
            
            return $result > 0;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new DomainException(
                "Erro ao excluir discussão: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function findById(string $id): ?ForumDiscussion
    {
        try {
            // Tenta recuperar do cache
            return Cache::remember(
                self::CACHE_PREFIX . $id,
                self::CACHE_TTL,
                function () use ($id) {
                    $model = EloquentForumDiscussion::find($id);
                    
                    if ($model === null) {
                        return null;
                    }
                    
                    return $this->toDomainEntity($model);
                }
            );
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar discussão: {$e->getMessage()}",
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
            $models = EloquentForumDiscussion::query()
                ->orderBy('published_at', 'desc')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            
            return $models->map(function (EloquentForumDiscussion $model) {
                return $this->toDomainEntity($model);
            })->all();
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao listar discussões: {$e->getMessage()}",
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
            // Usando whereJsonContains para buscar no array de tags
            $models = EloquentForumDiscussion::whereJsonContains('tags', $topic)
                ->orWhere('title', 'like', "%{$topic}%")
                ->orWhere('content', 'like', "%{$topic}%")
                ->orderBy('published_at', 'desc')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            
            return $models->map(function (EloquentForumDiscussion $model) {
                return $this->toDomainEntity($model);
            })->all();
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar discussões por tópico: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function findBySource(string $source, int $page = 1, int $perPage = 15): array
    {
        try {
            $models = EloquentForumDiscussion::bySource($source)
                ->orderBy('published_at', 'desc')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            
            return $models->map(function (EloquentForumDiscussion $model) {
                return $this->toDomainEntity($model);
            })->all();
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar discussões por fonte: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function findByCategory(ForumCategory $category, int $page = 1, int $perPage = 15): array
    {
        try {
            $models = EloquentForumDiscussion::byCategory($category->value)
                ->orderBy('published_at', 'desc')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            
            return $models->map(function (EloquentForumDiscussion $model) {
                return $this->toDomainEntity($model);
            })->all();
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar discussões por categoria: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function findByRelevance(int $minRelevanceScore, int $page = 1, int $perPage = 15): array
    {
        try {
            $models = EloquentForumDiscussion::byMinRelevance($minRelevanceScore)
                ->orderBy('relevance_score', 'desc')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            
            return $models->map(function (EloquentForumDiscussion $model) {
                return $this->toDomainEntity($model);
            })->all();
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar discussões por relevância: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function filterByVehicle(VehicleReference|string $vehicle, int $page = 1, int $perPage = 15): array
    {
        try {
            $query = EloquentForumDiscussion::query();
            
            if ($vehicle instanceof VehicleReference) {
                $make = $vehicle->make;
                $model = $vehicle->model;
                
                $query->byVehicle($make, $model);
            } else {
                // Considerando o parâmetro como string que representa marca ou modelo
                $query->byVehicle($vehicle);
            }
            
            $models = $query->orderBy('published_at', 'desc')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            
            return $models->map(function (EloquentForumDiscussion $model) {
                return $this->toDomainEntity($model);
            })->all();
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao filtrar discussões por veículo: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getRelevantInsights(string $context, array $keywords = [], int $limit = 5): array
    {
        try {
            $cacheKey = self::CACHE_PREFIX . 'insights:' . md5($context . json_encode($keywords) . $limit);
            
            return Cache::remember(
                $cacheKey,
                self::CACHE_TTL,
                function () use ($context, $keywords, $limit) {
                    $query = EloquentForumDiscussion::query();
                    
                    // Procura o contexto no título ou conteúdo
                    $query->where(function ($q) use ($context) {
                        $q->where('title', 'like', "%{$context}%")
                            ->orWhere('content', 'like', "%{$context}%");
                    });
                    
                    // Adiciona filtros por palavras-chave (operação AND)
                    if (!empty($keywords)) {
                        $query->byKeywords($keywords);
                    }
                    
                    // Ordenar por relevância e data de publicação
                    $models = $query->orderBy('relevance_score', 'desc')
                        ->orderBy('published_at', 'desc')
                        ->limit($limit)
                        ->get();
                    
                    return $models->map(function (EloquentForumDiscussion $model) {
                        return $this->toDomainEntity($model);
                    })->all();
                }
            );
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar insights relevantes: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function markAsUsed(ForumDiscussion $discussion): ForumDiscussion
    {
        if ($discussion->id === null) {
            throw new DomainException(
                "Não é possível marcar como utilizada uma discussão sem ID",
                'REPOSITORY_ERROR'
            );
        }
        
        try {
            DB::beginTransaction();
            
            $model = EloquentForumDiscussion::findOrFail($discussion->id);
            $model->incrementUsage();
            
            // Atualiza a entidade de domínio
            $updatedDiscussion = $discussion->markAsUsed();
            
            DB::commit();
            
            // Limpa o cache
            $this->clearCache($discussion->id);
            
            return $updatedDiscussion;
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            throw new DomainException(
                "Discussão não encontrada: {$discussion->id}",
                'ENTITY_NOT_FOUND'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            throw new DomainException(
                "Erro ao marcar discussão como utilizada: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getRandomUnused(int $recentDays = 30): ?ForumDiscussion
    {
        try {
            // Este tipo de consulta não é cacheável devido à aleatoriedade
            $model = EloquentForumDiscussion::notRecentlyUsed($recentDays)
                ->inRandomOrder()
                ->first();
            
            if ($model === null) {
                return null;
            }
            
            return $this->toDomainEntity($model);
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar discussão aleatória não utilizada: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getRandomByCategory(ForumCategory $category, int $minRelevanceScore = 0): ?ForumDiscussion
    {
        try {
            $query = EloquentForumDiscussion::byCategory($category->value);
            
            if ($minRelevanceScore > 0) {
                $query->where('relevance_score', '>=', $minRelevanceScore);
            }
            
            $model = $query->inRandomOrder()->first();
            
            if ($model === null) {
                return null;
            }
            
            return $this->toDomainEntity($model);
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar discussão aleatória por categoria: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function search(string $searchText, int $page = 1, int $perPage = 15): array
    {
        try {
            $models = EloquentForumDiscussion::search($searchText)
                ->orderBy('relevance_score', 'desc')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            
            return $models->map(function (EloquentForumDiscussion $model) {
                return $this->toDomainEntity($model);
            })->all();
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar discussões: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getRecent(int $days = 30, int $page = 1, int $perPage = 15): array
    {
        try {
            $models = EloquentForumDiscussion::recent($days)
                ->orderBy('published_at', 'desc')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            
            return $models->map(function (EloquentForumDiscussion $model) {
                return $this->toDomainEntity($model);
            })->all();
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar discussões recentes: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function findByKeywords(array $keywords, int $page = 1, int $perPage = 15): array
    {
        try {
            $models = EloquentForumDiscussion::byKeywords($keywords)
                ->orderBy('relevance_score', 'desc')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            
            return $models->map(function (EloquentForumDiscussion $model) {
                return $this->toDomainEntity($model);
            })->all();
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar discussões por palavras-chave: {$e->getMessage()}",
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
                    return EloquentForumDiscussion::count();
                }
            );
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao contar discussões: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
}