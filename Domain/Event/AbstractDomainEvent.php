<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Event;

/**
 * Classe abstrata base para eventos de domínio
 */
readonly abstract class AbstractDomainEvent implements DomainEventInterface
{
    /**
     * @var string $eventId ID único do evento
     */
    private string $eventId;
    
    /**
     * @var \DateTimeImmutable $occurredAt Timestamp de quando o evento ocorreu
     */
    private \DateTimeImmutable $occurredAt;
    
    /**
     * Construtor
     */
    public function __construct()
    {
        $this->eventId = uniqid('event_', true);
        $this->occurredAt = new \DateTimeImmutable();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getEventId(): string
    {
        return $this->eventId;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getEventType(): string
    {
        return static::class;
    }
    
    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'eventId' => $this->eventId,
            'eventType' => $this->getEventType(),
            'occurredAt' => $this->occurredAt->format('Y-m-d\TH:i:s.uP')
        ];
    }
}