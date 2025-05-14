<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Infrastructure\Repository;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Src\ArticleGenerator\Domain\Entity\HumanPersona;
use Src\ArticleGenerator\Domain\Repository\HumanPersonaRepositoryInterface;
use Src\ArticleGenerator\Domain\ValueObject\PersonaName;
use Src\ArticleGenerator\Domain\ValueObject\Profession;
use Src\ArticleGenerator\Domain\Exception\DomainException;
use Src\ArticleGenerator\Infrastructure\Eloquent\HumanPersona as EloquentHumanPersona;

/**
 * Implementação Eloquent da interface HumanPersonaRepositoryInterface
 */
class EloquentHumanPersonaRepository implements HumanPersonaRepositoryInterface
{
    /**
     * Prefixo para as chaves de cache
     * 
     * @var string
     */
    private const CACHE_PREFIX = 'persona:';
    
    /**
     * Tempo de expiração do cache em segundos (1 hora)
     * 
     * @var int
     */
    private const CACHE_TTL = 3600;
    
    /**
     * Converte um modelo Eloquent para uma entidade de domínio
     * 
     * @param EloquentHumanPersona $model Modelo Eloquent
     * @return HumanPersona Entidade de domínio
     */
    private function toDomainEntity(EloquentHumanPersona $model): HumanPersona
    {
        // Converte o nome para um ValueObject PersonaName
        $personaName = new PersonaName($model->first_name, $model->last_name);
        
        // Cria um ValueObject Profession (simplificado, em uma implementação completa
        // seria necessário mapear para uma enum ProfessionCategory corretamente)
        $profession = $model->profession;
        
        // Cria uma nova entidade de domínio com os dados do modelo
        $persona = new HumanPersona(
            $model->id,
            $personaName,
            $profession,
            $model->location,
            $model->preferred_vehicles ?? [],
            $model->usage_count,
            $model->last_used_at
        );
        
        return $persona;
    }
    
    /**
     * Converte uma entidade de domínio para um modelo Eloquent
     * 
     * @param HumanPersona $entity Entidade de domínio
     * @return EloquentHumanPersona Modelo Eloquent
     */
    private function toEloquentModel(HumanPersona $entity): EloquentHumanPersona
    {
        // Busca o modelo existente ou cria um novo
        if ($entity->id !== null) {
            try {
                $model = EloquentHumanPersona::findOrFail($entity->id);
            } catch (ModelNotFoundException $e) {
                $model = new EloquentHumanPersona();
                $model->id = $entity->id;
            }
        } else {
            $model = new EloquentHumanPersona();
        }
        
        // Obtém o nome da persona (pode ser um ValueObject ou string)
        $name = $entity->getName();
        if ($name instanceof PersonaName) {
            $model->first_name = $name->firstName;
            $model->last_name = $name->lastName;
        } else {
            // Se for string, tenta dividir em primeiro e último nome
            $nameParts = explode(' ', $name, 2);
            $model->first_name = $nameParts[0];
            $model->last_name = $nameParts[1] ?? '';
        }
        
        // Atualiza os demais atributos
        $model->profession = $entity->getProfession();
        $model->location = $entity->getLocation();
        $model->preferred_vehicles = $entity->getPreferredVehicles();
        $model->usage_count = $entity->getUsageCount();
        $model->last_used_at = $entity->getLastUsedAt();
        
        return $model;
    }
    
    /**
     * Limpa o cache de uma persona específica
     * 
     * @param string $id ID da persona
     * @return void
     */
    private function clearCache(string $id): void
    {
        Cache::forget(self::CACHE_PREFIX . $id);
        Cache::forget(self::CACHE_PREFIX . 'all');
    }
    
