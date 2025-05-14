<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Service;

use Src\ArticleGenerator\Domain\ValueObject\VehicleReference;

/**
 * Serviço de domínio para orquestração de geração de artigos
 */
class ArticleGenerationOrchestrator implements ArticleGenerationOrchestratorInterface
{
    /**
     * @var array<string, array<string, mixed>> $generationLog Registro de gerações de artigos
     */
    private array $generationLog = [];

    /**
     * @param ArticleComponentsAssemblyServiceInterface $componentsAssemblyService Serviço de montagem de componentes
     * @param ElementCompatibilityValidatorInterface $compatibilityValidator Validador de compatibilidade
     * @param HumanizationTrackingServiceInterface $trackingService Serviço de rastreamento de humanização
     */
    public function __construct(
        private readonly ArticleComponentsAssemblyServiceInterface $componentsAssemblyService,
        private readonly ElementCompatibilityValidatorInterface $compatibilityValidator,
        private readonly HumanizationTrackingServiceInterface $trackingService
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function prepareHumanizationSet(
        string $context,
        array $keywords = [],
        ?VehicleReference $vehicle = null
    ): array {
        // Delega a montagem dos componentes para o serviço de montagem
        $components = $this->componentsAssemblyService->assembleArticleComponents(
            $context,
            $keywords,
            $vehicle
        );
        
        // Extrai os elementos do resultado
        $persona = $components['persona'] ?? null;
        $location = $components['location'] ?? null;
        $discussions = $components['discussions'] ?? [];
        $isCoherent = $components['isCoherent'] ?? false;
        
        // Estrutura o conjunto de humanização
        $humanizationSet = [
            'persona' => $persona,
            'location' => $location,
            'discussions' => $discussions,
            'isCoherent' => $isCoherent,
            'metadata' => [
                'context' => $context,
                'keywords' => $keywords,
                'vehicle' => $vehicle,
                'timestamp' => time()
            ]
        ];
        
        return $humanizationSet;
    }

    /**
     * {@inheritDoc}
     */
    public function prepareMultipleHumanizationSets(
        array $contexts,
        array $keywordsByArticle = [],
        array $vehiclesByArticle = []
    ): array {
        $humanizationSets = [];
        
        foreach ($contexts as $index => $context) {
            $keywords = $keywordsByArticle[$index] ?? [];
            $vehicle = $vehiclesByArticle[$index] ?? null;
            
            $humanizationSets[$index] = $this->prepareHumanizationSet($context, $keywords, $vehicle);
        }
        
        return $humanizationSets;
    }

    /**
     * {@inheritDoc}
     */
    public function orchestrateArticleGeneration(
        string $context,
        array $keywords = [],
        ?VehicleReference $vehicle = null,
        array $options = []
    ): array {
        // Prepara o conjunto de humanização
        $humanizationSet = $this->prepareHumanizationSet($context, $keywords, $vehicle);
        
        // Valida o conjunto de humanização
        $validationResult = $this->validateHumanizationSet($humanizationSet, $context);
        
        // Se não for válido e a opção de retentar estiver ativada, tenta novamente
        if (!$validationResult['isValid'] && ($options['retryOnInvalid'] ?? true)) {
            // Tenta novamente com um conjunto diferente de elementos
            $humanizationSet = $this->prepareHumanizationSet($context, $keywords, $vehicle);
            $validationResult = $this->validateHumanizationSet($humanizationSet, $context);
        }
        
        // Gera um ID único para o artigo
        $articleId = uniqid('article_', true);
        
        // Registra o uso do conjunto de humanização
        $this->registerHumanizationSetUsage($humanizationSet, $articleId);
        
        // Registra a geração do artigo no log interno
        $this->generationLog[$articleId] = [
            'timestamp' => time(),
            'context' => $context,
            'keywords' => $keywords,
            'vehicleReference' => $vehicle ? (string)$vehicle : null,
            'humanizationSet' => [
                'personaId' => $humanizationSet['persona']->id ?? null,
                'locationId' => $humanizationSet['location']->id ?? null,
                'discussionIds' => array_map(
                    fn($d) => $d->id, 
                    $humanizationSet['discussions']
                )
            ],
            'validationResult' => $validationResult
        ];
        
        // Retorna o resultado completo
        return [
            'articleId' => $articleId,
            'humanizationSet' => $humanizationSet,
            'validationResult' => $validationResult,
            'timestamp' => time()
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function orchestrateBatchArticleGeneration(
        array $contexts,
        array $keywordsByArticle = [],
        array $vehiclesByArticle = [],
        array $globalOptions = []
    ): array {
        $results = [];
        
        // Processa cada artigo individualmente
        foreach ($contexts as $index => $context) {
            $keywords = $keywordsByArticle[$index] ?? [];
            $vehicle = $vehiclesByArticle[$index] ?? null;
            
            // Mescla opções globais com opções específicas (se houver)
            $options = $globalOptions;
            
            // Gera o artigo
            $results[$index] = $this->orchestrateArticleGeneration(
                $context,
                $keywords,
                $vehicle,
                $options
            );
        }
        
        // Aqui poderia ser implementada uma lógica para garantir diversidade entre artigos do lote
        
        return $results;
    }

    /**
     * {@inheritDoc}
     */
    public function validateHumanizationSet(
        array $humanizationSet,
        string $context
    ): array {
        $persona = $humanizationSet['persona'] ?? null;
        $location = $humanizationSet['location'] ?? null;
        $discussions = $humanizationSet['discussions'] ?? [];
        
        $issues = [];
        
        // Verifica se há elementos faltando
        if ($persona === null) {
            $issues[] = 'Persona ausente no conjunto de humanização';
        }
        
        if ($location === null) {
            $issues[] = 'Localização ausente no conjunto de humanização';
        }
        
        if (empty($discussions)) {
            $issues[] = 'Discussões ausentes no conjunto de humanização';
        }
        
        // Verifica compatibilidade entre elementos
        if ($persona !== null && $location !== null) {
            $personaLocationCompatible = $this->compatibilityValidator->areElementsCompatible(
                $persona,
                $location,
                null
            );
            
            if (!$personaLocationCompatible) {
                $issues[] = "Persona '{$persona->getName()}' não é compatível com a localização '{$location->getFullLocationName()}'";
            }
        }
        
        // Verifica compatibilidade com discussões
        if ($persona !== null && $location !== null && !empty($discussions)) {
            foreach ($discussions as $index => $discussion) {
                $discussionCompatible = $this->compatibilityValidator->areElementsCompatible(
                    $persona,
                    $location,
                    $discussion
                );
                
                if (!$discussionCompatible) {
                    $issues[] = "Discussão '{$discussion->getTitle()}' não é compatível com o conjunto persona-localização";
                }
            }
        }
        
        // Verifica se a combinação já foi usada recentemente
        if ($persona !== null && $location !== null) {
            $recentlyUsed = $this->trackingService->isCombinationRecentlyUsed($persona, $location, 30);
            
            if ($recentlyUsed) {
                $issues[] = "A combinação persona-localização foi utilizada recentemente";
            }
        }
        
        // Verifica adequação ao contexto
        $contextCompatibility = $this->evaluateContextCompatibility($humanizationSet, $context);
        
        if ($contextCompatibility < 0.7) {
            $issues[] = "O conjunto de humanização tem baixa compatibilidade com o contexto do artigo (score: {$contextCompatibility})";
        }
        
        // Resultado da validação
        $isValid = empty($issues);
        
        return [
            'isValid' => $isValid,
            'issues' => $issues,
            'contextCompatibility' => $contextCompatibility
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function registerHumanizationSetUsage(
        array $humanizationSet,
        ?string $articleId = null
    ): bool {
        $persona = $humanizationSet['persona'] ?? null;
        $location = $humanizationSet['location'] ?? null;
        $discussions = $humanizationSet['discussions'] ?? [];
        
        // Verifica se há elementos para registrar
        if ($persona === null || $location === null || empty($discussions)) {
            return false;
        }
        
        // Delega o registro para o serviço de rastreamento
        return $this->trackingService->registerComponentsUsage($persona, $location, $discussions);
    }

    /**
     * {@inheritDoc}
     */
    public function getGenerationStatistics(int $timeframeDays = 90): array
    {
        $cutoffTimestamp = time() - ($timeframeDays * 24 * 60 * 60);
        
        // Filtra log para incluir apenas entradas dentro do período especificado
        $recentLog = array_filter(
            $this->generationLog,
            fn($entry) => $entry['timestamp'] >= $cutoffTimestamp
        );
        
        $totalArticles = count($recentLog);
        $validArticles = 0;
        $personaUsage = [];
        $locationUsage = [];
        $discussionUsage = [];
        
        foreach ($recentLog as $entry) {
            // Contabiliza artigos válidos
            if ($entry['validationResult']['isValid'] ?? false) {
                $validArticles++;
            }
            
            // Contabiliza uso de personas
            if (isset($entry['humanizationSet']['personaId'])) {
                $personaId = $entry['humanizationSet']['personaId'];
                $personaUsage[$personaId] = ($personaUsage[$personaId] ?? 0) + 1;
            }
            
            // Contabiliza uso de localizações
            if (isset($entry['humanizationSet']['locationId'])) {
                $locationId = $entry['humanizationSet']['locationId'];
                $locationUsage[$locationId] = ($locationUsage[$locationId] ?? 0) + 1;
            }
            
            // Contabiliza uso de discussões
            if (isset($entry['humanizationSet']['discussionIds']) && is_array($entry['humanizationSet']['discussionIds'])) {
                foreach ($entry['humanizationSet']['discussionIds'] as $discussionId) {
                    $discussionUsage[$discussionId] = ($discussionUsage[$discussionId] ?? 0) + 1;
                }
            }
        }
        
        // Calcula métricas de diversidade
        $uniquePersonas = count($personaUsage);
        $uniqueLocations = count($locationUsage);
        $uniqueDiscussions = count($discussionUsage);
        
        $personaDiversity = $totalArticles > 0 ? $uniquePersonas / $totalArticles : 0;
        $locationDiversity = $totalArticles > 0 ? $uniqueLocations / $totalArticles : 0;
        $discussionDiversity = $totalArticles > 0 ? $uniqueDiscussions / $totalArticles : 0;
        
        return [
            'timeframeDays' => $timeframeDays,
            'totalArticles' => $totalArticles,
            'validArticles' => $validArticles,
            'validPercentage' => $totalArticles > 0 ? ($validArticles / $totalArticles) * 100 : 0,
            'uniqueElements' => [
                'persona' => $uniquePersonas,
                'location' => $uniqueLocations,
                'discussion' => $uniqueDiscussions
            ],
            'diversityMetrics' => [
                'persona' => $personaDiversity,
                'location' => $locationDiversity,
                'discussion' => $discussionDiversity,
                'overall' => ($personaDiversity + $locationDiversity + $discussionDiversity) / 3
            ]
        ];
    }

    /**
     * Avalia a compatibilidade do conjunto de humanização com o contexto do artigo
     * 
     * @param array<string, mixed> $humanizationSet Conjunto de humanização
     * @param string $context Contexto do artigo
     * @return float Score de compatibilidade (0.0 a 1.0)
     */
    private function evaluateContextCompatibility(
        array $humanizationSet,
        string $context
    ): float {
        $persona = $humanizationSet['persona'] ?? null;
        $location = $humanizationSet['location'] ?? null;
        $discussions = $humanizationSet['discussions'] ?? [];
        
        $scoreFactors = [];
        
        // Avalia compatibilidade da persona
        if ($persona !== null) {
            // Neste exemplo, usamos uma lógica simples
            // Em uma implementação real, usaríamos algoritmos mais sofisticados
            $personaScore = 0.8; // Valor padrão para compatibilidade razoável
            $scoreFactors[] = $personaScore;
        }
        
        // Avalia compatibilidade da localização
        if ($location !== null) {
            $locationScore = 0.8; // Valor padrão para compatibilidade razoável
            $scoreFactors[] = $locationScore;
        }
        
        // Avalia compatibilidade das discussões
        if (!empty($discussions)) {
            $discussionScores = [];
            
            foreach ($discussions as $discussion) {
                // Verifica se a discussão contém palavras-chave do contexto
                $contextWords = explode(' ', strtolower($context));
                $relevantWords = array_filter($contextWords, fn($word) => strlen($word) > 3);
                
                $relevantCount = 0;
                foreach ($relevantWords as $word) {
                    if (str_contains(strtolower($discussion->getContent()), $word)) {
                        $relevantCount++;
                    }
                }
                
                $discussionScore = $relevantCount > 0 
                    ? min(1.0, $relevantCount / count($relevantWords) * 1.5) 
                    : 0.5;
                
                $discussionScores[] = $discussionScore;
            }
            
            // Usa a média dos scores das discussões
            if (!empty($discussionScores)) {
                $scoreFactors[] = array_sum($discussionScores) / count($discussionScores);
            }
        }
        
        // Calcula score global (média dos fatores)
        if (empty($scoreFactors)) {
            return 0.0;
        }
        
        return array_sum($scoreFactors) / count($scoreFactors);
    }
}