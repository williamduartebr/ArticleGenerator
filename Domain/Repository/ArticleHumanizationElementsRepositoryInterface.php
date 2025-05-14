<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Repository;

use Src\ArticleGenerator\Domain\Entity\HumanPersona;
use Src\ArticleGenerator\Domain\Entity\BrazilianLocation;
use Src\ArticleGenerator\Domain\Entity\ForumDiscussion;
use Src\ArticleGenerator\Domain\ValueObject\VehicleReference;

/**
 * Interface de repositório para elementos de humanização de artigos
 * 
 * Esta interface combina acesso a personas, localizações e discussões para
 * fornecer conjuntos complementares de elementos de humanização para artigos.
 */
interface ArticleHumanizationElementsRepositoryInterface
{
    /**
     * Obtém uma persona aleatória compatível com o contexto do artigo
     * 
     * @param string $articleContext Contexto do artigo
     * @param array<string> $keywords Palavras-chave relacionadas ao artigo
     * @param VehicleReference|null $vehicle Veículo relacionado ao artigo (opcional)
     * @return HumanPersona|null Uma persona compatível ou null se nenhuma estiver disponível
     */
    public function getRandomCompatiblePersona(
        string $articleContext, 
        array $keywords = [],
        ?VehicleReference $vehicle = null
    ): ?HumanPersona;

    /**
     * Obtém uma localização aleatória compatível com o contexto do artigo
     * 
     * @param string $articleContext Contexto do artigo
     * @param HumanPersona|null $persona Persona relacionada ao artigo (opcional)
     * @return BrazilianLocation|null Uma localização compatível ou null se nenhuma estiver disponível
     */
    public function getRandomCompatibleLocation(
        string $articleContext,
        ?HumanPersona $persona = null
    ): ?BrazilianLocation;

    /**
     * Obtém discussões relevantes para o contexto do artigo
     * 
     * @param string $articleContext Contexto do artigo
     * @param array<string> $keywords Palavras-chave relacionadas ao artigo
     * @param VehicleReference|null $vehicle Veículo relacionado ao artigo (opcional)
     * @param int $limit Número máximo de discussões a retornar
     * @return array<ForumDiscussion> Array de discussões relevantes
     */
    public function getRelevantDiscussions(
        string $articleContext,
        array $keywords = [],
        ?VehicleReference $vehicle = null,
        int $limit = 3
    ): array;

    /**
     * Obtém um conjunto completo de elementos de humanização para um artigo
     * 
     * @param string $articleContext Contexto do artigo
     * @param array<string> $keywords Palavras-chave relacionadas ao artigo
     * @param VehicleReference|null $vehicle Veículo relacionado ao artigo (opcional)
     * @return array<string, mixed> Array associativo com persona, localização e discussões
     */
    public function getHumanizationSet(
        string $articleContext,
        array $keywords = [],
        ?VehicleReference $vehicle = null
    ): array;

    /**
     * Marca um conjunto de elementos de humanização como utilizados
     * 
     * @param HumanPersona|null $persona A persona utilizada (opcional)
     * @param BrazilianLocation|null $location A localização utilizada (opcional)
     * @param array<ForumDiscussion> $discussions As discussões utilizadas (opcional)
     * @return bool Verdadeiro se a operação foi bem-sucedida
     */
    public function markHumanizationSetAsUsed(
        ?HumanPersona $persona = null,
        ?BrazilianLocation $location = null,
        array $discussions = []
    ): bool;

    /**
     * Obtém elementos de humanização menos utilizados
     * 
     * @param int $personaLimit Número máximo de personas a retornar
     * @param int $locationLimit Número máximo de localizações a retornar
     * @param int $discussionLimit Número máximo de discussões a retornar
     * @return array<string, mixed> Array associativo com personas, localizações e discussões menos utilizadas
     */
    public function getLeastUsedElements(
        int $personaLimit = 5,
        int $locationLimit = 5,
        int $discussionLimit = 5
    ): array;

    /**
     * Verifica se uma combinação de elementos de humanização já foi utilizada recentemente
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
     * Obtém estatísticas de uso dos elementos de humanização
     * 
     * @return array<string, mixed> Array associativo com estatísticas de uso
     */
    public function getUsageStatistics(): array;

    /**
     * Distribui personas entre artigos de forma balanceada
     * 
     * @param int $articleCount Número de artigos a serem gerados
     * @param array<string> $contexts Lista de contextos dos artigos
     * @return array<int, HumanPersona> Array associativo de personas por índice de artigo
     */
    public function distributePersonasForArticles(int $articleCount, array $contexts): array;

    /**
     * Distribui localizações entre artigos de forma balanceada
     * 
     * @param int $articleCount Número de artigos a serem gerados
     * @param array<string> $contexts Lista de contextos dos artigos
     * @return array<int, BrazilianLocation> Array associativo de localizações por índice de artigo
     */
    public function distributeLocationsForArticles(int $articleCount, array $contexts): array;

    /**
     * Obtém elementos de humanização complementares
     * 
     * @param HumanPersona|null $persona A persona base (opcional)
     * @param BrazilianLocation|null $location A localização base (opcional)
     * @param VehicleReference|null $vehicle O veículo base (opcional)
     * @return array<string, mixed> Array associativo com elementos complementares
     */
    public function getComplementaryElements(
        ?HumanPersona $persona = null,
        ?BrazilianLocation $location = null,
        ?VehicleReference $vehicle = null
    ): array;

    /**
     * Busca por elementos de humanização com base em palavras-chave
     * 
     * @param array<string> $keywords Palavras-chave para busca
     * @param bool $includePersonas Incluir personas na busca
     * @param bool $includeLocations Incluir localizações na busca
     * @param bool $includeDiscussions Incluir discussões na busca
     * @return array<string, mixed> Array associativo com resultados da busca
     */
    public function searchElementsByKeywords(
        array $keywords,
        bool $includePersonas = true,
        bool $includeLocations = true,
        bool $includeDiscussions = true
    ): array;
}