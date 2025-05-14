<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Infrastructure\Repository;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Src\ArticleGenerator\Domain\Entity\HumanPersona;
use Src\ArticleGenerator\Domain\Entity\BrazilianLocation;
use Src\ArticleGenerator\Domain\Entity\ForumDiscussion;
use Src\ArticleGenerator\Domain\ValueObject\VehicleReference;
use Src\ArticleGenerator\Domain\Repository\ArticleHumanizationElementsRepositoryInterface;
use Src\ArticleGenerator\Domain\Repository\HumanPersonaRepositoryInterface;
use Src\ArticleGenerator\Domain\Repository\BrazilianLocationRepositoryInterface;
use Src\ArticleGenerator\Domain\Repository\ForumDiscussionRepositoryInterface;
use Src\ArticleGenerator\Domain\Exception\DomainException;

/**
 * Implementação Eloquent da interface ArticleHumanizationElementsRepositoryInterface
 * 
 * Esta classe coordena os repositórios individuais para fornecer conjuntos
 * completos de elementos de humanização para geração de artigos.
 */
class EloquentArticleHumanizationElementsRepository implements ArticleHumanizationElementsRepositoryInterface
{
    /**
     * Prefixo para as chaves de cache
     * 
     * @var string
     */
    private const CACHE_PREFIX = 'humanization:';
    
    /**
     * Tempo de expiração do cache em segundos (30 minutos)
     * 
     * @var int
     */
    private const CACHE_TTL = 1800;
    
    /**
     * @var array<string, array<string>> $recentCombinations Registro de combinações recentes
     */
    private array $recentCombinations = [];
    
    /**
     * Construtor
     * 
     * @param HumanPersonaRepositoryInterface $personaRepository Repositório de personas
     * @param BrazilianLocationRepositoryInterface $locationRepository Repositório de localizações
     * @param ForumDiscussionRepositoryInterface $discussionRepository Repositório de discussões
     */
    public function __construct(
        private readonly HumanPersonaRepositoryInterface $personaRepository,
        private readonly BrazilianLocationRepositoryInterface $locationRepository,
        private readonly ForumDiscussionRepositoryInterface $discussionRepository
    ) {
        // Inicializa o registro de combinações recentes
        $this->loadRecentCombinations();
    }
    
    /**
     * Carrega combinações recentes do cache
     * 
     * @return void
     */
    private function loadRecentCombinations(): void
    {
        $this->recentCombinations = Cache::get(
            self::CACHE_PREFIX . 'recent_combinations',
            [
                'persona_location' => [],
                'persona_discussion' => [],
                'location_discussion' => []
            ]
        );
    }
    
    /**
     * Salva combinações recentes no cache
     * 
     * @return void
     */
    private function saveRecentCombinations(): void
    {
        Cache::put(
            self::CACHE_PREFIX . 'recent_combinations',
            $this->recentCombinations,
            self::CACHE_TTL * 2 // Dobro do TTL padrão para persistir por mais tempo
        );
    }
    
    /**
     * Registra uma combinação persona-localização recente
     * 
     * @param string $personaId ID da persona
     * @param string $locationId ID da localização
     * @return void
     */
    private function registerPersonaLocationCombination(string $personaId, string $locationId): void
    {
        $key = "{$personaId}_{$locationId}";
        
        // Adiciona a combinação se ainda não existir
        if (!in_array($key, $this->recentCombinations['persona_location'])) {
            array_push($this->recentCombinations['persona_location'], $key);
            
            // Limita o tamanho do array (mantém apenas as últimas 50 combinações)
            if (count($this->recentCombinations['persona_location']) > 50) {
                array_shift($this->recentCombinations['persona_location']);
            }
            
            // Atualiza o cache
            $this->saveRecentCombinations();
        }
    }
    
    /**
     * Verifica se uma combinação persona-localização foi utilizada recentemente
     * 
     * @param string $personaId ID da persona
     * @param string $locationId ID da localização
     * @return bool
     */
    private function isPersonaLocationCombinationRecent(string $personaId, string $locationId): bool
    {
        $key = "{$personaId}_{$locationId}";
        return in_array($key, $this->recentCombinations['persona_location']);
    }
    
