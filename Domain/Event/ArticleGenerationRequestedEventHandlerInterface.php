<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Event;

/**
 * Interface para handlers do evento ArticleGenerationRequestedEvent
 */
interface ArticleGenerationRequestedEventHandlerInterface
{
    /**
     * Manipula o evento quando a geração de um artigo é solicitada
     * 
     * @param ArticleGenerationRequestedEvent $event O evento a ser manipulado
     * @return void
     */
    public function handle(ArticleGenerationRequestedEvent $event): void;
}