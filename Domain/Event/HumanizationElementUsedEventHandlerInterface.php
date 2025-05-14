<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Event;

/**
 * Interface para handlers do evento HumanizationElementUsedEvent
 */
interface HumanizationElementUsedEventHandlerInterface
{
    /**
     * Manipula o evento quando um elemento de humanização é usado
     * 
     * @param HumanizationElementUsedEvent $event O evento a ser manipulado
     * @return void
     */
    public function handle(HumanizationElementUsedEvent $event): void;
}