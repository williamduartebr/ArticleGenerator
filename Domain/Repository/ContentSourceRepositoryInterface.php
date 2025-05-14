<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Repository;

use Src\ArticleGenerator\Domain\Entity\ContentSource;
use Src\ArticleGenerator\Domain\Entity\ContentSourceType;

/**
 * Interface de repositório para ContentSource
 */
interface ContentSourceRepositoryInterface
{
    /**
     * Salva uma fonte de conteúdo
     * 
     * @param ContentSource $source A fonte a ser salva
     * @return ContentSource A fonte salva com ID atualizado
     */
    public function save(ContentSource $source): ContentSource;

    /**
     * Exclui uma fonte de conteúdo
     * 
     * @param ContentSource $source A fonte a ser excluída
     * @return bool Verdadeiro se a exclusão foi bem-sucedida
     */
    public function delete(ContentSource $source): bool;

    /**
     * Encontra uma fonte pelo ID
     * 
     * @param string $id O ID da fonte a ser encontrada
     * @return ContentSource|null A fonte encontrada ou null se não existir
     */
    public function findById(string $id): ?ContentSource;

    /**
     * Retorna todas as fontes
     * 
     * @param int $page Número da página para paginação
     * @param int $perPage Número de itens por página
     * @return array<ContentSource> Array de fontes
     */
    public function findAll(int $page = 1, int $perPage = 15): array;

    /**
     * Encontra fontes por tipo
     * 
     * @param ContentSourceType $type O tipo a ser procurado
     * @param int $page Número da página para paginação
     * @param int $perPage Número de itens por página
     * @return array<ContentSource> Array de fontes do tipo especificado
     */
    public function findByType(ContentSourceType $type, int $page = 1, int $perPage = 15): array;

    /**
     * Encontra fontes por nível mínimo de confiabilidade
     * 
     * @param float $minTrustScore Pontuação mínima de confiabilidade
     * @param int $page Número da página para paginação
     * @param int $perPage Número de itens por página
     * @return array<ContentSource> Array de fontes com trustScore >= minTrustScore
     */
    public function findByMinTrustScore(float $minTrustScore, int $page = 1, int $perPage = 15): array;

    /**
     * Encontra fontes ativas
     * 
     * @param int $page Número da página para paginação
     * @param int $perPage Número de itens por página
     * @return array<ContentSource> Array de fontes ativas
     */
    public function findActive(int $page = 1, int $perPage = 15): array;

    /**
     * Encontra fontes por tópico
     * 
     * @param string $topic O tópico a ser procurado
     * @param int $page Número da página para paginação
     * @param int $perPage Número de itens por página
     * @return array<ContentSource> Array de fontes que abordam o tópico especificado
     */
    public function findByTopic(string $topic, int $page = 1, int $perPage = 15): array;

    /**
     * Obtém fontes confiáveis por categoria (tipo)
     * 
     * @param ContentSourceType $type O tipo desejado
     * @param float $minTrustScore Pontuação mínima de confiabilidade
     * @param int $limit Número máximo de fontes a retornar
     * @return array<ContentSource> Array de fontes confiáveis do tipo especificado
     */
    public function getTrustedByType(ContentSourceType $type, float $minTrustScore = 70.0, int $limit = 5): array;

    /**
     * Marca uma fonte como utilizada
     * 
     * @param ContentSource $source A fonte a ser marcada como utilizada
     * @return ContentSource A fonte atualizada
     */
    public function markAsUsed(ContentSource $source): ContentSource;

    /**
     * Marca uma fonte como extraída
     * 
     * @param ContentSource $source A fonte a ser marcada como extraída
     * @return ContentSource A fonte atualizada
     */
    public function markAsCrawled(ContentSource $source): ContentSource;

    /**
     * Encontra fontes que precisam ser extraídas
     * 
     * @param int $daysThreshold Número de dias para considerar como desatualizada
     * @param int $limit Número máximo de fontes a retornar
     * @return array<ContentSource> Array de fontes que precisam ser extraídas
     */
    public function findNeedsCrawling(int $daysThreshold = 7, int $limit = 10): array;

    /**
     * Ativa uma fonte
     * 
     * @param ContentSource $source A fonte a ser ativada
     * @return ContentSource A fonte atualizada
     */
    public function activate(ContentSource $source): ContentSource;

    /**
     * Desativa uma fonte
     * 
     * @param ContentSource $source A fonte a ser desativada
     * @return ContentSource A fonte atualizada
     */
    public function deactivate(ContentSource $source): ContentSource;

    /**
     * Busca fontes por nome ou URL
     * 
     * @param string $search Texto para buscar em nome ou URL
     * @param int $page Número da página para paginação
     * @param int $perPage Número de itens por página
     * @return array<ContentSource> Array de fontes que correspondem ao critério de busca
     */
    public function search(string $search, int $page = 1, int $perPage = 15): array;

    /**
     * Obtém fontes ordenadas por pontuação de confiabilidade ponderada
     * 
     * @param int $limit Número máximo de fontes a retornar
     * @return array<ContentSource> Array de fontes ordenadas por confiabilidade ponderada (descendente)
     */
    public function getMostTrusted(int $limit = 10): array;

    /**
     * Conta o total de fontes no repositório
     * 
     * @return int O número total de fontes
     */
    public function count(): int;

    /**
     * Conta o total de fontes ativas no repositório
     * 
     * @return int O número total de fontes ativas
     */
    public function countActive(): int;
}