<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Event;

/**
 * Interface para handlers do evento IncompatibleElementsDetectedEvent
 */
interface IncompatibleElementsDetectedEventHandlerInterface
{
    /**
     * Manipula o evento quando elementos incompatíveis são detectados
     * 
     * @param IncompatibleElementsDetectedEvent $event O evento a ser manipulado
     * @return void
     */
    public function handle(IncompatibleElementsDetectedEvent $event): void;
}