    /**
     * {@inheritdoc}
     */
    public function save(HumanPersona $persona): HumanPersona
    {
        try {
            DB::beginTransaction();
            
            $model = $this->toEloquentModel($persona);
            $model->save();
            
            // Atualiza o ID da entidade caso tenha sido gerado
            if ($persona->id === null) {
                $persona = new HumanPersona(
                    $model->id,
                    $persona->getName(),
                    $persona->getProfession(),
                    $persona->getLocation(),
                    $persona->getPreferredVehicles(),
                    $persona->getUsageCount(),
                    $persona->getLastUsedAt()
                );
            }
            
            DB::commit();
            
            // Limpa o cache
            if ($model->id) {
                $this->clearCache($model->id);
            }
            
            return $persona;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new DomainException(
                "Erro ao salvar persona: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function delete(HumanPersona $persona): bool
    {
        if ($persona->id === null) {
            return false;
        }
        
        try {
            DB::beginTransaction();
            
            $result = EloquentHumanPersona::where('id', $persona->id)->delete();
            
            DB::commit();
            
            // Limpa o cache
            $this->clearCache($persona->id);
            
            return $result > 0;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new DomainException(
                "Erro ao excluir persona: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function findById(string $id): ?HumanPersona
    {
        try {
            // Tenta recuperar do cache
            return Cache::remember(
                self::CACHE_PREFIX . $id,
                self::CACHE_TTL,
                function () use ($id) {
                    $model = EloquentHumanPersona::find($id);
                    
                    if ($model === null) {
                        return null;
                    }
                    
                    return $this->toDomainEntity($model);
                }
            );
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar persona: {$e->getMessage()}",
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
            $models = EloquentHumanPersona::query()
                ->orderBy('created_at', 'desc')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            
            return $models->map(function (EloquentHumanPersona $model) {
                return $this->toDomainEntity($model);
            })->all();
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao listar personas: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function findByProfession(Profession|string $profession, int $page = 1, int $perPage = 15): array
    {
        try {
            // Converte Profession para string se necessário
            $professionStr = $profession instanceof Profession ? (string)$profession : $profession;
            
            $models = EloquentHumanPersona::byProfession($professionStr)
                ->orderBy('created_at', 'desc')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            
            return $models->map(function (EloquentHumanPersona $model) {
                return $this->toDomainEntity($model);
            })->all();
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar personas por profissão: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function findByLocation(string $location, int $page = 1, int $perPage = 15): array
    {
        try {
            $models = EloquentHumanPersona::byLocation($location)
                ->orderBy('created_at', 'desc')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            
            return $models->map(function (EloquentHumanPersona $model) {
                return $this->toDomainEntity($model);
            })->all();
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar personas por localização: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getRandomUnused(int $recentDays = 7): ?HumanPersona
    {
        try {
            // Este tipo de consulta não é cacheável devido à aleatoriedade
            $model = EloquentHumanPersona::notRecentlyUsed($recentDays)
                ->inRandomOrder()
                ->first();
            
            if ($model === null) {
                return null;
            }
            
            return $this->toDomainEntity($model);
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar persona aleatória não utilizada: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getRandomWithMaxUsageCount(int $maxUsageCount): ?HumanPersona
    {
        try {
            $model = EloquentHumanPersona::maxUsageCount($maxUsageCount)
                ->inRandomOrder()
                ->first();
            
            if ($model === null) {
                return null;
            }
            
            return $this->toDomainEntity($model);
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar persona aleatória com uso máximo: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function markAsUsed(HumanPersona $persona): HumanPersona
    {
        if ($persona->id === null) {
            throw new DomainException(
                "Não é possível marcar como utilizada uma persona sem ID",
                'REPOSITORY_ERROR'
            );
        }
        
        try {
            DB::beginTransaction();
            
            $model = EloquentHumanPersona::findOrFail($persona->id);
            $model->incrementUsage();
            
            // Atualiza a entidade de domínio
            $updatedPersona = $persona->markAsUsed();
            
            DB::commit();
            
            // Limpa o cache
            $this->clearCache($persona->id);
            
            return $updatedPersona;
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            throw new DomainException(
                "Persona não encontrada: {$persona->id}",
                'ENTITY_NOT_FOUND'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            throw new DomainException(
                "Erro ao marcar persona como utilizada: {$e->getMessage()}",
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
                'totalPersonas' => EloquentHumanPersona::count(),
                'totalUsages' => EloquentHumanPersona::sum('usage_count'),
                'averageUsage' => EloquentHumanPersona::avg('usage_count'),
                'mostUsed' => null,
                'leastUsed' => null,
                'recentlyUsed' => EloquentHumanPersona::whereNotNull('last_used_at')
                    ->where('last_used_at', '>=', now()->subDays(7))
                    ->count(),
                'unusedCount' => EloquentHumanPersona::where('usage_count', 0)->count()
            ];
            
            // Busca a persona mais utilizada
            $mostUsed = EloquentHumanPersona::orderBy('usage_count', 'desc')->first();
            if ($mostUsed) {
                $stats['mostUsed'] = [
                    'id' => $mostUsed->id,
                    'name' => $mostUsed->getFullName(),
                    'usageCount' => $mostUsed->usage_count
                ];
            }
            
            // Busca a persona menos utilizada (com pelo menos um uso)
            $leastUsed = EloquentHumanPersona::where('usage_count', '>', 0)
                ->orderBy('usage_count', 'asc')
                ->first();
            if ($leastUsed) {
                $stats['leastUsed'] = [
                    'id' => $leastUsed->id,
                    'name' => $leastUsed->getFullName(),
                    'usageCount' => $leastUsed->usage_count
                ];
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
                    return EloquentHumanPersona::count();
                }
            );
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao contar personas: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function findByPreferredVehicle(
        string $vehicleMake,
        ?string $vehicleModel = null,
        int $page = 1,
        int $perPage = 15
    ): array {
        try {
            $models = EloquentHumanPersona::byPreferredVehicle($vehicleMake, $vehicleModel)
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            
            return $models->map(function (EloquentHumanPersona $model) {
                return $this->toDomainEntity($model);
            })->all();
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar personas por veículo preferido: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getLeastUsedPersonas(int $limit = 10): array
    {
        try {
            $models = EloquentHumanPersona::orderByLeastUsed()
                ->limit($limit)
                ->get();
            
            return $models->map(function (EloquentHumanPersona $model) {
                return $this->toDomainEntity($model);
            })->all();
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar personas menos utilizadas: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function searchByName(string $name, int $page = 1, int $perPage = 15): array
    {
        try {
            $models = EloquentHumanPersona::searchByName($name)
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            
            return $models->map(function (EloquentHumanPersona $model) {
                return $this->toDomainEntity($model);
            })->all();
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar personas por nome: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
}