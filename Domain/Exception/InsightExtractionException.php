<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Exception;

/**
 * Exceção lançada quando ocorre um erro durante a extração de insights de discussões
 */
class InsightExtractionException extends DomainException
{
    /**
     * Construtor
     * 
     * @param string $message Mensagem de erro
     * @param int $code Código de erro
     * @param \Throwable|null $previous Exceção anterior na cadeia
     * @param array<string, mixed> $context Contexto adicional do erro
     */
    public function __construct(
        string $message,
        int $code = 0,
        ?\Throwable $previous = null,
        array $context = []
    ) {
        parent::__construct(
            $message,
            'INSIGHT_EXTRACTION_ERROR',
            array_merge(['service' => 'ClaudeAPI'], $context),
            $code,
            $previous
        );
    }
}