<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Infrastructure\Repository;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Src\ArticleGenerator\Domain\Entity\BrazilianLocation;
use Src\ArticleGenerator\Domain\Entity\BrazilianStateCode;
use Src\ArticleGenerator\Domain\Entity\TrafficPattern;
use Src\ArticleGenerator\Domain\Repository\BrazilianLocationRepositoryInterface;
use Src\ArticleGenerator\Domain\Exception\DomainException;
use Src\ArticleGenerator\Infrastructure\Eloquent\BrazilianLocation as EloquentBrazilianLocation;

/**
 * Implementação Eloquent da interface BrazilianLocationRepositoryInterface
 */
class EloquentBrazilianLocationRepository implements BrazilianLocationRepositoryInterface
{
    /**
     * Prefixo para as chaves de cache
     * 
     * @var string
     */
    private const CACHE_PREFIX = 'location:';
    
    /**
     * Tempo de expiração do cache em segundos (1 hora)
     * 
     * @var int
     */
    private const CACHE_TTL = 3600;
    
    /**
     * Converte um modelo Eloquent para uma entidade de domínio
     * 
     * @param EloquentBrazilianLocation $model Modelo Eloquent
     * @return BrazilianLocation Entidade de domínio
     */
    private function toDomainEntity(EloquentBrazilianLocation $model): BrazilianLocation
    {
        // Mapeia o state_code para o enum BrazilianStateCode
        $stateCode = BrazilianStateCode::from($model->state_code);
        
        // Mapeia o traffic_pattern para o enum TrafficPattern
        $trafficPattern = TrafficPattern::from($model->traffic_pattern);
        
        // Cria uma nova entidade de domínio com os dados do modelo
        $location = new BrazilianLocation(
            $model->id,
            $model->city,
            $model->region,
            $trafficPattern,
            $stateCode,
            $model->usage_count,
            $model->last_used_at
        );
        
        return $location;
    }
    
    /**
     * Converte uma entidade de domínio para um modelo Eloquent
     * 
     * @param BrazilianLocation $entity Entidade de domínio
     * @return EloquentBrazilianLocation Modelo Eloquent
     */
    private function toEloquentModel(BrazilianLocation $entity): EloquentBrazilianLocation
    {
        // Busca o modelo existente ou cria um novo
        if ($entity->id !== null) {
            try {
                $model = EloquentBrazilianLocation::findOrFail($entity->id);
            } catch (ModelNotFoundException $e) {
                $model = new EloquentBrazilianLocation();
                $model->id = $entity->id;
            }
        } else {
            $model = new EloquentBrazilianLocation();
        }
        
        // Atualiza os atributos
        $model->city = $entity->getCity();
        $model->region = $entity->getRegion();
        $model->traffic_pattern = $entity->getTrafficPattern()->value;
        $model->state_code = $entity->getStateCode()->value;
        $model->usage_count = $entity->getUsageCount();
        $model->last_used_at = $entity->getLastUsedAt();
        
        return $model;
    }
    
