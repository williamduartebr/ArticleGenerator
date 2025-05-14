<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Service;

use Src\ArticleGenerator\Domain\Entity\HumanPersona;
use Src\ArticleGenerator\Domain\Entity\BrazilianLocation;
use Src\ArticleGenerator\Domain\Entity\ForumDiscussion;

/**
 * Interface para o serviço de rastreamento de humanização
 */
interface HumanizationTrackingServiceInterface
{
    /**
     * Registra o uso de componentes de humanização
     * 
     * @param HumanPersona $persona A persona utilizada
     * @param BrazilianLocation $location A localização utilizada
     * @param array<ForumDiscussion> $discussions As discussões utilizadas
     * @return bool Verdadeiro se o registro foi bem-sucedido
     */
    public function registerComponentsUsage(
        HumanPersona $persona,
        BrazilianLocation $location,
        array $discussions
    ): bool;
    
    /**
     * Verifica se uma combinação de elementos foi utilizada recentemente
     * 
     * @param HumanPersona $persona A persona a verificar
     * @param BrazilianLocation $location A localização a verificar
     * @param int $recentDays Número de dias para considerar como uso recente
     * @return bool Verdadeiro se a combinação foi utilizada recentemente
     */
    public function isCombinationRecentlyUsed(
        HumanPersona $persona,
        BrazilianLocation $location,
        int $recentDays = 30
    ): bool;
    
    /**
     * Obtém o histórico de uso recente de elementos
     * 
     * @param int $limit Limite de registros a retornar
     * @return array<string, array<string>> Histórico de uso recente por tipo de elemento
     */
    public function getRecentUsageHistory(int $limit = 50): array;
    
    /**
     * Obtém estatísticas de uso de elementos
     * 
     * @return array<string, mixed> Estatísticas de uso
     */
    public function getUsageStatistics(): array;
    
    /**
     * Identifica elementos mais utilizados
     * 
     * @param int $limit Limite de elementos a retornar
     * @return array<string, array<array<string, mixed>>> Elementos mais utilizados por tipo
     */
    public function getMostUsedElements(int $limit = 10): array;
    
    /**
     * Identifica elementos menos utilizados
     * 
     * @param int $limit Limite de elementos a retornar
     * @return array<string, array<array<string, mixed>>> Elementos menos utilizados por tipo
     */
    public function getLeastUsedElements(int $limit = 10): array;
    
    /**
     * Analisa padrões de uso de elementos
     * 
     * @param int $timeframeDays Período de análise em dias
     * @return array<string, mixed> Análise de padrões de uso
     */
    public function analyzeUsagePatterns(int $timeframeDays = 90): array;
    
    /**
     * Identifica combinações de elementos mais frequentes
     * 
     * @param int $limit Limite de combinações a retornar
     * @return array<array<string, mixed>> Combinações mais frequentes
     */
    public function getFrequentCombinations(int $limit = 10): array;
    
    /**
     * Limpa o histórico de uso mais antigo que um determinado período
     * 
     * @param int $olderThanDays Limpar registros mais antigos que este número de dias
     * @return int Número de registros removidos
     */
    public function cleanOldUsageHistory(int $olderThanDays = 180): int;
}