<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Event;

/**
 * Evento disparado quando uma persona, localização ou discussão é usada em um artigo
 */
readonly class HumanizationElementUsedEvent extends AbstractDomainEvent
{
    /**
     * @param string $elementType Tipo do elemento (persona, location, discussion)
     * @param string $elementId ID do elemento
     * @param string $articleId ID do artigo onde o elemento foi usado
     * @param \DateTimeImmutable $usedAt Timestamp de quando o elemento foi usado
     * @param array<string, mixed> $usageContext Contexto adicional do uso
     */
    public function __construct(
        private string $elementType,
        private string $elementId,
        private string $articleId,
        private \DateTimeImmutable $usedAt,
        private array $usageContext = []
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
     * Retorna o ID do artigo
     * 
     * @return string
     */
    public function getArticleId(): string
    {
        return $this->articleId;
    }
    
    /**
     * Retorna o timestamp de quando o elemento foi usado
     * 
     * @return \DateTimeImmutable
     */
    public function getUsedAt(): \DateTimeImmutable
    {
        return $this->usedAt;
    }
    
    /**
     * Retorna o contexto do uso
     * 
     * @return array<string, mixed>
     */
    public function getUsageContext(): array
    {
        return $this->usageContext;
    }
    
    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'elementType' => $this->elementType,
            'elementId' => $this->elementId,
            'articleId' => $this->articleId,
            'usedAt' => $this->usedAt->format('Y-m-d\TH:i:s.uP'),
            'usageContext' => $this->usageContext
        ]);
    }
}