    /**
     * Limpa o cache de uma localização específica
     * 
     * @param string $id ID da localização
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
    public function save(BrazilianLocation $location): BrazilianLocation
    {
        try {
            DB::beginTransaction();
            
            $model = $this->toEloquentModel($location);
            $model->save();
            
            // Atualiza o ID da entidade caso tenha sido gerado
            if ($location->id === null) {
                $location = new BrazilianLocation(
                    $model->id,
                    $location->getCity(),
                    $location->getRegion(),
                    $location->getTrafficPattern(),
                    $location->getStateCode(),
                    $location->getUsageCount(),
                    $location->getLastUsedAt()
                );
            }
            
            DB::commit();
            
            // Limpa o cache
            if ($model->id) {
                $this->clearCache($model->id);
            }
            
            return $location;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new DomainException(
                "Erro ao salvar localização: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function delete(BrazilianLocation $location): bool
    {
        if ($location->id === null) {
            return false;
        }
        
        try {
            DB::beginTransaction();
            
            $result = EloquentBrazilianLocation::where('id', $location->id)->delete();
            
            DB::commit();
            
            // Limpa o cache
            $this->clearCache($location->id);
            
            return $result > 0;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new DomainException(
                "Erro ao excluir localização: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function findById(string $id): ?BrazilianLocation
    {
        try {
            // Tenta recuperar do cache
            return Cache::remember(
                self::CACHE_PREFIX . $id,
                self::CACHE_TTL,
                function () use ($id) {
                    $model = EloquentBrazilianLocation::find($id);
                    
                    if ($model === null) {
                        return null;
                    }
                    
                    return $this->toDomainEntity($model);
                }
            );
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar localização: {$e->getMessage()}",
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
            $models = EloquentBrazilianLocation::query()
                ->orderBy('created_at', 'desc')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            
            return $models->map(function (EloquentBrazilianLocation $model) {
                return $this->toDomainEntity($model);
            })->all();
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao listar localizações: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function findByRegion(string $region, int $page = 1, int $perPage = 15): array
    {
        try {
            $models = EloquentBrazilianLocation::byRegion($region)
                ->orderBy('city')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            
            return $models->map(function (EloquentBrazilianLocation $model) {
                return $this->toDomainEntity($model);
            })->all();
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar localizações por região: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function findByState(BrazilianStateCode $stateCode, int $page = 1, int $perPage = 15): array
    {
        try {
            $models = EloquentBrazilianLocation::byState($stateCode->value)
                ->orderBy('city')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            
            return $models->map(function (EloquentBrazilianLocation $model) {
                return $this->toDomainEntity($model);
            })->all();
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar localizações por estado: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function findByCity(string $city, int $page = 1, int $perPage = 15): array
    {
        try {
            $models = EloquentBrazilianLocation::byCity($city)
                ->orderBy('region')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            
            return $models->map(function (EloquentBrazilianLocation $model) {
                return $this->toDomainEntity($model);
            })->all();
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar localizações por cidade: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function findByTrafficPattern(TrafficPattern $trafficPattern, int $page = 1, int $perPage = 15): array
    {
        try {
            $models = EloquentBrazilianLocation::byTrafficPattern($trafficPattern->value)
                ->orderBy('city')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            
            return $models->map(function (EloquentBrazilianLocation $model) {
                return $this->toDomainEntity($model);
            })->all();
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar localizações por padrão de tráfego: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getRandomUnused(int $recentDays = 7): ?BrazilianLocation
    {
        try {
            // Este tipo de consulta não é cacheável devido à aleatoriedade
            $model = EloquentBrazilianLocation::notRecentlyUsed($recentDays)
                ->inRandomOrder()
                ->first();
            
            if ($model === null) {
                return null;
            }
            
            return $this->toDomainEntity($model);
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar localização aleatória não utilizada: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getRandomByTrafficPattern(TrafficPattern $trafficPattern): ?BrazilianLocation
    {
        try {
            $model = EloquentBrazilianLocation::byTrafficPattern($trafficPattern->value)
                ->inRandomOrder()
                ->first();
            
            if ($model === null) {
                return null;
            }
            
            return $this->toDomainEntity($model);
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar localização aleatória por padrão de tráfego: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getRandomByState(BrazilianStateCode $stateCode): ?BrazilianLocation
    {
        try {
            $model = EloquentBrazilianLocation::byState($stateCode->value)
                ->inRandomOrder()
                ->first();
            
            if ($model === null) {
                return null;
            }
            
            return $this->toDomainEntity($model);
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar localização aleatória por estado: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function markAsUsed(BrazilianLocation $location): BrazilianLocation
    {
        if ($location->id === null) {
            throw new DomainException(
                "Não é possível marcar como utilizada uma localização sem ID",
                'REPOSITORY_ERROR'
            );
        }
        
        try {
            DB::beginTransaction();
            
            $model = EloquentBrazilianLocation::findOrFail($location->id);
            $model->incrementUsage();
            
            // Atualiza a entidade de domínio
            $updatedLocation = $location->markAsUsed();
            
            DB::commit();
            
            // Limpa o cache
            $this->clearCache($location->id);
            
            return $updatedLocation;
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            throw new DomainException(
                "Localização não encontrada: {$location->id}",
                'ENTITY_NOT_FOUND'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            throw new DomainException(
                "Erro ao marcar localização como utilizada: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getUsageStatistics(): array
    {
        try {
            // Esta consulta não é cacheada pois os dados mudam frequentemente
            $stats = [
                'totalLocations' => EloquentBrazilianLocation::count(),
                'totalUsages' => EloquentBrazilianLocation::sum('usage_count'),
                'averageUsage' => EloquentBrazilianLocation::avg('usage_count'),
                'mostUsed' => null,
                'leastUsed' => null,
                'recentlyUsed' => EloquentBrazilianLocation::whereNotNull('last_used_at')
                    ->where('last_used_at', '>=', now()->subDays(7))
                    ->count(),
                'locationsByState' => [],
                'locationsByTrafficPattern' => []
            ];
            
            // Busca a localização mais utilizada
            $mostUsed = EloquentBrazilianLocation::orderBy('usage_count', 'desc')->first();
            if ($mostUsed) {
                $stats['mostUsed'] = [
                    'id' => $mostUsed->id,
                    'name' => $mostUsed->getFullLocationName(),
                    'usageCount' => $mostUsed->usage_count
                ];
            }
            
            // Busca a localização menos utilizada (com pelo menos um uso)
            $leastUsed = EloquentBrazilianLocation::where('usage_count', '>', 0)
                ->orderBy('usage_count', 'asc')
                ->first();
            if ($leastUsed) {
                $stats['leastUsed'] = [
                    'id' => $leastUsed->id,
                    'name' => $leastUsed->getFullLocationName(),
                    'usageCount' => $leastUsed->usage_count
                ];
            }
            
            // Contagem por estado
            $byState = DB::table('brazilian_locations')
                ->select('state_code', DB::raw('count(*) as total'))
                ->groupBy('state_code')
                ->get();
            
            foreach ($byState as $state) {
                $stats['locationsByState'][$state->state_code] = $state->total;
            }
            
            // Contagem por padrão de tráfego
            $byTrafficPattern = DB::table('brazilian_locations')
                ->select('traffic_pattern', DB::raw('count(*) as total'))
                ->groupBy('traffic_pattern')
                ->get();
            
            foreach ($byTrafficPattern as $pattern) {
                $stats['locationsByTrafficPattern'][$pattern->traffic_pattern] = $pattern->total;
            }
            
            return $stats;
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao obter estatísticas de uso: {$e->getMessage()}",
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
                    return EloquentBrazilianLocation::count();
                }
            );
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao contar localizações: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getLeastUsedLocations(int $limit = 10): array
    {
        try {
            $models = EloquentBrazilianLocation::orderByLeastUsed()
                ->limit($limit)
                ->get();
            
            return $models->map(function (EloquentBrazilianLocation $model) {
                return $this->toDomainEntity($model);
            })->all();
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar localizações menos utilizadas: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getRandomByCity(string $city): ?BrazilianLocation
    {
        try {
            $model = EloquentBrazilianLocation::byCity($city)
                ->inRandomOrder()
                ->first();
            
            if ($model === null) {
                return null;
            }
            
            return $this->toDomainEntity($model);
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar localização aleatória por cidade: {$e->getMessage()}",
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
            $models = EloquentBrazilianLocation::search($search)
                ->orderBy('city')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            
            return $models->map(function (EloquentBrazilianLocation $model) {
                return $this->toDomainEntity($model);
            })->all();
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar localizações: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
}