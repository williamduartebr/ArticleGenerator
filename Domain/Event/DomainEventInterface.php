<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Event;

/**
 * Interface genérica para eventos de domínio
 */
interface DomainEventInterface
{
    /**
     * Retorna o ID do evento
     * 
     * @return string
     */
    public function getEventId(): string;
    
    /**
     * Retorna o timestamp de quando o evento ocorreu
     * 
     * @return \DateTimeImmutable
     */
    public function getOccurredAt(): \DateTimeImmutable;
    
    /**
     * Retorna o tipo do evento
     * 
     * @return string
     */
    public function getEventType(): string;
    
    /**
     * Retorna os dados do evento em formato array
     * 
     * @return array<string, mixed>
     */
    public function toArray(): array;
}