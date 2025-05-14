<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Service;

use Src\ArticleGenerator\Domain\Entity\HumanPersona;
use Src\ArticleGenerator\Domain\Entity\BrazilianLocation;
use Src\ArticleGenerator\Domain\Entity\ForumDiscussion;
use Src\ArticleGenerator\Domain\ValueObject\VehicleReference;

/**
 * Interface para o serviço de seleção não-repetitiva de elementos
 */
interface NonRepetitiveSelectionServiceInterface
{
    /**
     * Seleciona uma persona aleatória não-repetitiva com base no contexto
     * 
     * @param string $context Contexto do artigo
     * @param array<string> $keywords Palavras-chave relacionadas ao artigo
     * @param array<string> $excludeIds IDs de personas a serem excluídas da seleção
     * @param VehicleReference|null $vehicle Veículo relacionado (opcional)
     * @return HumanPersona|null Uma persona selecionada ou null se nenhuma estiver disponível
     */
    public function selectPersona(
        string $context,
        array $keywords = [],
        array $excludeIds = [],
        ?VehicleReference $vehicle = null
    ): ?HumanPersona;
    
    /**
     * Seleciona uma localização aleatória não-repetitiva com base no contexto
     * 
     * @param string $context Contexto do artigo
     * @param array<string> $excludeIds IDs de localizações a serem excluídas da seleção
     * @param HumanPersona|null $persona Persona relacionada (opcional)
     * @return BrazilianLocation|null Uma localização selecionada ou null se nenhuma estiver disponível
     */
    public function selectLocation(
        string $context,
        array $excludeIds = [],
        ?HumanPersona $persona = null
    ): ?BrazilianLocation;
    
    /**
     * Seleciona uma discussão aleatória não-repetitiva com base no contexto
     * 
     * @param string $context Contexto do artigo
     * @param array<string> $keywords Palavras-chave relacionadas ao artigo
     * @param array<string> $excludeIds IDs de discussões a serem excluídas da seleção
     * @param VehicleReference|null $vehicle Veículo relacionado (opcional)
     * @return ForumDiscussion|null Uma discussão selecionada ou null se nenhuma estiver disponível
     */
    public function selectDiscussion(
        string $context,
        array $keywords = [],
        array $excludeIds = [],
        ?VehicleReference $vehicle = null
    ): ?ForumDiscussion;
    
    /**
     * Seleciona múltiplas discussões não-repetitivas com base no contexto
     * 
     * @param string $context Contexto do artigo
     * @param array<string> $keywords Palavras-chave relacionadas ao artigo
     * @param int $count Número de discussões a selecionar
     * @param array<string> $excludeIds IDs de discussões a serem excluídas da seleção
     * @param VehicleReference|null $vehicle Veículo relacionado (opcional)
     * @return array<ForumDiscussion> Array de discussões selecionadas
     */
    public function selectMultipleDiscussions(
        string $context,
        array $keywords = [],
        int $count = 3,
        array $excludeIds = [],
        ?VehicleReference $vehicle = null
    ): array;
    
    /**
     * Seleciona elementos compatíveis para um artigo
     * 
     * @param string $context Contexto do artigo
     * @param array<string> $keywords Palavras-chave relacionadas ao artigo
     * @param VehicleReference|null $vehicle Veículo relacionado (opcional)
     * @param array<string, array<string>> $excludeIds IDs a serem excluídos por tipo de elemento
     * @return array<string, mixed> Array associativo com persona, localização e discussões selecionadas
     */
    public function selectCompatibleElements(
        string $context,
        array $keywords = [],
        ?VehicleReference $vehicle = null,
        array $excludeIds = []
    ): array;
    
    /**
     * Obtém o histórico de seleções recentes
     * 
     * @param int $limit Limite de registros a retornar
     * @return array<string, array<string>> Histórico de seleções recentes por tipo de elemento
     */
    public function getRecentSelectionHistory(int $limit = 50): array;
    
    /**
     * Limpa o histórico de seleções para um determinado tipo de elemento
     * 
     * @param string $elementType Tipo de elemento ('persona', 'location', 'discussion')
     * @param int $olderThanDays Limpar registros mais antigos que este número de dias
     * @return bool Verdadeiro se a operação foi bem-sucedida
     */
    public function clearSelectionHistory(string $elementType, int $olderThanDays = 30): bool;
}