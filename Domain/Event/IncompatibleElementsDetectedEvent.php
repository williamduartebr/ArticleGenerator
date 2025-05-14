<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Event;

/**
 * Evento disparado quando elementos incompatíveis são detectados
 */
readonly class IncompatibleElementsDetectedEvent extends AbstractDomainEvent
{
    /**
     * @param array<string, string> $elementIds IDs dos elementos incompatíveis (tipo => id)
     * @param string $incompatibilityReason Razão da incompatibilidade
     * @param \DateTimeImmutable $detectedAt Timestamp de quando a incompatibilidade foi detectada
     * @param string|null $articleId ID do artigo relacionado (opcional)
     * @param array<string, mixed> $additionalInfo Informações adicionais sobre a incompatibilidade
     */
    public function __construct(
        private array $elementIds,
        private string $incompatibilityReason,
        private \DateTimeImmutable $detectedAt,
        private ?string $articleId = null,
        private array $additionalInfo = []
    ) {
        parent::__construct();
    }
    
    /**
     * Retorna os IDs dos elementos incompatíveis
     * 
     * @return array<string, string>
     */
    public function getElementIds(): array
    {
        return $this->elementIds;
    }
    
    /**
     * Retorna a razão da incompatibilidade
     * 
     * @return string
     */
    public function getIncompatibilityReason(): string
    {
        return $this->incompatibilityReason;
    }
    
    /**
     * Retorna o timestamp de quando a incompatibilidade foi detectada
     * 
     * @return \DateTimeImmutable
     */
    public function getDetectedAt(): \DateTimeImmutable
    {
        return $this->detectedAt;
    }
    
    /**
     * Retorna o ID do artigo relacionado
     * 
     * @return string|null
     */
    public function getArticleId(): ?string
    {
        return $this->articleId;
    }
    
    /**
     * Retorna informações adicionais sobre a incompatibilidade
     * 
     * @return array<string, mixed>
     */
    public function getAdditionalInfo(): array
    {
        return $this->additionalInfo;
    }
    
    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'elementIds' => $this->elementIds,
            'incompatibilityReason' => $this->incompatibilityReason,
            'detectedAt' => $this->detectedAt->format('Y-m-d\TH:i:s.uP'),
            'articleId' => $this->articleId,
            'additionalInfo' => $this->additionalInfo
        ]);
    }
}