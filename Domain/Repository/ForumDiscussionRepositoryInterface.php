<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Repository;

use Src\ArticleGenerator\Domain\Entity\ForumDiscussion;
use Src\ArticleGenerator\Domain\Entity\ForumCategory;
use Src\ArticleGenerator\Domain\ValueObject\VehicleReference;

/**
 * Interface de repositório para ForumDiscussion
 */
interface ForumDiscussionRepositoryInterface
{
    /**
     * Salva uma discussão de fórum
     * 
     * @param ForumDiscussion $discussion A discussão a ser salva
     * @return ForumDiscussion A discussão salva com ID atualizado
     */
    public function save(ForumDiscussion $discussion): ForumDiscussion;

    /**
     * Exclui uma discussão de fórum
     * 
     * @param ForumDiscussion $discussion A discussão a ser excluída
     * @return bool Verdadeiro se a exclusão foi bem-sucedida
     */
    public function delete(ForumDiscussion $discussion): bool;

    /**
     * Encontra uma discussão pelo ID
     * 
     * @param string $id O ID da discussão a ser encontrada
     * @return ForumDiscussion|null A discussão encontrada ou null se não existir
     */
    public function findById(string $id): ?ForumDiscussion;

    /**
     * Retorna todas as discussões
     * 
     * @param int $page Número da página para paginação
     * @param int $perPage Número de itens por página
     * @return array<ForumDiscussion> Array de discussões
     */
    public function findAll(int $page = 1, int $perPage = 15): array;

    /**
     * Encontra discussões por tópico
     * 
     * @param string $topic O tópico a ser procurado
     * @param int $page Número da página para paginação
     * @param int $perPage Número de itens por página
     * @return array<ForumDiscussion> Array de discussões sobre o tópico especificado
     */
    public function findByTopic(string $topic, int $page = 1, int $perPage = 15): array;

    /**
     * Encontra discussões por fonte (URL do fórum)
     * 
     * @param string $source A fonte a ser procurada
     * @param int $page Número da página para paginação
     * @param int $perPage Número de itens por página
     * @return array<ForumDiscussion> Array de discussões da fonte especificada
     */
    public function findBySource(string $source, int $page = 1, int $perPage = 15): array;

    /**
     * Encontra discussões por categoria
     * 
     * @param ForumCategory $category A categoria a ser procurada
     * @param int $page Número da página para paginação
     * @param int $perPage Número de itens por página
     * @return array<ForumDiscussion> Array de discussões na categoria especificada
     */
    public function findByCategory(ForumCategory $category, int $page = 1, int $perPage = 15): array;

    /**
     * Encontra discussões por relevância
     * 
     * @param int $minRelevanceScore Pontuação mínima de relevância
     * @param int $page Número da página para paginação
     * @param int $perPage Número de itens por página
     * @return array<ForumDiscussion> Array de discussões com relevância >= minRelevanceScore
     */
    public function findByRelevance(int $minRelevanceScore, int $page = 1, int $perPage = 15): array;

    /**
     * Filtra discussões por modelo de veículo
     * 
     * @param VehicleReference|string $vehicle O veículo a ser usado como filtro
     * @param int $page Número da página para paginação
     * @param int $perPage Número de itens por página
     * @return array<ForumDiscussion> Array de discussões relacionadas ao veículo especificado
     */
    public function filterByVehicle(VehicleReference|string $vehicle, int $page = 1, int $perPage = 15): array;

    /**
     * Obtém insights relevantes para um contexto específico
     * 
     * @param string $context O contexto para o qual os insights são necessários
     * @param array<string> $keywords Palavras-chave adicionais para refinar a busca
     * @param int $limit Número máximo de insights a retornar
     * @return array<ForumDiscussion> Array de discussões relevantes para o contexto especificado
     */
    public function getRelevantInsights(string $context, array $keywords = [], int $limit = 5): array;

    /**
     * Marca uma discussão como utilizada
     * 
     * @param ForumDiscussion $discussion A discussão a ser marcada como utilizada
     * @return ForumDiscussion A discussão atualizada
     */
    public function markAsUsed(ForumDiscussion $discussion): ForumDiscussion;

    /**
     * Obtém uma discussão aleatória que não foi utilizada recentemente
     * 
     * @param int $recentDays Número de dias para considerar como uso recente
     * @return ForumDiscussion|null Uma discussão aleatória não utilizada recentemente ou null se nenhuma estiver disponível
     */
    public function getRandomUnused(int $recentDays = 30): ?ForumDiscussion;

    /**
     * Obtém uma discussão aleatória em uma categoria específica
     * 
     * @param ForumCategory $category A categoria desejada
     * @param int $minRelevanceScore Pontuação mínima de relevância
     * @return ForumDiscussion|null Uma discussão aleatória na categoria especificada ou null se nenhuma estiver disponível
     */
    public function getRandomByCategory(ForumCategory $category, int $minRelevanceScore = 0): ?ForumDiscussion;

    /**
     * Busca discussões por texto no conteúdo ou título
     * 
     * @param string $searchText Texto para buscar
     * @param int $page Número da página para paginação
     * @param int $perPage Número de itens por página
     * @return array<ForumDiscussion> Array de discussões que correspondem ao critério de busca
     */
    public function search(string $searchText, int $page = 1, int $perPage = 15): array;

    /**
     * Obtém discussões recentes (criadas nos últimos N dias)
     * 
     * @param int $days Número de dias para considerar como recente
     * @param int $page Número da página para paginação
     * @param int $perPage Número de itens por página
     * @return array<ForumDiscussion> Array de discussões recentes
     */
    public function getRecent(int $days = 30, int $page = 1, int $perPage = 15): array;

    /**
     * Encontra discussões com palavras-chave múltiplas (operação AND)
     * 
     * @param array<string> $keywords Lista de palavras-chave
     * @param int $page Número da página para paginação
     * @param int $perPage Número de itens por página
     * @return array<ForumDiscussion> Array de discussões que contêm todas as palavras-chave
     */
    public function findByKeywords(array $keywords, int $page = 1, int $perPage = 15): array;

    /**
     * Conta o total de discussões no repositório
     * 
     * @return int O número total de discussões
     */
    public function count(): int;
}