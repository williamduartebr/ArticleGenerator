<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Service;

use Src\ArticleGenerator\Domain\Entity\HumanPersona;
use Src\ArticleGenerator\Domain\Entity\BrazilianLocation;
use Src\ArticleGenerator\Domain\Entity\ForumDiscussion;
use Src\ArticleGenerator\Domain\Repository\HumanPersonaRepositoryInterface;
use Src\ArticleGenerator\Domain\Repository\BrazilianLocationRepositoryInterface;
use Src\ArticleGenerator\Domain\Repository\ForumDiscussionRepositoryInterface;
use Src\ArticleGenerator\Domain\ValueObject\VehicleReference;

/**
 * Serviço de domínio para seleção não-repetitiva de elementos
 */
class NonRepetitiveSelectionService implements NonRepetitiveSelectionServiceInterface
{
    /**
     * @var array<string, array<string>> $recentSelections Histórico de seleções recentes por tipo de elemento
     */
    private array $recentSelections = [
        'persona' => [],
        'location' => [],
        'discussion' => []
    ];

    /**
     * @param HumanPersonaRepositoryInterface $personaRepository Repositório de personas
     * @param BrazilianLocationRepositoryInterface $locationRepository Repositório de localizações
     * @param ForumDiscussionRepositoryInterface $discussionRepository Repositório de discussões
     * @param ElementCompatibilityValidatorInterface $compatibilityValidator Validador de compatibilidade
     */
    public function __construct(
        private readonly HumanPersonaRepositoryInterface $personaRepository,
        private readonly BrazilianLocationRepositoryInterface $locationRepository,
        private readonly ForumDiscussionRepositoryInterface $discussionRepository,
        private readonly ElementCompatibilityValidatorInterface $compatibilityValidator
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function selectPersona(
        string $context,
        array $keywords = [],
        array $excludeIds = [],
        ?VehicleReference $vehicle = null
    ): ?HumanPersona {
        // Combinar IDs excluídos com seleções recentes
        $allExcludedIds = array_merge($excludeIds, $this->recentSelections['persona']);
        
        // Tenta obter uma persona compatível com o contexto e não utilizada recentemente
        $persona = $this->personaRepository->getRandomUnused(7);
        
        // Se não encontrou ou o ID está na lista de excluídos, tenta com base em veículo preferido
        if ($persona === null || in_array($persona->id, $allExcludedIds)) {
            if ($vehicle !== null) {
                $compatiblePersonas = $this->personaRepository->findByPreferredVehicle(
                    $vehicle->make,
                    $vehicle->model
                );
                
                // Filtra para remover personas excluídas
                $compatiblePersonas = array_filter(
                    $compatiblePersonas,
                    fn(HumanPersona $p) => !in_array($p->id, $allExcludedIds)
                );
                
                if (!empty($compatiblePersonas)) {
                    // Seleciona aleatoriamente uma persona compatível
                    $persona = $compatiblePersonas[array_rand($compatiblePersonas)];
                }
            }
        }
        
        // Se ainda não encontrou, tenta uma persona com uso mínimo
        if ($persona === null || in_array($persona->id, $allExcludedIds)) {
            $leastUsedPersonas = $this->personaRepository->getLeastUsedPersonas(10);
            
            // Filtra para remover personas excluídas
            $leastUsedPersonas = array_filter(
                $leastUsedPersonas,
                fn(HumanPersona $p) => !in_array($p->id, $allExcludedIds)
            );
            
            if (!empty($leastUsedPersonas)) {
                // Seleciona aleatoriamente uma persona menos utilizada
                $persona = $leastUsedPersonas[array_rand($leastUsedPersonas)];
            }
        }
        
        // Se encontrou uma persona, registra na lista de seleções recentes
        if ($persona !== null && $persona->id !== null) {
            $this->recentSelections['persona'][] = $persona->id;
            
            // Manter apenas as últimas 30 seleções
            if (count($this->recentSelections['persona']) > 30) {
                array_shift($this->recentSelections['persona']);
            }
        }
        
        return $persona;
    }

    /**
     * {@inheritDoc}
     */
    public function selectLocation(
        string $context,
        array $excludeIds = [],
        ?HumanPersona $persona = null
    ): ?BrazilianLocation {
        // Combinar IDs excluídos com seleções recentes
        $allExcludedIds = array_merge($excludeIds, $this->recentSelections['location']);
        
        // Tenta obter uma localização compatível com o contexto
        $location = $this->locationRepository->getRandomUnused(7);
        
        // Se não encontrou ou o ID está na lista de excluídos, tenta com base na persona
        if ($location === null || in_array($location->id, $allExcludedIds)) {
            if ($persona !== null && $persona->getLocation() !== null) {
                $personaLocation = $persona->getLocation();
                $compatibleLocations = $this->locationRepository->findByCity($personaLocation);
                
                // Filtra para remover localizações excluídas
                $compatibleLocations = array_filter(
                    $compatibleLocations,
                    fn(BrazilianLocation $l) => !in_array($l->id, $allExcludedIds)
                );
                
                if (!empty($compatibleLocations)) {
                    // Seleciona aleatoriamente uma localização compatível
                    $location = $compatibleLocations[array_rand($compatibleLocations)];
                }
            }
        }
        
        // Se ainda não encontrou, tenta uma localização com uso mínimo
        if ($location === null || in_array($location->id, $allExcludedIds)) {
            $leastUsedLocations = $this->locationRepository->getLeastUsedLocations(10);
            
            // Filtra para remover localizações excluídas
            $leastUsedLocations = array_filter(
                $leastUsedLocations,
                fn(BrazilianLocation $l) => !in_array($l->id, $allExcludedIds)
            );
            
            if (!empty($leastUsedLocations)) {
                // Seleciona aleatoriamente uma localização menos utilizada
                $location = $leastUsedLocations[array_rand($leastUsedLocations)];
            }
        }
        
        // Se encontrou uma localização, registra na lista de seleções recentes
        if ($location !== null && $location->id !== null) {
            $this->recentSelections['location'][] = $location->id;
            
            // Manter apenas as últimas 30 seleções
            if (count($this->recentSelections['location']) > 30) {
                array_shift($this->recentSelections['location']);
            }
        }
        
        return $location;
    }

    /**
     * {@inheritDoc}
     */
    public function selectDiscussion(
        string $context,
        array $keywords = [],
        array $excludeIds = [],
        ?VehicleReference $vehicle = null
    ): ?ForumDiscussion {
        // Combinar IDs excluídos com seleções recentes
        $allExcludedIds = array_merge($excludeIds, $this->recentSelections['discussion']);
        
        // Tenta obter discussões relevantes para o contexto
        $relevantDiscussions = $this->discussionRepository->getRelevantInsights($context, $keywords, 10);
        
        // Filtra para remover discussões excluídas
        $relevantDiscussions = array_filter(
            $relevantDiscussions,
            fn(ForumDiscussion $d) => !in_array($d->id, $allExcludedIds)
        );
        
        // Se encontrou discussões relevantes, seleciona aleatoriamente uma
        if (!empty($relevantDiscussions)) {
            $discussion = $relevantDiscussions[array_rand($relevantDiscussions)];
        } else {
            // Tenta com base no veículo
            if ($vehicle !== null) {
                $vehicleDiscussions = $this->discussionRepository->filterByVehicle($vehicle);
                
                // Filtra para remover discussões excluídas
                $vehicleDiscussions = array_filter(
                    $vehicleDiscussions,
                    fn(ForumDiscussion $d) => !in_array($d->id, $allExcludedIds)
                );
                
                if (!empty($vehicleDiscussions)) {
                    // Seleciona aleatoriamente uma discussão compatível
                    $discussion = $vehicleDiscussions[array_rand($vehicleDiscussions)];
                } else {
                    // Tenta uma discussão não utilizada recentemente
                    $discussion = $this->discussionRepository->getRandomUnused(30);
                    
                    // Verifica se a discussão não está na lista de excluídos
                    if ($discussion !== null && in_array($discussion->id, $allExcludedIds)) {
                        $discussion = null;
                    }
                }
            } else {
                // Tenta uma discussão não utilizada recentemente
                $discussion = $this->discussionRepository->getRandomUnused(30);
                
                // Verifica se a discussão não está na lista de excluídos
                if ($discussion !== null && in_array($discussion->id, $allExcludedIds)) {
                    $discussion = null;
                }
            }
        }
        
        // Se encontrou uma discussão, registra na lista de seleções recentes
        if ($discussion !== null && $discussion->id !== null) {
            $this->recentSelections['discussion'][] = $discussion->id;
            
            // Manter apenas as últimas 50 seleções
            if (count($this->recentSelections['discussion']) > 50) {
                array_shift($this->recentSelections['discussion']);
            }
        }
        
        return $discussion;
    }

    /**
     * {@inheritDoc}
     */
    public function selectMultipleDiscussions(
        string $context,
        array $keywords = [],
        int $count = 3,
        array $excludeIds = [],
        ?VehicleReference $vehicle = null
    ): array {
        $selectedDiscussions = [];
        $localExcludeIds = $excludeIds;
        
        // Tenta selecionar o número solicitado de discussões
        for ($i = 0; $i < $count; $i++) {
            $discussion = $this->selectDiscussion($context, $keywords, $localExcludeIds, $vehicle);
            
            if ($discussion !== null && $discussion->id !== null) {
                $selectedDiscussions[] = $discussion;
                $localExcludeIds[] = $discussion->id;
            }
        }
        
        return $selectedDiscussions;
    }

    /**
     * {@inheritDoc}
     */
    public function selectCompatibleElements(
        string $context,
        array $keywords = [],
        ?VehicleReference $vehicle = null,
        array $excludeIds = []
    ): array {
        // Obtém excludeIds para cada tipo de elemento
        $personaExcludeIds = $excludeIds['persona'] ?? [];
        $locationExcludeIds = $excludeIds['location'] ?? [];
        $discussionExcludeIds = $excludeIds['discussion'] ?? [];
        
        // Seleciona persona
        $persona = $this->selectPersona($context, $keywords, $personaExcludeIds, $vehicle);
        
        // Seleciona localização compatível com a persona
        $location = $this->selectLocation($context, $locationExcludeIds, $persona);
        
        // Seleciona discussões compatíveis
        $discussions = $this->selectMultipleDiscussions(
            $context, 
            $keywords, 
            3, 
            $discussionExcludeIds, 
            $vehicle
        );
        
        // Verifica compatibilidade entre os elementos selecionados
        if ($persona !== null && $location !== null && !empty($discussions)) {
            foreach ($discussions as $discussion) {
                if (!$this->compatibilityValidator->areElementsCompatible($persona, $location, $discussion)) {
                    // Se alguma discussão não for compatível, tenta selecionar outra
                    $newDiscussionExcludeIds = array_merge(
                        $discussionExcludeIds,
                        array_map(
                            fn(ForumDiscussion $d) => $d->id, 
                            $discussions
                        )
                    );
                    
                    return $this->selectCompatibleElements(
                        $context,
                        $keywords,
                        $vehicle,
                        [
                            'persona' => [$persona->id],
                            'location' => [$location->id],
                            'discussion' => $newDiscussionExcludeIds
                        ]
                    );
                }
            }
        }
        
        return [
            'persona' => $persona,
            'location' => $location,
            'discussions' => $discussions
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getRecentSelectionHistory(int $limit = 50): array
    {
        $history = [];
        
        foreach ($this->recentSelections as $type => $ids) {
            $history[$type] = array_slice($ids, -$limit);
        }
        
        return $history;
    }

    /**
     * {@inheritDoc}
     */
    public function clearSelectionHistory(string $elementType, int $olderThanDays = 30): bool
    {
        if (!isset($this->recentSelections[$elementType])) {
            return false;
        }
        
        // Em uma implementação real, isso removeria registros mais antigos que o número de dias especificado
        // Para este exemplo, simplesmente limpa o histórico
        $this->recentSelections[$elementType] = [];
        
        return true;
    }
}