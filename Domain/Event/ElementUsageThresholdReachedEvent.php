<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Event;

/**
 * Evento disparado quando um elemento atinge um limite de uso predefinido
 */
readonly class ElementUsageThresholdReachedEvent extends AbstractDomainEvent
{
    /**
     * @param string $elementType Tipo do elemento (persona, location, discussion)
     * @param string $elementId ID do elemento
     * @param int $usageCount Número atual de usos do elemento
     * @param int $threshold Limite de uso que foi atingido
     * @param \DateTimeImmutable $thresholdReachedAt Timestamp de quando o limite foi atingido
     * @param array<string, mixed> $metadata Metadados adicionais
     */
    public function __construct(
        private string $elementType,
        private string $elementId,
        private int $usageCount,
        private int $threshold,
        private \DateTimeImmutable $thresholdReachedAt,
        private array $metadata = []
    ) {
        parent::__construct();
    }
    
    /**
     * Retorna o tipo do elemento
     * 
     * @return string
     */
    public function getElementType(): string
    {
        return $this->elementType;
    }
    
    /**
     * Retorna o ID do elemento
     * 
     * @return string
     */
    public function getElementId(): string
    {
        return $this->elementId;
    }
    
    /**
     * Retorna o número atual de usos do elemento
     * 
     * @return int
     */
    public function getUsageCount(): int
    {
        return $this->usageCount;
    }
    
    /**
     * Retorna o limite de uso que foi atingido
     * 
     * @return int
     */
    public function getThreshold(): int
    {
        return $this->threshold;
    }
    
    /**
     * Retorna o timestamp de quando o limite foi atingido
     * 
     * @return \DateTimeImmutable
     */
    public function getThresholdReachedAt(): \DateTimeImmutable
    {
        return $this->thresholdReachedAt;
    }
    
    /**
     * Retorna os metadados adicionais
     * 
     * @return array<string, mixed>
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }
    
    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'elementType' => $this->elementType,
            'elementId' => $this->elementId,
            'usageCount' => $this->usageCount,
            'threshold' => $this->threshold,
            'thresholdReachedAt' => $this->thresholdReachedAt->format('Y-m-d\TH:i:s.uP'),
            'metadata' => $this->metadata
        ]);
    }
}