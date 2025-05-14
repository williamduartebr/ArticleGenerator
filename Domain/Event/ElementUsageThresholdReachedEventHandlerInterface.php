<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Event;

/**
 * Interface para handlers do evento ElementUsageThresholdReachedEvent
 */
interface ElementUsageThresholdReachedEventHandlerInterface
{
    /**
     * Manipula o evento quando um elemento atinge um limite de uso predefinido
     * 
     * @param ElementUsageThresholdReachedEvent $event O evento a ser manipulado
     * @return void
     */
    public function handle(ElementUsageThresholdReachedEvent $event): void;
}