<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Src\ArticleGenerator\Domain\ValueObject\PersonaName;

/**
 * Representa uma persona humana para geração de artigos
 * 
 * Esta entidade armazena as características de uma persona fictícia que será
 * utilizada como fonte ou personagem nos artigos gerados automaticamente.
 */
class HumanPersona
{
    /**
     * @param string|null $id Identificador único da persona
     * @param PersonaName|string $name Nome da persona (como ValueObject ou string)
     * @param string $profession Profissão da persona
     * @param string $location Localização geográfica da persona
     * @param array<string> $preferredVehicles Lista de veículos preferidos
     * @param int $usageCount Número de vezes que a persona foi utilizada
     * @param DateTimeInterface|null $lastUsedAt Data da última utilização
     */
    public function __construct(
        public readonly ?string $id = null,
        private PersonaName|string $name,
        private string $profession,
        private string $location,
        private array $preferredVehicles = [],
        private int $usageCount = 0,
        private ?DateTimeInterface $lastUsedAt = null
    ) {
    }

    /**
     * Retorna o nome da persona
     */
    public function getName(): PersonaName|string
    {
        return $this->name;
    }

    /**
     * Define o nome da persona
     */
    public function setName(PersonaName|string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Retorna a profissão da persona
     */
    public function getProfession(): string
    {
        return $this->profession;
    }

    /**
     * Define a profissão da persona
     */
    public function setProfession(string $profession): self
    {
        $this->profession = $profession;
        return $this;
    }

    /**
     * Retorna a localização da persona
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * Define a localização da persona
     */
    public function setLocation(string $location): self
    {
        $this->location = $location;
        return $this;
    }

    /**
     * Retorna os veículos preferidos da persona
     * 
     * @return array<string>
     */
    public function getPreferredVehicles(): array
    {
        return $this->preferredVehicles;
    }

    /**
     * Define os veículos preferidos da persona
     * 
     * @param array<string> $preferredVehicles
     */
    public function setPreferredVehicles(array $preferredVehicles): self
    {
        $this->preferredVehicles = $preferredVehicles;
        return $this;
    }

    /**
     * Adiciona um veículo à lista de preferidos
     */
    public function addPreferredVehicle(string $vehicle): self
    {
        if (!in_array($vehicle, $this->preferredVehicles)) {
            $this->preferredVehicles[] = $vehicle;
        }
        
        return $this;
    }

    /**
     * Retorna a contagem de uso da persona
     */
    public function getUsageCount(): int
    {
        return $this->usageCount;
    }

    /**
     * Retorna a data da última utilização da persona
     */
    public function getLastUsedAt(): ?DateTimeInterface
    {
        return $this->lastUsedAt;
    }

    /**
     * Marca a persona como utilizada
     */
    public function markAsUsed(): self
    {
        $this->lastUsedAt = new DateTimeImmutable();
        $this->incrementUsageCount();
        
        return $this;
    }

    /**
     * Verifica se a persona foi utilizada recentemente (últimos 7 dias)
     */
    public function isRecentlyUsed(): bool
    {
        if ($this->lastUsedAt === null) {
            return false;
        }

        $now = new DateTimeImmutable();
        $diff = $now->diff($this->lastUsedAt);
        
        return $diff->days < 7;
    }

    /**
     * Incrementa o contador de uso da persona
     */
    public function incrementUsageCount(): self
    {
        $this->usageCount++;
        
        return $this;
    }
}