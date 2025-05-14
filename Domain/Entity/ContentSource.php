<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Entity;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * Representa uma fonte de conteúdo para geração de artigos
 * 
 * Esta entidade armazena informações sobre a origem do conteúdo
 * utilizado para gerar artigos automatizados.
 */
class ContentSource
{
    /**
     * @param string|null $id Identificador único da fonte
     * @param string $name Nome da fonte de conteúdo
     * @param string $url URL da fonte de conteúdo
     * @param ContentSourceType $type Tipo da fonte de conteúdo
     * @param float $trustScore Pontuação de confiabilidade (0-100)
     * @param array<string> $topics Tópicos principais abordados pela fonte
     * @param DateTimeInterface|null $lastCrawledAt Data da última extração de conteúdo
     * @param bool $isActive Indica se a fonte está ativa para extração
     * @param int $usageCount Número de vezes que a fonte foi utilizada
     * @param DateTimeInterface|null $lastUsedAt Data da última utilização
     */
    public function __construct(
        public readonly ?string $id = null,
        private string $name,
        private string $url,
        private ContentSourceType $type,
        private float $trustScore,
        private array $topics = [],
        private ?DateTimeInterface $lastCrawledAt = null,
        private bool $isActive = true,
        private int $usageCount = 0,
        private ?DateTimeInterface $lastUsedAt = null
    ) {
    }

    /**
     * Retorna o nome da fonte
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Define o nome da fonte
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Retorna a URL da fonte
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Define a URL da fonte
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Retorna o tipo da fonte
     */
    public function getType(): ContentSourceType
    {
        return $this->type;
    }

    /**
     * Define o tipo da fonte
     */
    public function setType(ContentSourceType $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Retorna a pontuação de confiabilidade da fonte
     */
    public function getTrustScore(): float
    {
        return $this->trustScore;
    }

    /**
     * Define a pontuação de confiabilidade da fonte
     * 
     * @param float $trustScore Valor entre 0 e 100
     */
    public function setTrustScore(float $trustScore): self
    {
        $this->trustScore = max(0, min(100, $trustScore));
        return $this;
    }

    /**
     * Retorna os tópicos principais abordados pela fonte
     * 
     * @return array<string>
     */
    public function getTopics(): array
    {
        return $this->topics;
    }

    /**
     * Define os tópicos principais abordados pela fonte
     * 
     * @param array<string> $topics
     */
    public function setTopics(array $topics): self
    {
        $this->topics = $topics;
        return $this;
    }

    /**
     * Adiciona um tópico à lista de tópicos da fonte
     */
    public function addTopic(string $topic): self
    {
        if (!in_array($topic, $this->topics)) {
            $this->topics[] = $topic;
        }
        
        return $this;
    }

    /**
     * Retorna a data da última extração de conteúdo
     */
    public function getLastCrawledAt(): ?DateTimeInterface
    {
        return $this->lastCrawledAt;
    }

    /**
     * Define a data da última extração de conteúdo
     */
    public function setLastCrawledAt(?DateTimeInterface $lastCrawledAt): self
    {
        $this->lastCrawledAt = $lastCrawledAt;
        return $this;
    }

    /**
     * Indica se a fonte está ativa para extração
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * Define se a fonte está ativa para extração
     */
    public function setActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * Ativa a fonte para extração
     */
    public function activate(): self
    {
        $this->isActive = true;
        return $this;
    }

    /**
     * Desativa a fonte para extração
     */
    public function deactivate(): self
    {
        $this->isActive = false;
        return $this;
    }

    /**
     * Retorna a contagem de uso da fonte
     */
    public function getUsageCount(): int
    {
        return $this->usageCount;
    }

    /**
     * Retorna a data da última utilização da fonte
     */
    public function getLastUsedAt(): ?DateTimeInterface
    {
        return $this->lastUsedAt;
    }

    /**
     * Marca a fonte como utilizada
     */
    public function markAsUsed(): self
    {
        $this->lastUsedAt = new DateTimeImmutable();
        $this->incrementUsageCount();
        
        return $this;
    }

    /**
     * Incrementa o contador de uso da fonte
     */
    public function incrementUsageCount(): self
    {
        $this->usageCount++;
        
        return $this;
    }

    /**
     * Marca a fonte como recém-extraída
     */
    public function markAsCrawled(): self
    {
        $this->lastCrawledAt = new DateTimeImmutable();
        return $this;
    }

    /**
     * Verifica se a fonte precisa ser extraída novamente
     * 
     * @param int $daysThreshold Número de dias para considerar uma extração como desatualizada
     */
    public function needsCrawling(int $daysThreshold = 7): bool
    {
        if (!$this->isActive) {
            return false;
        }

        if ($this->lastCrawledAt === null) {
            return true;
        }

        $now = new DateTimeImmutable();
        $diff = $now->diff($this->lastCrawledAt);
        
        return $diff->days >= $daysThreshold;
    }

    /**
     * Calcula um score de confiabilidade ponderado baseado no uso
     * 
     * Este método retorna uma pontuação que considera tanto o trust score
     * quanto a frequência de uso da fonte.
     */
    public function getWeightedTrustScore(): float
    {
        $usageMultiplier = min(1.0 + ($this->usageCount / 100), 1.5);
        return $this->trustScore * $usageMultiplier;
    }

    /**
     * Verifica se a fonte é relevante para determinados tópicos
     * 
     * @param array<string> $targetTopics Tópicos para verificar relevância
     */
    public function isRelevantFor(array $targetTopics): bool
    {
        foreach ($targetTopics as $topic) {
            if (in_array($topic, $this->topics)) {
                return true;
            }
        }
        
        return false;
    }
}