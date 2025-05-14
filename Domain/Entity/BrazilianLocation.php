<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Entity;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * Representa uma localização brasileira para geração de artigos
 * 
 * Esta entidade armazena informações sobre cidades e regiões do Brasil
 * para contextualizar os artigos gerados.
 */
class BrazilianLocation
{
    /**
     * @param string|null $id Identificador único da localização
     * @param string $city Nome da cidade
     * @param string $region Região ou bairro
     * @param TrafficPattern $trafficPattern Padrão de tráfego da localização
     * @param BrazilianStateCode $stateCode Código do estado brasileiro
     * @param int $usageCount Número de vezes que a localização foi utilizada
     * @param DateTimeInterface|null $lastUsedAt Data da última utilização
     */
    public function __construct(
        public readonly ?string $id = null,
        private string $city,
        private string $region,
        private TrafficPattern $trafficPattern,
        private BrazilianStateCode $stateCode,
        private int $usageCount = 0,
        private ?DateTimeInterface $lastUsedAt = null
    ) {
    }

    /**
     * Retorna o nome da cidade
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * Define o nome da cidade
     */
    public function setCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Retorna a região ou bairro
     */
    public function getRegion(): string
    {
        return $this->region;
    }

    /**
     * Define a região ou bairro
     */
    public function setRegion(string $region): self
    {
        $this->region = $region;
        return $this;
    }

    /**
     * Retorna o padrão de tráfego da localização
     */
    public function getTrafficPattern(): TrafficPattern
    {
        return $this->trafficPattern;
    }

    /**
     * Define o padrão de tráfego da localização
     */
    public function setTrafficPattern(TrafficPattern $trafficPattern): self
    {
        $this->trafficPattern = $trafficPattern;
        return $this;
    }

    /**
     * Retorna o código do estado
     */
    public function getStateCode(): BrazilianStateCode
    {
        return $this->stateCode;
    }

    /**
     * Define o código do estado
     */
    public function setStateCode(BrazilianStateCode $stateCode): self
    {
        $this->stateCode = $stateCode;
        return $this;
    }

    /**
     * Retorna a contagem de uso da localização
     */
    public function getUsageCount(): int
    {
        return $this->usageCount;
    }

    /**
     * Retorna a data da última utilização da localização
     */
    public function getLastUsedAt(): ?DateTimeInterface
    {
        return $this->lastUsedAt;
    }

    /**
     * Marca a localização como utilizada
     */
    public function markAsUsed(): self
    {
        $this->lastUsedAt = new DateTimeImmutable();
        $this->incrementUsageCount();
        
        return $this;
    }

    /**
     * Verifica se a localização foi utilizada recentemente (últimos 7 dias)
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
     * Incrementa o contador de uso da localização
     */
    public function incrementUsageCount(): self
    {
        $this->usageCount++;
        
        return $this;
    }

    /**
     * Retorna o nome completo da localização (Cidade - UF)
     */
    public function getFullLocationName(): string
    {
        return "{$this->city} - {$this->stateCode->value}";
    }
}