<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Service;

use Src\ArticleGenerator\Domain\Entity\HumanPersona;
use Src\ArticleGenerator\Domain\Entity\BrazilianLocation;
use Src\ArticleGenerator\Domain\Entity\ForumDiscussion;
use Src\ArticleGenerator\Domain\ValueObject\VehicleReference;

/**
 * Interface para o serviço orquestrador de geração de artigos
 */
interface ArticleGenerationOrchestratorInterface
{
    /**
     * Prepara um conjunto de elementos de humanização para um artigo
     * 
     * @param string $context Contexto do artigo
     * @param array<string> $keywords Palavras-chave relacionadas ao artigo
     * @param VehicleReference|null $vehicle Veículo relacionado (opcional)
     * @return array<string, mixed> Conjunto de elementos de humanização
     */
    public function prepareHumanizationSet(
        string $context,
        array $keywords = [],
        ?VehicleReference $vehicle = null
    ): array;
    
    /**
     * Prepara conjuntos de elementos de humanização para múltiplos artigos
     * 
     * @param array<string> $contexts Lista de contextos de artigos
     * @param array<int, array<string>> $keywordsByArticle Lista de palavras-chave por artigo
     * @param array<int, VehicleReference|null> $vehiclesByArticle Lista de veículos por artigo
     * @return array<int, array<string, mixed>> Conjuntos de elementos de humanização por artigo
     */
    public function prepareMultipleHumanizationSets(
        array $contexts,
        array $keywordsByArticle = [],
        array $vehiclesByArticle = []
    ): array;
    
    /**
     * Orquestra o processo completo de geração de artigo com humanização
     * 
     * @param string $context Contexto do artigo
     * @param array<string> $keywords Palavras-chave relacionadas ao artigo
     * @param VehicleReference|null $vehicle Veículo relacionado (opcional)
     * @param array<string, mixed> $options Opções adicionais para a geração do artigo
     * @return array<string, mixed> Resultado contendo elementos de humanização e metadados
     */
    public function orchestrateArticleGeneration(
        string $context,
        array $keywords = [],
        ?VehicleReference $vehicle = null,
        array $options = []
    ): array;
    
    /**
     * Orquestra o processo completo de geração de múltiplos artigos com humanização
     * 
     * @param array<string> $contexts Lista de contextos de artigos
     * @param array<int, array<string>> $keywordsByArticle Lista de palavras-chave por artigo
     * @param array<int, VehicleReference|null> $vehiclesByArticle Lista de veículos por artigo
     * @param array<string, mixed> $globalOptions Opções globais para todos os artigos
     * @return array<int, array<string, mixed>> Resultado por artigo
     */
    public function orchestrateBatchArticleGeneration(
        array $contexts,
        array $keywordsByArticle = [],
        array $vehiclesByArticle = [],
        array $globalOptions = []
    ): array;
    
    /**
     * Valida se um conjunto de elementos de humanização é adequado para um artigo
     * 
     * @param array<string, mixed> $humanizationSet Conjunto de elementos de humanização
     * @param string $context Contexto do artigo
     * @return array<string, mixed> Resultado da validação com possíveis sugestões
     */
    public function validateHumanizationSet(
        array $humanizationSet,
        string $context
    ): array;
    
    /**
     * Registra o uso de um conjunto de elementos de humanização
     * 
     * @param array<string, mixed> $humanizationSet Conjunto de elementos de humanização
     * @param string $articleId ID do artigo gerado (opcional)
     * @return bool Verdadeiro se o registro foi bem-sucedido
     */
    public function registerHumanizationSetUsage(
        array $humanizationSet,
        ?string $articleId = null
    ): bool;
    
    /**
     * Obtém estatísticas sobre a geração de artigos
     * 
     * @param int $timeframeDays Período de análise em dias
     * @return array<string, mixed> Estatísticas de geração de artigos
     */
    public function getGenerationStatistics(int $timeframeDays = 90): array;
}