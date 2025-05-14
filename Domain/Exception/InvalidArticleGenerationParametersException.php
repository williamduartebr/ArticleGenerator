<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Exception;

/**
 * Exceção lançada quando os parâmetros para geração de artigo são inválidos
 */
class InvalidArticleGenerationParametersException extends DomainException
{
    /**
     * @var array<string, mixed> $parameters Parâmetros inválidos
     */
    private array $parameters;
    
    /**
     * @var array<string, array<string>> $violations Violações por parâmetro
     */
    private array $violations;
    
    /**
     * Construtor
     * 
     * @param string $message Mensagem de erro
     * @param array<string, mixed> $parameters Parâmetros inválidos
     * @param array<string, array<string>> $violations Violações por parâmetro
     * @param array<string, mixed> $context Contexto adicional
     */
    public function __construct(
        string $message,
        array $parameters,
        array $violations,
        array $context = []
    ) {
        parent::__construct(
            $message,
            'INVALID_ARTICLE_GENERATION_PARAMETERS',
            array_merge($context, [
                'parameters' => $parameters,
                'violations' => $violations
            ])
        );
        
        $this->parameters = $parameters;
        $this->violations = $violations;
    }
    
    /**
     * Cria uma exceção para parâmetros obrigatórios ausentes
     * 
     * @param array<string> $missingParams Lista de parâmetros ausentes
     * @param array<string, mixed> $providedParams Parâmetros fornecidos
     * @return self
     */
    public static function missingRequiredParameters(
        array $missingParams,
        array $providedParams = []
    ): self {
        $message = sprintf(
            'Parâmetros obrigatórios ausentes: %s',
            implode(', ', $missingParams)
        );
        
        $violations = [];
        foreach ($missingParams as $param) {
            $violations[$param] = ['O parâmetro é obrigatório'];
        }
        
        return new self($message, $providedParams, $violations);
    }
    
    /**
     * Cria uma exceção para parâmetros com valores inválidos
     * 
     * @param array<string, array<string>> $invalidParams Parâmetros inválidos com suas violações
     * @param array<string, mixed> $providedParams Todos os parâmetros fornecidos
     * @return self
     */
    public static function invalidParameterValues(
        array $invalidParams,
        array $providedParams
    ): self {
        $invalidParamNames = array_keys($invalidParams);
        
        $message = sprintf(
            'Parâmetros com valores inválidos: %s',
            implode(', ', $invalidParamNames)
        );
        
        return new self($message, $providedParams, $invalidParams);
    }
    
    /**
     * Cria uma exceção para combinação inválida de parâmetros
     * 
     * @param string $description Descrição do problema de combinação
     * @param array<string, mixed> $conflictingParams Parâmetros em conflito
     * @param array<string, mixed> $allParams Todos os parâmetros
     * @return self
     */
    public static function invalidParameterCombination(
        string $description,
        array $conflictingParams,
        array $allParams
    ): self {
        $message = sprintf(
            'Combinação inválida de parâmetros: %s',
            $description
        );
        
        $violations = [];
        foreach ($conflictingParams as $param => $value) {
            $violations[$param] = ["Conflito: {$description}"];
        }
        
        return new self($message, $allParams, $violations);
    }
    
    /**
     * Cria uma exceção para parâmetros desconhecidos
     * 
     * @param array<string> $unknownParams Lista de parâmetros desconhecidos
     * @param array<string, mixed> $allParams Todos os parâmetros
     * @return self
     */
    public static function unknownParameters(
        array $unknownParams,
        array $allParams
    ): self {
        $message = sprintf(
            'Parâmetros desconhecidos: %s',
            implode(', ', $unknownParams)
        );
        
        $violations = [];
        foreach ($unknownParams as $param) {
            $violations[$param] = ['Parâmetro desconhecido'];
        }
        
        return new self($message, $allParams, $violations);
    }
    
    /**
     * Retorna os parâmetros inválidos
     * 
     * @return array<string, mixed>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
    
    /**
     * Retorna as violações por parâmetro
     * 
     * @return array<string, array<string>>
     */
    public function getViolations(): array
    {
        return $this->violations;
    }
    
    /**
     * Retorna uma mensagem de erro formatada para cada violação
     * 
     * @return string
     */
    public function getFormattedViolationsMessage(): string
    {
        $lines = [];
        
        foreach ($this->violations as $param => $errors) {
            $lines[] = sprintf(
                "- %s: %s",
                $param,
                implode('; ', $errors)
            );
        }
        
        return implode("\n", $lines);
    }
    
    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'parameters' => $this->parameters,
            'violations' => $this->violations,
            'formattedViolations' => $this->getFormattedViolationsMessage()
        ]);
    }
}