    /**
     * Filtra palavras-chave a partir de um contexto para uso em consultas
     * 
     * @param string $context Contexto do artigo
     * @param array<string> $additionalKeywords Palavras-chave adicionais
     * @return array<string> Palavras-chave extraídas
     */
    private function extractKeywordsFromContext(string $context, array $additionalKeywords = []): array
    {
        // Lista de palavras a serem ignoradas (stop words)
        $stopWords = [
            'a', 'e', 'o', 'as', 'os', 'um', 'uma', 'uns', 'umas', 'de', 'do', 'da', 'dos', 'das',
            'em', 'no', 'na', 'nos', 'nas', 'com', 'por', 'para', 'que', 'se', 'como', 'mas', 'ou',
            'ao', 'à', 'pelo', 'pela', 'pelos', 'pelas'
        ];
        
        // Extrai palavras do contexto
        $contextWords = explode(' ', strtolower($context));
        
        // Filtra palavras com menos de 4 caracteres e stop words
        $keywords = array_filter($contextWords, function ($word) use ($stopWords) {
            return strlen($word) >= 4 && !in_array($word, $stopWords);
        });
        
        // Adiciona palavras-chave extras fornecidas
        $keywords = array_merge($keywords, $additionalKeywords);
        
        // Remove duplicatas e reindexar array
        $keywords = array_values(array_unique($keywords));
        
        // Limita o número de palavras-chave para evitar queries muito complexas
        return array_slice($keywords, 0, 5);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getRandomCompatiblePersona(
        string $articleContext, 
        array $keywords = [],
        ?VehicleReference $vehicle = null
    ): ?HumanPersona {
        try {
            // Extrai palavras-chave do contexto
            $extractedKeywords = $this->extractKeywordsFromContext($articleContext, $keywords);
            
            // Se tiver um veículo especificado, tenta obter uma persona com preferência por esse veículo
            if ($vehicle !== null) {
                $vehiclePersonas = $this->personaRepository->findByPreferredVehicle(
                    $vehicle->make,
                    $vehicle->model
                );
                
                // Se encontrou personas compatíveis, retorna uma aleatoriamente
                if (!empty($vehiclePersonas)) {
                    return $vehiclePersonas[array_rand($vehiclePersonas)];
                }
            }
            
            // Tenta obter uma persona não utilizada recentemente
            $persona = $this->personaRepository->getRandomUnused(7);
            
            // Se não encontrou, tenta uma com uso mínimo
            if ($persona === null) {
                $personas = $this->personaRepository->getLeastUsedPersonas(10);
                
                if (!empty($personas)) {
                    $persona = $personas[array_rand($personas)];
                }
            }
            
            return $persona;
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao obter persona compatível: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function getRandomCompatibleLocation(
        string $articleContext,
        ?HumanPersona $persona = null
    ): ?BrazilianLocation {
        try {
            // Se tiver uma persona, tenta obter uma localização na mesma cidade
            if ($persona !== null && $persona->getLocation() !== '') {
                $personaLocation = $persona->getLocation();
                $locationsByCity = $this->locationRepository->findByCity($personaLocation);
                
                // Se encontrou localizações compatíveis, retorna uma aleatoriamente
                if (!empty($locationsByCity)) {
                    return $locationsByCity[array_rand($locationsByCity)];
                }
            }
            
            // Tenta obter uma localização não utilizada recentemente
            $location = $this->locationRepository->getRandomUnused(7);
            
            // Se não encontrou, tenta uma com uso mínimo
            if ($location === null) {
                $locations = $this->locationRepository->getLeastUsedLocations(10);
                
                if (!empty($locations)) {
                    $location = $locations[array_rand($locations)];
                }
            }
            
            return $location;
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao obter localização compatível: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function getRelevantDiscussions(
        string $articleContext,
        array $keywords = [],
        ?VehicleReference $vehicle = null,
        int $limit = 3
    ): array {
        try {
            // Extrai palavras-chave do contexto
            $extractedKeywords = $this->extractKeywordsFromContext($articleContext, $keywords);
            
            // Obtém discussões relevantes para o contexto e palavras-chave
            $relevantDiscussions = $this->discussionRepository->getRelevantInsights(
                $articleContext,
                $extractedKeywords,
                $limit * 2 // Buscamos o dobro e depois filtramos
            );
            
            // Se não encontrou discussões relevantes ou encontrou menos que o limite
            if (count($relevantDiscussions) < $limit) {
                // Tenta obter discussões relacionadas ao veículo se especificado
                if ($vehicle !== null) {
                    $vehicleDiscussions = $this->discussionRepository->filterByVehicle($vehicle);
                    
                    // Adiciona discussões relacionadas ao veículo se não estiverem já incluídas
                    foreach ($vehicleDiscussions as $discussion) {
                        if (!$this->isDiscussionInArray($discussion, $relevantDiscussions)) {
                            $relevantDiscussions[] = $discussion;
                            
                            if (count($relevantDiscussions) >= $limit) {
                                break;
                            }
                        }
                    }
                }
            }
            
            // Se ainda faltam discussões, obtém discussões recentes
            if (count($relevantDiscussions) < $limit) {
                $recentDiscussions = $this->discussionRepository->getRecent(90);
                
                // Adiciona discussões recentes que não estejam já incluídas
                foreach ($recentDiscussions as $discussion) {
                    if (!$this->isDiscussionInArray($discussion, $relevantDiscussions)) {
                        $relevantDiscussions[] = $discussion;
                        
                        if (count($relevantDiscussions) >= $limit) {
                            break;
                        }
                    }
                }
            }
            
            // Limita ao número solicitado
            return array_slice($relevantDiscussions, 0, $limit);
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao obter discussões relevantes: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * Verifica se uma discussão já está incluída em um array de discussões
     * 
     * @param ForumDiscussion $discussion Discussão a verificar
     * @param array<ForumDiscussion> $discussionArray Array de discussões
     * @return bool
     */
    private function isDiscussionInArray(ForumDiscussion $discussion, array $discussionArray): bool
    {
        foreach ($discussionArray as $existingDiscussion) {
            if ($existingDiscussion->id === $discussion->id) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getHumanizationSet(
        string $articleContext,
        array $keywords = [],
        ?VehicleReference $vehicle = null
    ): array {
        try {
            // Obtém os componentes do conjunto de humanização
            $persona = $this->getRandomCompatiblePersona($articleContext, $keywords, $vehicle);
            $location = $this->getRandomCompatibleLocation($articleContext, $persona);
            $discussions = $this->getRelevantDiscussions($articleContext, $keywords, $vehicle);
            
            // Verifica se os componentes foram obtidos com sucesso
            $isComplete = $persona !== null && $location !== null && !empty($discussions);
            
            // Verifica se a combinação foi utilizada recentemente
            $isUnique = true;
            if ($persona !== null && $location !== null && $persona->id !== null && $location->id !== null) {
                $isUnique = !$this->isPersonaLocationCombinationRecent($persona->id, $location->id);
            }
            
            return [
                'persona' => $persona,
                'location' => $location,
                'discussions' => $discussions,
                'isComplete' => $isComplete,
                'isUnique' => $isUnique,
                'metadata' => [
                    'contextKeywords' => $this->extractKeywordsFromContext($articleContext, $keywords),
                    'timestamp' => time()
                ]
            ];
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao obter conjunto de humanização: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function markHumanizationSetAsUsed(
        ?HumanPersona $persona = null,
        ?BrazilianLocation $location = null,
        array $discussions = []
    ): bool {
        try {
            DB::beginTransaction();
            
            // Marca a persona como utilizada
            if ($persona !== null && $persona->id !== null) {
                $this->personaRepository->markAsUsed($persona);
            }
            
            // Marca a localização como utilizada
            if ($location !== null && $location->id !== null) {
                $this->locationRepository->markAsUsed($location);
            }
            
            // Marca as discussões como utilizadas
            foreach ($discussions as $discussion) {
                if ($discussion->id !== null) {
                    $this->discussionRepository->markAsUsed($discussion);
                }
            }
            
            // Registra a combinação persona-localização como recentemente utilizada
            if ($persona !== null && $location !== null && $persona->id !== null && $location->id !== null) {
                $this->registerPersonaLocationCombination($persona->id, $location->id);
            }
            
            DB::commit();
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new DomainException(
                "Erro ao marcar conjunto de humanização como utilizado: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function getLeastUsedElements(
        int $personaLimit = 5,
        int $locationLimit = 5,
        int $discussionLimit = 5
    ): array {
        try {
            // Obtém elementos menos utilizados de cada repositório
            $personas = $this->personaRepository->getLeastUsedPersonas($personaLimit);
            $locations = $this->locationRepository->getLeastUsedLocations($locationLimit);
            
            // Para discussões, podemos implementar uma lógica semelhante obtendo várias discussões
            // e ordenando por uso no código (discussionRepository não tem um método específico)
            $allDiscussions = $this->discussionRepository->findAll(1, $discussionLimit * 3);
            
            // Ordena as discussões por contagem de uso (ascendente)
            usort($allDiscussions, function (ForumDiscussion $a, ForumDiscussion $b) {
                return $a->getUsageCount() <=> $b->getUsageCount();
            });
            
            // Limita ao número solicitado
            $discussions = array_slice($allDiscussions, 0, $discussionLimit);
            
            return [
                'personas' => $personas,
                'locations' => $locations,
                'discussions' => $discussions
            ];
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao obter elementos menos utilizados: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function isCombinationRecentlyUsed(
        HumanPersona $persona,
        BrazilianLocation $location,
        int $recentDays = 30
    ): bool {
        // Verifica se os IDs são válidos
        if ($persona->id === null || $location->id === null) {
            return false;
        }
        
        // Verifica usando o método interno que usa o cache
        return $this->isPersonaLocationCombinationRecent($persona->id, $location->id);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getUsageStatistics(): array
    {
        try {
            $cacheKey = self::CACHE_PREFIX . 'usage_stats';
            
            return Cache::remember(
                $cacheKey,
                self::CACHE_TTL,
                function () {
                    // Obtém estatísticas de cada repositório
                    $personaStats = $this->personaRepository->getUsageStatistics();
                    $locationStats = $this->locationRepository->getUsageStatistics();
                    
                    // Cálculo personalizado para discussões (repositório não tem um método específico)
                    $discussionCount = $this->discussionRepository->count();
                    
                    // Constrói estatísticas agregadas
                    return [
                        'totalPersonas' => $personaStats['totalPersonas'] ?? 0,
                        'totalLocations' => $locationStats['totalLocations'] ?? 0,
                        'totalDiscussions' => $discussionCount,
                        'personaStats' => $personaStats,
                        'locationStats' => $locationStats,
                        'recentCombinations' => [
                            'personaLocation' => count($this->recentCombinations['persona_location'] ?? [])
                        ],
                        'timestamp' => time()
                    ];
                }
            );
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao obter estatísticas de uso: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function distributePersonasForArticles(int $articleCount, array $contexts): array
    {
        try {
            // Obtém personas menos utilizadas
            $leastUsedPersonas = $this->personaRepository->getLeastUsedPersonas($articleCount * 2);
            
            // Se não houver personas suficientes, obtém algumas aleatórias
            if (count($leastUsedPersonas) < $articleCount) {
                // Em uma implementação real, adicionaríamos mais personas
                // Neste exemplo, apenas repetimos as que temos
                while (count($leastUsedPersonas) < $articleCount) {
                    foreach ($this->personaRepository->getLeastUsedPersonas(5) as $persona) {
                        if (count($leastUsedPersonas) < $articleCount) {
                            $leastUsedPersonas[] = $persona;
                        } else {
                            break;
                        }
                    }
                }
            }
            
            // Distribuição inicial - uma persona por artigo
            $distribution = [];
            for ($i = 0; $i < $articleCount; $i++) {
                $personaIndex = $i % count($leastUsedPersonas);
                $distribution[$i] = $leastUsedPersonas[$personaIndex];
            }
            
            // Tenta melhorar a distribuição usando o contexto se disponível
            if (!empty($contexts)) {
                foreach ($contexts as $index => $context) {
                    if (isset($distribution[$index])) {
                        // Extrai palavras-chave do contexto
                        $keywords = $this->extractKeywordsFromContext($context);
                        
                        // Obtém uma persona compatível com o contexto
                        $compatiblePersona = $this->getRandomCompatiblePersona($context, $keywords);
                        
                        // Substitui se encontrou uma persona compatível
                        if ($compatiblePersona !== null) {
                            $distribution[$index] = $compatiblePersona;
                        }
                    }
                }
            }
            
            return $distribution;
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao distribuir personas para artigos: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function distributeLocationsForArticles(int $articleCount, array $contexts): array
    {
        try {
            // Obtém localizações menos utilizadas
            $leastUsedLocations = $this->locationRepository->getLeastUsedLocations($articleCount * 2);
            
            // Se não houver localizações suficientes, obtém algumas aleatórias
            if (count($leastUsedLocations) < $articleCount) {
                // Em uma implementação real, adicionaríamos mais localizações
                // Neste exemplo, apenas repetimos as que temos
                while (count($leastUsedLocations) < $articleCount) {
                    foreach ($this->locationRepository->getLeastUsedLocations(5) as $location) {
                        if (count($leastUsedLocations) < $articleCount) {
                            $leastUsedLocations[] = $location;
                        } else {
                            break;
                        }
                    }
                }
            }
            
            // Distribuição inicial - uma localização por artigo
            $distribution = [];
            for ($i = 0; $i < $articleCount; $i++) {
                $locationIndex = $i % count($leastUsedLocations);
                $distribution[$i] = $leastUsedLocations[$locationIndex];
            }
            
            // Tenta melhorar a distribuição usando o contexto se disponível
            if (!empty($contexts)) {
                foreach ($contexts as $index => $context) {
                    if (isset($distribution[$index])) {
                        // Obtém uma localização compatível com o contexto
                        $compatibleLocation = $this->getRandomCompatibleLocation($context);
                        
                        // Substitui se encontrou uma localização compatível
                        if ($compatibleLocation !== null) {
                            $distribution[$index] = $compatibleLocation;
                        }
                    }
                }
            }
            
            return $distribution;
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao distribuir localizações para artigos: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function getComplementaryElements(
        ?HumanPersona $persona = null,
        ?BrazilianLocation $location = null,
        ?VehicleReference $vehicle = null
    ): array {
        try {
            $result = [
                'persona' => $persona,
                'location' => $location,
                'discussions' => [],
                'isComplete' => false
            ];
            
            // Se não tiver persona, obtém uma aleatória
            if ($result['persona'] === null) {
                $result['persona'] = $this->personaRepository->getRandomUnused();
            }
            
            // Se não tiver localização, obtém uma compatível com a persona
            if ($result['location'] === null && $result['persona'] !== null) {
                $result['location'] = $this->getRandomCompatibleLocation('', $result['persona']);
            }
            
            // Obtém discussões complementares
            if ($vehicle !== null) {
                // Se tiver veículo, obtém discussões relacionadas
                $result['discussions'] = $this->discussionRepository->filterByVehicle($vehicle, 1, 3);
            } else {
                // Caso contrário, obtém discussões recentes
                $result['discussions'] = $this->discussionRepository->getRecent(90, 1, 3);
            }
            
            // Verifica se o conjunto está completo
            $result['isComplete'] = $result['persona'] !== null && $result['location'] !== null && !empty($result['discussions']);
            
            return $result;
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao obter elementos complementares: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function searchElementsByKeywords(
        array $keywords,
        bool $includePersonas = true,
        bool $includeLocations = true,
        bool $includeDiscussions = true
    ): array {
        try {
            $result = [
                'personas' => [],
                'locations' => [],
                'discussions' => []
            ];
            
            // Se não houver palavras-chave, retorna vazio
            if (empty($keywords)) {
                return $result;
            }
            
            // Processa palavras-chave para busca
            $processedKeywords = array_filter($keywords, function ($keyword) {
                return strlen(trim($keyword)) >= 3;
            });
            
            // Busca personas com base nas palavras-chave
            if ($includePersonas) {
                foreach ($processedKeywords as $keyword) {
                    // Busca por nome
                    $personasByName = $this->personaRepository->searchByName($keyword);
                    
                    // Busca por profissão
                    $personasByProfession = $this->personaRepository->findByProfession($keyword);
                    
                    // Busca por localização
                    $personasByLocation = $this->personaRepository->findByLocation($keyword);
                    
                    // Combina resultados e remove duplicatas
                    $personas = array_merge($personasByName, $personasByProfession, $personasByLocation);
                    $uniquePersonas = [];
                    
                    foreach ($personas as $persona) {
                        if ($persona->id !== null && !isset($uniquePersonas[$persona->id])) {
                            $uniquePersonas[$persona->id] = $persona;
                        }
                    }
                    
                    $result['personas'] = array_merge($result['personas'], array_values($uniquePersonas));
                }
            }
            
            // Busca localizações com base nas palavras-chave
            if ($includeLocations) {
                foreach ($processedKeywords as $keyword) {
                    // Busca por texto
                    $locationsByKeyword = $this->locationRepository->search($keyword);
                    
                    // Remove duplicatas
                    $uniqueLocations = [];
                    
                    foreach ($locationsByKeyword as $location) {
                        if ($location->id !== null && !isset($uniqueLocations[$location->id])) {
                            $uniqueLocations[$location->id] = $location;
                        }
                    }
                    
                    $result['locations'] = array_merge($result['locations'], array_values($uniqueLocations));
                }
            }
            
            // Busca discussões com base nas palavras-chave
            if ($includeDiscussions) {
                // Usa a API de busca por palavras-chave múltiplas
                $discussions = $this->discussionRepository->findByKeywords($processedKeywords);
                
                // Remove duplicatas
                $uniqueDiscussions = [];
                
                foreach ($discussions as $discussion) {
                    if ($discussion->id !== null && !isset($uniqueDiscussions[$discussion->id])) {
                        $uniqueDiscussions[$discussion->id] = $discussion;
                    }
                }
                
                $result['discussions'] = array_values($uniqueDiscussions);
            }
            
            return $result;
        } catch (\Exception $e) {
            throw new DomainException(
                "Erro ao buscar elementos por palavras-chave: {$e->getMessage()}",
                'REPOSITORY_ERROR',
                ['original_exception' => get_class($e)]
            );
        }
    }
}