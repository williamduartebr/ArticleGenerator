<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Service;

use Src\ArticleGenerator\Domain\Entity\HumanPersona;
use Src\ArticleGenerator\Domain\Entity\BrazilianLocation;
use Src\ArticleGenerator\Domain\Entity\ForumDiscussion;
use Src\ArticleGenerator\Domain\ValueObject\VehicleReference;

/**
 * Interface para o serviço de montagem de componentes de artigo
 */
interface ArticleComponentsAssemblyServiceInterface
{
    /**
     * Monta um conjunto de componentes para um artigo
     * 
     * @param string $articleContext Contexto do artigo
     * @param array<string> $keywords Palavras-chave relacionadas ao artigo
     * @param VehicleReference|null $vehicle Veículo relacionado (opcional)
     * @return array<string, mixed> Array associativo com componentes montados
     */
    public function assembleArticleComponents(
        string $articleContext,
        array $keywords = [],
        ?VehicleReference $vehicle = null
    ): array;
    
    /**
     * Monta um conjunto de componentes para artigos em lote
     * 
     * @param array<string> $articleContexts Lista de contextos dos artigos
     * @param array<int, array<string>> $keywordsByArticle Lista de palavras-chave por artigo
     * @param array<int, VehicleReference|null> $vehiclesByArticle Lista de veículos por artigo (opcional)
     * @return array<int, array<string, mixed>> Array de componentes montados por artigo
     */
    public function assembleBatchComponents(
        array $articleContexts,
        array $keywordsByArticle = [],
        array $vehiclesByArticle = []
    ): array;
    
    /**
     * Seleciona uma persona adequada para o artigo
     * 
     * @param string $articleContext Contexto do artigo
     * @param array<string> $keywords Palavras-chave relacionadas ao artigo
     * @param VehicleReference|null $vehicle Veículo relacionado (opcional)
     * @return HumanPersona|null Persona selecionada ou null se nenhuma estiver disponível
     */
    public function selectPersonaForArticle(
        string $articleContext,
        array $keywords = [],
        ?VehicleReference $vehicle = null
    ): ?HumanPersona;
    
    /**
     * Seleciona uma localização adequada para o artigo
     * 
     * @param string $articleContext Contexto do artigo
     * @param HumanPersona|null $persona Persona já selecionada para o artigo (opcional)
     * @return BrazilianLocation|null Localização selecionada ou null se nenhuma estiver disponível
     */
    public function selectLocationForArticle(
        string $articleContext,
        ?HumanPersona $persona = null
    ): ?BrazilianLocation;
    
    /**
     * Seleciona discussões/insights adequados para o artigo
     * 
     * @param string $articleContext Contexto do artigo
     * @param array<string> $keywords Palavras-chave relacionadas ao artigo
     * @param VehicleReference|null $vehicle Veículo relacionado (opcional)
     * @param int $count Número de discussões a selecionar
     * @return array<ForumDiscussion> Lista de discussões selecionadas
     */
    public function selectDiscussionsForArticle(
        string $articleContext,
        array $keywords = [],
        ?VehicleReference $vehicle = null,
        int $count = 3
    ): array;
    
    /**
     * Verifica a coerência dos componentes selecionados
     * 
     * @param HumanPersona|null $persona Persona selecionada
     * @param BrazilianLocation|null $location Localização selecionada
     * @param array<ForumDiscussion> $discussions Discussões selecionadas
     * @param string $articleContext Contexto do artigo
     * @return bool Verdadeiro se os componentes são coerentes entre si
     */
    public function checkComponentsCoherence(
        ?HumanPersona $persona,
        ?BrazilianLocation $location,
        array $discussions,
        string $articleContext
    ): bool;
    
    /**
     * Substitui componentes incoerentes por alternativas mais adequadas
     * 
     * @param array<string, mixed> $components Componentes atuais
     * @param string $articleContext Contexto do artigo
     * @param array<string> $keywords Palavras-chave relacionadas ao artigo
     * @return array<string, mixed> Componentes revisados
     */
    public function replaceIncoherentComponents(
        array $components,
        string $articleContext,
        array $keywords = []
    ): array;
}