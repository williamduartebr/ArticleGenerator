<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Repository;

use Src\ArticleGenerator\Domain\Entity\HumanPersona;
use Src\ArticleGenerator\Domain\ValueObject\Profession;

/**
 * Interface de repositório para HumanPersona
 */
interface HumanPersonaRepositoryInterface
{
    /**
     * Salva uma persona
     * 
     * @param HumanPersona $persona A persona a ser salva
     * @return HumanPersona A persona salva com ID atualizado
     */
    public function save(HumanPersona $persona): HumanPersona;

    /**
     * Exclui uma persona
     * 
     * @param HumanPersona $persona A persona a ser excluída
     * @return bool Verdadeiro se a exclusão foi bem-sucedida
     */
    public function delete(HumanPersona $persona): bool;

    /**
     * Encontra uma persona pelo ID
     * 
     * @param string $id O ID da persona a ser encontrada
     * @return HumanPersona|null A persona encontrada ou null se não existir
     */
    public function findById(string $id): ?HumanPersona;

    /**
     * Retorna todas as personas
     * 
     * @param int $page Número da página para paginação
     * @param int $perPage Número de itens por página
     * @return array<HumanPersona> Array de personas
     */
    public function findAll(int $page = 1, int $perPage = 15): array;

    /**
     * Encontra personas por profissão
     * 
     * @param Profession|string $profession A profissão a ser procurada
     * @param int $page Número da página para paginação
     * @param int $perPage Número de itens por página
     * @return array<HumanPersona> Array de personas com a profissão especificada
     */
    public function findByProfession(Profession|string $profession, int $page = 1, int $perPage = 15): array;

    /**
     * Encontra personas por localização
     * 
     * @param string $location A localização a ser procurada
     * @param int $page Número da página para paginação
     * @param int $perPage Número de itens por página
     * @return array<HumanPersona> Array de personas com a localização especificada
     */
    public function findByLocation(string $location, int $page = 1, int $perPage = 15): array;

    /**
     * Obtém uma persona aleatória que não foi utilizada recentemente
     * 
     * @param int $recentDays Número de dias para considerar como uso recente
     * @return HumanPersona|null Uma persona aleatória não utilizada recentemente ou null se nenhuma estiver disponível
     */
    public function getRandomUnused(int $recentDays = 7): ?HumanPersona;

    /**
     * Obtém uma persona aleatória com um número máximo de usos
     * 
     * @param int $maxUsageCount Número máximo de usos
     * @return HumanPersona|null Uma persona aleatória com número de usos <= maxUsageCount ou null se nenhuma estiver disponível
     */
    public function getRandomWithMaxUsageCount(int $maxUsageCount): ?HumanPersona;

    /**
     * Marca uma persona como utilizada
     * 
     * @param HumanPersona $persona A persona a ser marcada como utilizada
     * @return HumanPersona A persona atualizada
     */
    public function markAsUsed(HumanPersona $persona): HumanPersona;

    /**
     * Obtém estatísticas de uso das personas
     * 
     * @return array<string, mixed> Array associativo com estatísticas de uso
     */
    public function getUsageStatistics(): array;

    /**
     * Conta o total de personas no repositório
     * 
     * @return int O número total de personas
     */
    public function count(): int;

    /**
     * Encontra personas com veículos preferidos específicos
     * 
     * @param string $vehicleMake Marca do veículo
     * @param string|null $vehicleModel Modelo do veículo (opcional)
     * @param int $page Número da página para paginação
     * @param int $perPage Número de itens por página
     * @return array<HumanPersona> Array de personas com preferência pelo veículo especificado
     */
    public function findByPreferredVehicle(
        string $vehicleMake, 
        ?string $vehicleModel = null, 
        int $page = 1, 
        int $perPage = 15
    ): array;

    /**
     * Obtém personas com menor número de utilizações
     * 
     * @param int $limit Limite de personas a retornar
     * @return array<HumanPersona> Array de personas ordenadas por número de utilizações (ascendente)
     */
    public function getLeastUsedPersonas(int $limit = 10): array;

    /**
     * Busca personas por nome completo ou parcial
     * 
     * @param string $name Nome completo ou parcial para buscar
     * @param int $page Número da página para paginação
     * @param int $perPage Número de itens por página
     * @return array<HumanPersona> Array de personas que correspondem ao critério de busca
     */
    public function searchByName(string $name, int $page = 1, int $perPage = 15): array;
}