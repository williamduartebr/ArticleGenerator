<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Exception;

/**
 * Exceção base para erros relacionados à API Claude
 */
class ApiConnectionException extends DomainException
{
    /**
     * Construtor
     * 
     * @param string $message Mensagem de erro
     * @param int $code Código de erro
     * @param \Throwable|null $previous Exceção anterior na cadeia
     */
    public function __construct(
        string $message,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct(
            $message,
            'API_CONNECTION_ERROR',
            ['service' => 'ClaudeAPI'],
            $code,
            $previous
        );
    }
}