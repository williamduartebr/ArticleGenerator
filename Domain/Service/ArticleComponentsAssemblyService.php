<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Service;

use Src\ArticleGenerator\Domain\Entity\HumanPersona;
use Src\ArticleGenerator\Domain\Entity\BrazilianLocation;
use Src\ArticleGenerator\Domain\Entity\ForumDiscussion;
use Src\ArticleGenerator\Domain\ValueObject\VehicleReference;

/**
 * Serviço de domínio para montagem de componentes de artigo
 */
class ArticleComponentsAssemblyService implements ArticleComponentsAssemblyServiceInterface
{
    /**
     * @param NonRepetitiveSelectionServiceInterface $selectionService Serviço de seleção não-repetitiva
     * @param ElementCompatibilityValidatorInterface $compatibilityValidator Validador de compatibilidade
     * @param HumanizationTrackingServiceInterface $trackingService Serviço de rastreamento de humanização
     */
    public function __construct(
        private readonly NonRepetitiveSelectionServiceInterface $selectionService,
        private readonly ElementCompatibilityValidatorInterface $compatibilityValidator,
        private readonly HumanizationTrackingServiceInterface $trackingService
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function assembleArticleComponents(
        string $articleContext,
        array $keywords = [],
        ?VehicleReference $vehicle = null
    ): array {
        // Verifica o histórico recente para obter IDs a serem excluídos
        $recentHistory = $this->trackingService->getRecentUsageHistory(20);
        
        $excludeIds = [
            'persona' => $recentHistory['persona'] ?? [],
            'location' => $recentHistory['location'] ?? [],
            'discussion' => $recentHistory['discussion'] ?? []
        ];
        
        // Seleciona persona
        $persona = $this->selectPersonaForArticle($articleContext, $keywords, $vehicle);
        
        // Seleciona localização compatível com a persona
        $location = $this->selectLocationForArticle($articleContext, $persona);
        
        // Seleciona discussões relevantes
        $discussions = $this->selectDiscussionsForArticle(
            $articleContext, 
            $keywords, 
            $vehicle
        );
        
        // Verifica a coerência dos componentes
        $isCoherent = $this->checkComponentsCoherence($persona, $location, $discussions, $articleContext);
        
        $components = [
            'persona' => $persona,
            'location' => $location,
            'discussions' => $discussions,
            'isCoherent' => $isCoherent
        ];
        
        // Se os componentes não são coerentes, tenta substituí-los
        if (!$isCoherent) {
            $components = $this->replaceIncoherentComponents($components, $articleContext, $keywords);
        }
        
        // Registra o uso dos componentes selecionados
        if ($persona !== null && $location !== null) {
            $this->trackingService->registerComponentsUsage($persona, $location, $discussions);
        }
        
        return $components;
    }

    /**
     * {@inheritDoc}
     */
    public function assembleBatchComponents(
        array $articleContexts,
        array $keywordsByArticle = [],
        array $vehiclesByArticle = []
    ): array {
        $batchComponents = [];
        
        // Mantém uma lista de IDs utilizados para evitar repetições no lote
        $usedIds = [
            'persona' => [],
            'location' => [],
            'discussion' => []
        ];
        
        foreach ($articleContexts as $index => $context) {
            // Obtém palavras-chave para o artigo atual
            $keywords = $keywordsByArticle[$index] ?? [];
            
            // Obtém veículo para o artigo atual
            $vehicle = $vehiclesByArticle[$index] ?? null;
            
            // Monta componentes para o artigo atual, excluindo IDs já utilizados no lote
            $components = $this->assembleArticleComponents($context, $keywords, $vehicle);
            
            // Adiciona os componentes ao resultado do lote
            $batchComponents[$index] = $components;
            
            // Atualiza a lista de IDs utilizados
            if ($components['persona'] !== null) {
                $usedIds['persona'][] = $components['persona']->id;
            }
            
            if ($components['location'] !== null) {
                $usedIds['location'][] = $components['location']->id;
            }
            
            foreach ($components['discussions'] as $discussion) {
                $usedIds['discussion'][] = $discussion->id;
            }
        }
        
        return $batchComponents;
    }

    /**
     * {@inheritDoc}
     */
    public function selectPersonaForArticle(
        string $articleContext,
        array $keywords = [],
        ?VehicleReference $vehicle = null
    ): ?HumanPersona {
        // Delega a seleção para o serviço de seleção não-repetitiva
        return $this->selectionService->selectPersona($articleContext, $keywords, [], $vehicle);
    }

    /**
     * {@inheritDoc}
     */
    public function selectLocationForArticle(
        string $articleContext,
        ?HumanPersona $persona = null
    ): ?BrazilianLocation {
        // Delega a seleção para o serviço de seleção não-repetitiva
        return $this->selectionService->selectLocation($articleContext, [], $persona);
    }

    /**
     * {@inheritDoc}
     */
    public function selectDiscussionsForArticle(
        string $articleContext,
        array $keywords = [],
        ?VehicleReference $vehicle = null,
        int $count = 3
    ): array {
        // Delega a seleção para o serviço de seleção não-repetitiva
        return $this->selectionService->selectMultipleDiscussions(
            $articleContext, 
            $keywords, 
            $count, 
            [], 
            $vehicle
        );
    }

    /**
     * {@inheritDoc}
     */
    public function checkComponentsCoherence(
        ?HumanPersona $persona,
        ?BrazilianLocation $location,
        array $discussions,
        string $articleContext
    ): bool {
        // Se algum componente essencial estiver faltando, não é coerente
        if ($persona === null || $location === null || empty($discussions)) {
            return false;
        }
        
        // Verifica compatibilidade entre persona e localização
        $personaLocationCoherent = $this->compatibilityValidator->areElementsCompatible(
            $persona,
            $location,
            null
        );
        
        if (!$personaLocationCoherent) {
            return false;
        }
        
        // Verifica compatibilidade com cada discussão
        foreach ($discussions as $discussion) {
            $discussionCoherent = $this->compatibilityValidator->areElementsCompatible(
                $persona,
                $location,
                $discussion
            );
            
            if (!$discussionCoherent) {
                return false;
            }
        }
        
        // Verifica se a combinação já foi usada recentemente
        $recentlyUsed = $this->trackingService->isCombinationRecentlyUsed($persona, $location, 30);
        
        if ($recentlyUsed) {
            return false;
        }
        
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function replaceIncoherentComponents(
        array $components,
        string $articleContext,
        array $keywords = []
    ): array {
        $persona = $components['persona'];
        $location = $components['location'];
        $discussions = $components['discussions'];
        
        // Identifica qual componente está causando a incoerência
        // Prioridade: manter persona > manter localização > manter discussões
        
        // Tenta substituir discussões primeiro
        $newDiscussions = $this->selectDiscussionsForArticle(
            $articleContext, 
            $keywords, 
            null, 
            3
        );
        
        if ($this->checkComponentsCoherence($persona, $location, $newDiscussions, $articleContext)) {
            return [
                'persona' => $persona,
                'location' => $location,
                'discussions' => $newDiscussions,
                'isCoherent' => true
            ];
        }
        
        // Tenta substituir localização mantendo persona
        $newLocation = $this->selectLocationForArticle($articleContext, $persona);
        
        if ($this->checkComponentsCoherence($persona, $newLocation, $discussions, $articleContext)) {
            return [
                'persona' => $persona,
                'location' => $newLocation,
                'discussions' => $discussions,
                'isCoherent' => true
            ];
        }
        
        // Tenta substituir localização e discussões mantendo persona
        if ($this->checkComponentsCoherence($persona, $newLocation, $newDiscussions, $articleContext)) {
            return [
                'persona' => $persona,
                'location' => $newLocation,
                'discussions' => $newDiscussions,
                'isCoherent' => true
            ];
        }
        
        // Em último caso, substitui todos os componentes
        $vehicle = null; // Aqui poderíamos extrair o veículo do contexto
        $newPersona = $this->selectPersonaForArticle($articleContext, $keywords, $vehicle);
        $newLocation = $this->selectLocationForArticle($articleContext, $newPersona);
        $newDiscussions = $this->selectDiscussionsForArticle(
            $articleContext, 
            $keywords, 
            $vehicle
        );
        
        return [
            'persona' => $newPersona,
            'location' => $newLocation,
            'discussions' => $newDiscussions,
            'isCoherent' => $this->checkComponentsCoherence(
                $newPersona, 
                $newLocation, 
                $newDiscussions, 
                $articleContext
            )
        ];
    }
}