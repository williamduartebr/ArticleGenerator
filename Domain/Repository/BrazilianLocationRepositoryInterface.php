<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Repository;

use Src\ArticleGenerator\Domain\Entity\BrazilianLocation;
use Src\ArticleGenerator\Domain\Entity\BrazilianStateCode;
use Src\ArticleGenerator\Domain\Entity\TrafficPattern;

/**
 * Interface de repositório para BrazilianLocation
 */
interface BrazilianLocationRepositoryInterface
{
    /**
     * Salva uma localização
     * 
     * @param BrazilianLocation $location A localização a ser salva
     * @return BrazilianLocation A localização salva com ID atualizado
     */
    public function save(BrazilianLocation $location): BrazilianLocation;

    /**
     * Exclui uma localização
     * 
     * @param BrazilianLocation $location A localização a ser excluída
     * @return bool Verdadeiro se a exclusão foi bem-sucedida
     */
    public function delete(BrazilianLocation $location): bool;

    /**
     * Encontra uma localização pelo ID
     * 
     * @param string $id O ID da localização a ser encontrada
     * @return BrazilianLocation|null A localização encontrada ou null se não existir
     */
    public function findById(string $id): ?BrazilianLocation;

    /**
     * Retorna todas as localizações
     * 
     * @param int $page Número da página para paginação
     * @param int $perPage Número de itens por página
     * @return array<BrazilianLocation> Array de localizações
     */
    public function findAll(int $page = 1, int $perPage = 15): array;

    /**
     * Encontra localizações por região/bairro
     * 
     * @param string $region A região/bairro a ser procurada
     * @param int $page Número da página para paginação
     * @param int $perPage Número de itens por página
     * @return array<BrazilianLocation> Array de localizações na região especificada
     */
    public function findByRegion(string $region, int $page = 1, int $perPage = 15): array;

    /**
     * Encontra localizações por estado
     * 
     * @param BrazilianStateCode $stateCode O código do estado a ser procurado
     * @param int $page Número da página para paginação
     * @param int $perPage Número de itens por página
     * @return array<BrazilianLocation> Array de localizações no estado especificado
     */
    public function findByState(BrazilianStateCode $stateCode, int $page = 1, int $perPage = 15): array;

    /**
     * Encontra localizações por cidade
     * 
     * @param string $city A cidade a ser procurada
     * @param int $page Número da página para paginação
     * @param int $perPage Número de itens por página
     * @return array<BrazilianLocation> Array de localizações na cidade especificada
     */
    public function findByCity(string $city, int $page = 1, int $perPage = 15): array;

    /**
     * Encontra localizações por padrão de tráfego
     * 
     * @param TrafficPattern $trafficPattern O padrão de tráfego a ser procurado
     * @param int $page Número da página para paginação
     * @param int $perPage Número de itens por página
     * @return array<BrazilianLocation> Array de localizações com o padrão de tráfego especificado
     */
    public function findByTrafficPattern(TrafficPattern $trafficPattern, int $page = 1, int $perPage = 15): array;

    /**
     * Obtém uma localização aleatória que não foi utilizada recentemente
     * 
     * @param int $recentDays Número de dias para considerar como uso recente
     * @return BrazilianLocation|null Uma localização aleatória não utilizada recentemente ou null se nenhuma estiver disponível
     */
    public function getRandomUnused(int $recentDays = 7): ?BrazilianLocation;

    /**
     * Obtém uma localização aleatória com um padrão de tráfego específico
     * 
     * @param TrafficPattern $trafficPattern O padrão de tráfego desejado
     * @return BrazilianLocation|null Uma localização aleatória com o padrão de tráfego especificado ou null se nenhuma estiver disponível
     */
    public function getRandomByTrafficPattern(TrafficPattern $trafficPattern): ?BrazilianLocation;

    /**
     * Obtém uma localização aleatória em um estado específico
     * 
     * @param BrazilianStateCode $stateCode O código do estado desejado
     * @return BrazilianLocation|null Uma localização aleatória no estado especificado ou null se nenhuma estiver disponível
     */
    public function getRandomByState(BrazilianStateCode $stateCode): ?BrazilianLocation;

    /**
     * Marca uma localização como utilizada
     * 
     * @param BrazilianLocation $location A localização a ser marcada como utilizada
     * @return BrazilianLocation A localização atualizada
     */
    public function markAsUsed(BrazilianLocation $location): BrazilianLocation;

    /**
     * Obtém estatísticas de uso das localizações
     * 
     * @return array<string, mixed> Array associativo com estatísticas de uso
     */
    public function getUsageStatistics(): array;

    /**
     * Conta o total de localizações no repositório
     * 
     * @return int O número total de localizações
     */
    public function count(): int;

    /**
     * Obtém localizações com menor número de utilizações
     * 
     * @param int $limit Limite de localizações a retornar
     * @return array<BrazilianLocation> Array de localizações ordenadas por número de utilizações (ascendente)
     */
    public function getLeastUsedLocations(int $limit = 10): array;

    /**
     * Obtém uma localização aleatória em uma cidade específica
     * 
     * @param string $city A cidade desejada
     * @return BrazilianLocation|null Uma localização aleatória na cidade especificada ou null se nenhuma estiver disponível
     */
    public function getRandomByCity(string $city): ?BrazilianLocation;

    /**
     * Busca localizações por texto
     * 
     * @param string $search Texto para buscar em cidade, região ou estado
     * @param int $page Número da página para paginação
     * @param int $perPage Número de itens por página
     * @return array<BrazilianLocation> Array de localizações que correspondem ao critério de busca
     */
    public function search(string $search, int $page = 1, int $perPage = 15): array;
}