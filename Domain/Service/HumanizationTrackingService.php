<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Service;

use DateTimeImmutable;
use Src\ArticleGenerator\Domain\Entity\HumanPersona;
use Src\ArticleGenerator\Domain\Entity\BrazilianLocation;
use Src\ArticleGenerator\Domain\Entity\ForumDiscussion;
use Src\ArticleGenerator\Domain\Repository\HumanPersonaRepositoryInterface;
use Src\ArticleGenerator\Domain\Repository\BrazilianLocationRepositoryInterface;
use Src\ArticleGenerator\Domain\Repository\ForumDiscussionRepositoryInterface;

/**
 * Serviço de domínio para rastreamento de humanização
 */
class HumanizationTrackingService implements HumanizationTrackingServiceInterface
{
    /**
     * @var array<array<string, mixed>> $usageLog Registro de uso de elementos
     */
    private array $usageLog = [];

    /**
     * @param HumanPersonaRepositoryInterface $personaRepository Repositório de personas
     * @param BrazilianLocationRepositoryInterface $locationRepository Repositório de localizações
     * @param ForumDiscussionRepositoryInterface $discussionRepository Repositório de discussões
     */
    public function __construct(
        private readonly HumanPersonaRepositoryInterface $personaRepository,
        private readonly BrazilianLocationRepositoryInterface $locationRepository,
        private readonly ForumDiscussionRepositoryInterface $discussionRepository
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function registerComponentsUsage(
        HumanPersona $persona,
        BrazilianLocation $location,
        array $discussions
    ): bool {
        // Atualiza o contador de uso nas entidades
        $this->personaRepository->markAsUsed($persona);
        $this->locationRepository->markAsUsed($location);
        
        foreach ($discussions as $discussion) {
            $this->discussionRepository->markAsUsed($discussion);
        }
        
        // Registra o uso no log interno
        $usageEntry = [
            'timestamp' => (new DateTimeImmutable())->getTimestamp(),
            'date' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            'personaId' => $persona->id,
            'locationId' => $location->id,
            'discussionIds' => array_map(fn(ForumDiscussion $d) => $d->id, $discussions)
        ];
        
        $this->usageLog[] = $usageEntry;
        
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isCombinationRecentlyUsed(
        HumanPersona $persona,
        BrazilianLocation $location,
        int $recentDays = 30
    ): bool {
        if ($persona->id === null || $location->id === null) {
            return false;
        }
        
        $cutoffTimestamp = (new DateTimeImmutable())->modify("-{$recentDays} days")->getTimestamp();
        
        foreach ($this->usageLog as $entry) {
            // Verifica se o uso é recente
            if ($entry['timestamp'] >= $cutoffTimestamp) {
                // Verifica se a combinação específica foi utilizada
                if ($entry['personaId'] === $persona->id && $entry['locationId'] === $location->id) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getRecentUsageHistory(int $limit = 50): array
    {
        $personaIds = [];
        $locationIds = [];
        $discussionIds = [];
        
        // Ordena o log de uso por timestamp (mais recente primeiro)
        usort($this->usageLog, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);
        
        // Limita ao número de registros solicitado
        $recentLog = array_slice($this->usageLog, 0, $limit);
        
        // Extrai os IDs dos elementos utilizados recentemente
        foreach ($recentLog as $entry) {
            if (isset($entry['personaId'])) {
                $personaIds[] = $entry['personaId'];
            }
            
            if (isset($entry['locationId'])) {
                $locationIds[] = $entry['locationId'];
            }
            
            if (isset($entry['discussionIds']) && is_array($entry['discussionIds'])) {
                $discussionIds = array_merge($discussionIds, $entry['discussionIds']);
            }
        }
        
        // Remove IDs duplicados
        $personaIds = array_unique($personaIds);
        $locationIds = array_unique($locationIds);
        $discussionIds = array_unique($discussionIds);
        
        return [
            'persona' => $personaIds,
            'location' => $locationIds,
            'discussion' => $discussionIds
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getUsageStatistics(): array
    {
        $totalUsages = count($this->usageLog);
        
        // Contagem de uso por elemento
        $personaUsage = [];
        $locationUsage = [];
        $discussionUsage = [];
        
        foreach ($this->usageLog as $entry) {
            // Contabiliza uso de persona
            if (isset($entry['personaId'])) {
                $personaId = $entry['personaId'];
                $personaUsage[$personaId] = ($personaUsage[$personaId] ?? 0) + 1;
            }
            
            // Contabiliza uso de localização
            if (isset($entry['locationId'])) {
                $locationId = $entry['locationId'];
                $locationUsage[$locationId] = ($locationUsage[$locationId] ?? 0) + 1;
            }
            
            // Contabiliza uso de discussões
            if (isset($entry['discussionIds']) && is_array($entry['discussionIds'])) {
                foreach ($entry['discussionIds'] as $discussionId) {
                    $discussionUsage[$discussionId] = ($discussionUsage[$discussionId] ?? 0) + 1;
                }
            }
        }
        
        // Cálculo da distribuição de uso
        $personaCount = count($personaUsage);
        $locationCount = count($locationUsage);
        $discussionCount = count($discussionUsage);
        
        $personaMostUsed = !empty($personaUsage) ? max($personaUsage) : 0;
        $locationMostUsed = !empty($locationUsage) ? max($locationUsage) : 0;
        $discussionMostUsed = !empty($discussionUsage) ? max($discussionUsage) : 0;
        
        // Médias de uso
        $personaAvgUsage = $personaCount > 0 ? array_sum($personaUsage) / $personaCount : 0;
        $locationAvgUsage = $locationCount > 0 ? array_sum($locationUsage) / $locationCount : 0;
        $discussionAvgUsage = $discussionCount > 0 ? array_sum($discussionUsage) / $discussionCount : 0;
        
        return [
            'totalUsages' => $totalUsages,
            'uniqueElements' => [
                'persona' => $personaCount,
                'location' => $locationCount,
                'discussion' => $discussionCount
            ],
            'maxUsage' => [
                'persona' => $personaMostUsed,
                'location' => $locationMostUsed,
                'discussion' => $discussionMostUsed
            ],
            'avgUsage' => [
                'persona' => $personaAvgUsage,
                'location' => $locationAvgUsage,
                'discussion' => $discussionAvgUsage
            ],
            'lastUsed' => !empty($this->usageLog) ? $this->usageLog[0]['date'] : null
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getMostUsedElements(int $limit = 10): array
    {
        // Contagem de uso por elemento
        $personaUsage = [];
        $locationUsage = [];
        $discussionUsage = [];
        
        foreach ($this->usageLog as $entry) {
            // Contabiliza uso de persona
            if (isset($entry['personaId'])) {
                $personaId = $entry['personaId'];
                $personaUsage[$personaId] = ($personaUsage[$personaId] ?? 0) + 1;
            }
            
            // Contabiliza uso de localização
            if (isset($entry['locationId'])) {
                $locationId = $entry['locationId'];
                $locationUsage[$locationId] = ($locationUsage[$locationId] ?? 0) + 1;
            }
            
            // Contabiliza uso de discussões
            if (isset($entry['discussionIds']) && is_array($entry['discussionIds'])) {
                foreach ($entry['discussionIds'] as $discussionId) {
                    $discussionUsage[$discussionId] = ($discussionUsage[$discussionId] ?? 0) + 1;
                }
            }
        }
        
        // Ordena por número de usos (decrescente)
        arsort($personaUsage);
        arsort($locationUsage);
        arsort($discussionUsage);
        
        // Limita ao número de elementos solicitado
        $personaUsage = array_slice($personaUsage, 0, $limit, true);
        $locationUsage = array_slice($locationUsage, 0, $limit, true);
        $discussionUsage = array_slice($discussionUsage, 0, $limit, true);
        
        // Prepara o resultado
        $result = [
            'persona' => [],
            'location' => [],
            'discussion' => []
        ];
        
        // Obtém informações sobre as personas mais utilizadas
        foreach ($personaUsage as $id => $count) {
            $persona = $this->personaRepository->findById($id);
            if ($persona !== null) {
                $result['persona'][] = [
                    'id' => $id,
                    'name' => (string)$persona->getName(),
                    'count' => $count
                ];
            }
        }
        
        // Obtém informações sobre as localizações mais utilizadas
        foreach ($locationUsage as $id => $count) {
            $location = $this->locationRepository->findById($id);
            if ($location !== null) {
                $result['location'][] = [
                    'id' => $id,
                    'name' => $location->getFullLocationName(),
                    'count' => $count
                ];
            }
        }
        
        // Obtém informações sobre as discussões mais utilizadas
        foreach ($discussionUsage as $id => $count) {
            $discussion = $this->discussionRepository->findById($id);
            if ($discussion !== null) {
                $result['discussion'][] = [
                    'id' => $id,
                    'title' => $discussion->getTitle(),
                    'count' => $count
                ];
            }
        }
        
        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getLeastUsedElements(int $limit = 10): array
    {
        // Obtém elementos pouco utilizados diretamente dos repositórios
        $leastUsedPersonas = $this->personaRepository->getLeastUsedPersonas($limit);
        $leastUsedLocations = $this->locationRepository->getLeastUsedLocations($limit);
        
        // Prepara o resultado
        $result = [
            'persona' => [],
            'location' => [],
            'discussion' => []
        ];
        
        // Informações sobre personas menos utilizadas
        foreach ($leastUsedPersonas as $persona) {
            $result['persona'][] = [
                'id' => $persona->id,
                'name' => (string)$persona->getName(),
                'count' => $persona->getUsageCount()
            ];
        }
        
        // Informações sobre localizações menos utilizadas
        foreach ($leastUsedLocations as $location) {
            $result['location'][] = [
                'id' => $location->id,
                'name' => $location->getFullLocationName(),
                'count' => $location->getUsageCount()
            ];
        }
        
        // Para discussões, não temos um método direto no repositório (poderia ser implementado)
        // Por simplicidade, usaremos uma lógica similar à de getMostUsedElements, mas em ordem inversa
        
        // Contagem de uso por discussão
        $discussionUsage = [];
        
        foreach ($this->usageLog as $entry) {
            if (isset($entry['discussionIds']) && is_array($entry['discussionIds'])) {
                foreach ($entry['discussionIds'] as $discussionId) {
                    $discussionUsage[$discussionId] = ($discussionUsage[$discussionId] ?? 0) + 1;
                }
            }
        }
        
        // Ordena por número de usos (crescente)
        asort($discussionUsage);
        
        // Limita ao número de elementos solicitado
        $discussionUsage = array_slice($discussionUsage, 0, $limit, true);
        
        // Obtém informações sobre as discussões menos utilizadas
        foreach ($discussionUsage as $id => $count) {
            $discussion = $this->discussionRepository->findById($id);
            if ($discussion !== null) {
                $result['discussion'][] = [
                    'id' => $id,
                    'title' => $discussion->getTitle(),
                    'count' => $count
                ];
            }
        }
        
        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function analyzeUsagePatterns(int $timeframeDays = 90): array
    {
        $cutoffTimestamp = (new DateTimeImmutable())->modify("-{$timeframeDays} days")->getTimestamp();
        
        // Filtra log para incluir apenas entradas dentro do período especificado
        $recentLog = array_filter(
            $this->usageLog,
            fn($entry) => $entry['timestamp'] >= $cutoffTimestamp
        );
        
        // Análise de combinações mais frequentes
        $combinations = [];
        
        foreach ($recentLog as $entry) {
            if (isset($entry['personaId']) && isset($entry['locationId'])) {
                $key = "p{$entry['personaId']}_l{$entry['locationId']}";
                $combinations[$key] = ($combinations[$key] ?? 0) + 1;
            }
        }
        
        // Ordena combinações por frequência (decrescente)
        arsort($combinations);
        
        // Análise de padrões temporais (uso por mês/semana)
        $usageByMonth = [];
        $usageByWeek = [];
        
        foreach ($recentLog as $entry) {
            $date = new DateTimeImmutable('@' . $entry['timestamp']);
            $month = $date->format('Y-m');
            $week = $date->format('Y-W');
            
            $usageByMonth[$month] = ($usageByMonth[$month] ?? 0) + 1;
            $usageByWeek[$week] = ($usageByWeek[$week] ?? 0) + 1;
        }
        
        // Frequência de rotação de elementos
        $uniquePersonas = count(array_unique(array_column($recentLog, 'personaId')));
        $uniqueLocations = count(array_unique(array_column($recentLog, 'locationId')));
        
        $totalUsages = count($recentLog);
        
        $rotationMetrics = [
            'personaRotationRate' => $totalUsages > 0 ? $uniquePersonas / $totalUsages : 0,
            'locationRotationRate' => $totalUsages > 0 ? $uniqueLocations / $totalUsages : 0
        ];
        
        return [
            'timeframeDays' => $timeframeDays,
            'totalUsages' => $totalUsages,
            'rotationMetrics' => $rotationMetrics,
            'temporalPatterns' => [
                'byMonth' => $usageByMonth,
                'byWeek' => $usageByWeek
            ],
            'topCombinations' => array_slice($combinations, 0, 10, true)
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getFrequentCombinations(int $limit = 10): array
    {
        // Análise de combinações persona-localização
        $combinations = [];
        
        foreach ($this->usageLog as $entry) {
            if (isset($entry['personaId']) && isset($entry['locationId'])) {
                $key = "{$entry['personaId']}_{$entry['locationId']}";
                
                if (!isset($combinations[$key])) {
                    $combinations[$key] = [
                        'personaId' => $entry['personaId'],
                        'locationId' => $entry['locationId'],
                        'count' => 0,
                        'lastUsed' => $entry['timestamp']
                    ];
                }
                
                $combinations[$key]['count']++;
                
                // Atualiza a data do último uso se for mais recente
                if ($entry['timestamp'] > $combinations[$key]['lastUsed']) {
                    $combinations[$key]['lastUsed'] = $entry['timestamp'];
                }
            }
        }
        
        // Ordena por contagem (decrescente) e depois por último uso (mais recente primeiro)
        uasort($combinations, function($a, $b) {
            $countDiff = $b['count'] <=> $a['count'];
            
            if ($countDiff !== 0) {
                return $countDiff;
            }
            
            return $b['lastUsed'] <=> $a['lastUsed'];
        });
        
        // Limita ao número de combinações solicitado
        $topCombinations = array_slice($combinations, 0, $limit, true);
        
        // Enriquece os resultados com informações das entidades
        $result = [];
        
        foreach ($topCombinations as $data) {
            $persona = $this->personaRepository->findById($data['personaId']);
            $location = $this->locationRepository->findById($data['locationId']);
            
            if ($persona !== null && $location !== null) {
                $result[] = [
                    'personaId' => $data['personaId'],
                    'personaName' => (string)$persona->getName(),
                    'locationId' => $data['locationId'],
                    'locationName' => $location->getFullLocationName(),
                    'usageCount' => $data['count'],
                    'lastUsed' => (new DateTimeImmutable('@' . $data['lastUsed']))->format('Y-m-d H:i:s')
                ];
            }
        }
        
        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function cleanOldUsageHistory(int $olderThanDays = 180): int
    {
        $cutoffTimestamp = (new DateTimeImmutable())->modify("-{$olderThanDays} days")->getTimestamp();
        
        $originalCount = count($this->usageLog);
        
        // Filtra para manter apenas entradas mais recentes que o cutoff
        $this->usageLog = array_filter(
            $this->usageLog,
            fn($entry) => $entry['timestamp'] >= $cutoffTimestamp
        );
        
        // Recalcula índices do array
        $this->usageLog = array_values($this->usageLog);
        
        // Retorna o número de registros removidos
        return $originalCount - count($this->usageLog);
    }
}