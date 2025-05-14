<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Exception;

/**
 * Classe base para todas as exceções de domínio
 */
abstract class DomainException extends \Exception
{
    /**
     * @var string $code Código de erro da exceção
     */
    protected string $errorCode;
    
    /**
     * @var array<string, mixed> $context Contexto adicional da exceção
     */
    protected array $context = [];
    
    /**
     * @var string $domain Domínio da exceção (subdomain)
     */
    protected string $domain = 'articleGenerator';
    
    /**
     * @var \DateTimeImmutable $occurredAt Timestamp de quando a exceção ocorreu
     */
    protected \DateTimeImmutable $occurredAt;
    
    /**
     * Construtor
     * 
     * @param string $message Mensagem de erro
     * @param string $errorCode Código de erro (específico do domínio)
     * @param array<string, mixed> $context Contexto adicional
     * @param int $code Código de erro (para compatibilidade com Exception)
     * @param \Throwable|null $previous Exceção anterior na cadeia
     */
    public function __construct(
        string $message,
        string $errorCode = 'DOMAIN_ERROR',
        array $context = [],
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        
        $this->errorCode = $errorCode;
        $this->context = $context;
        $this->occurredAt = new \DateTimeImmutable();
    }
    
    /**
     * Retorna o código de erro (específico do domínio)
     * 
     * @return string
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }
    
    /**
     * Retorna o contexto da exceção
     * 
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }
    
    /**
     * Adiciona contexto à exceção
     * 
     * @param string $key Chave do contexto
     * @param mixed $value Valor do contexto
     * @return self
     */
    public function addContext(string $key, mixed $value): self
    {
        $this->context[$key] = $value;
        return $this;
    }
    
    /**
     * Retorna o domínio da exceção
     * 
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }
    
    /**
     * Retorna o timestamp de quando a exceção ocorreu
     * 
     * @return \DateTimeImmutable
     */
    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
    
    /**
     * Converte a exceção para um array
     * 
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'message' => $this->getMessage(),
            'errorCode' => $this->errorCode,
            'domain' => $this->domain,
            'context' => $this->context,
            'occurredAt' => $this->occurredAt->format('Y-m-d\TH:i:s.uP'),
            'trace' => $this->getTraceAsString()
        ];
    }
    
    /**
     * Converte a exceção para JSON
     * 
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }
    
    /**
     * Retorna uma mensagem de log formatada
     * 
     * @return string
     */
    public function getLogMessage(): string
    {
        return sprintf(
            '[%s] %s | %s | %s',
            $this->domain,
            $this->errorCode,
            $this->getMessage(),
            json_encode($this->context)
        );
    }
}