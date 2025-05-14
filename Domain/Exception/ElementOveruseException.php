<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Exception;

/**
 * Exceção lançada quando um elemento atinge um limite de uso predefinido
 */
class ElementOveruseException extends DomainException
{
    /**
     * Níveis de severidade de sobreutilização
     */
    public const SEVERITY_LOW = 'low';
    public const SEVERITY_MEDIUM = 'medium';
    public const SEVERITY_HIGH = 'high';
    public const SEVERITY_CRITICAL = 'critical';
    
    /**
     * @var string $elementType Tipo do elemento
     */
    private string $elementType;
    
    /**
     * @var string $elementId ID do elemento
     */
    private string $elementId;
    
    /**
     * @var int $currentUsage Uso atual do elemento
     */
    private int $currentUsage;
    
    /**
     * @var int $usageLimit Limite de uso do elemento
     */
    private int $usageLimit;
    
    /**
     * @var string $severity Nível de severidade
     */
    private string $severity;
    
    /**
     * Construtor
     * 
     * @param string $message Mensagem de erro
     * @param string $elementType Tipo do elemento
     * @param string $elementId ID do elemento
     * @param int $currentUsage Uso atual do elemento
     * @param int $usageLimit Limite de uso do elemento
     * @param string $severity Nível de severidade
     * @param array<string, mixed> $context Contexto adicional
     */
    public function __construct(
        string $message,
        string $elementType,
        string $elementId,
        int $currentUsage,
        int $usageLimit,
        string $severity = self::SEVERITY_MEDIUM,
        array $context = []
    ) {
        $errorCode = 'ELEMENT_OVERUSE_' . strtoupper($severity);
        
        parent::__construct(
            $message,
            $errorCode,
            array_merge($context, [
                'elementType' => $elementType,
                'elementId' => $elementId,
                'currentUsage' => $currentUsage,
                'usageLimit' => $usageLimit,
                'severity' => $severity
            ])
        );
        
        $this->elementType = $elementType;
        $this->elementId = $elementId;
        $this->currentUsage = $currentUsage;
        $this->usageLimit = $usageLimit;
        $this->severity = $severity;
    }
    
    /**
     * Cria uma exceção para uso excessivo diário
     * 
     * @param string $elementType Tipo do elemento
     * @param string $elementId ID do elemento
     * @param string $elementName Nome legível do elemento
     * @param int $currentUsage Uso atual do elemento
     * @param int $dailyLimit Limite diário de uso
     * @return self
     */
    public static function dailyLimitExceeded(
        string $elementType,
        string $elementId,
        string $elementName,
        int $currentUsage,
        int $dailyLimit
    ): self {
        $message = sprintf(
            'Limite diário de uso excedido para %s "%s". Uso atual: %d, Limite: %d',
            $elementType,
            $elementName,
            $currentUsage,
            $dailyLimit
        );
        
        // Determina a severidade com base na proporção de uso
        $usageRatio = $currentUsage / $dailyLimit;
        $severity = self::getSeverityFromRatio($usageRatio);
        
        return new self(
            $message,
            $elementType,
            $elementId,
            $currentUsage,
            $dailyLimit,
            $severity,
            [
                'period' => 'daily',
                'elementName' => $elementName,
                'usageRatio' => $usageRatio
            ]
        );
    }
    
    /**
     * Cria uma exceção para uso excessivo semanal
     * 
     * @param string $elementType Tipo do elemento
     * @param string $elementId ID do elemento
     * @param string $elementName Nome legível do elemento
     * @param int $currentUsage Uso atual do elemento
     * @param int $weeklyLimit Limite semanal de uso
     * @return self
     */
    public static function weeklyLimitExceeded(
        string $elementType,
        string $elementId,
        string $elementName,
        int $currentUsage,
        int $weeklyLimit
    ): self {
        $message = sprintf(
            'Limite semanal de uso excedido para %s "%s". Uso atual: %d, Limite: %d',
            $elementType,
            $elementName,
            $currentUsage,
            $weeklyLimit
        );
        
        // Determina a severidade com base na proporção de uso
        $usageRatio = $currentUsage / $weeklyLimit;
        $severity = self::getSeverityFromRatio($usageRatio);
        
        return new self(
            $message,
            $elementType,
            $elementId,
            $currentUsage,
            $weeklyLimit,
            $severity,
            [
                'period' => 'weekly',
                'elementName' => $elementName,
                'usageRatio' => $usageRatio
            ]
        );
    }
    
    /**
     * Cria uma exceção para uso excessivo mensal
     * 
     * @param string $elementType Tipo do elemento
     * @param string $elementId ID do elemento
     * @param string $elementName Nome legível do elemento
     * @param int $currentUsage Uso atual do elemento
     * @param int $monthlyLimit Limite mensal de uso
     * @return self
     */
    public static function monthlyLimitExceeded(
        string $elementType,
        string $elementId,
        string $elementName,
        int $currentUsage,
        int $monthlyLimit
    ): self {
        $message = sprintf(
            'Limite mensal de uso excedido para %s "%s". Uso atual: %d, Limite: %d',
            $elementType,
            $elementName,
            $currentUsage,
            $monthlyLimit
        );
        
        // Determina a severidade com base na proporção de uso
        $usageRatio = $currentUsage / $monthlyLimit;
        $severity = self::getSeverityFromRatio($usageRatio);
        
        return new self(
            $message,
            $elementType,
            $elementId,
            $currentUsage,
            $monthlyLimit,
            $severity,
            [
                'period' => 'monthly',
                'elementName' => $elementName,
                'usageRatio' => $usageRatio
            ]
        );
    }
    
    /**
     * Cria uma exceção para uso excessivo em um período personalizado
     * 
     * @param string $elementType Tipo do elemento
     * @param string $elementId ID do elemento
     * @param string $elementName Nome legível do elemento
     * @param int $currentUsage Uso atual do elemento
     * @param int $usageLimit Limite de uso
     * @param string $period Descrição do período
     * @param string $severity Nível de severidade
     * @return self
     */
    public static function customLimitExceeded(
        string $elementType,
        string $elementId,
        string $elementName,
        int $currentUsage,
        int $usageLimit,
        string $period,
        string $severity = self::SEVERITY_MEDIUM
    ): self {
        $message = sprintf(
            'Limite de uso em %s excedido para %s "%s". Uso atual: %d, Limite: %d',
            $period,
            $elementType,
            $elementName,
            $currentUsage,
            $usageLimit
        );
        
        return new self(
            $message,
            $elementType,
            $elementId,
            $currentUsage,
            $usageLimit,
            $severity,
            [
                'period' => $period,
                'elementName' => $elementName,
                'usageRatio' => $currentUsage / $usageLimit
            ]
        );
    }
    
    /**
     * Retorna o tipo do elemento
     * 
     * @return string
     */
    public function getElementType(): string
    {
        return $this->elementType;
    }
    
    /**
     * Retorna o ID do elemento
     * 
     * @return string
     */
    public function getElementId(): string
    {
        return $this->elementId;
    }
    
    /**
     * Retorna o uso atual do elemento
     * 
     * @return int
     */
    public function getCurrentUsage(): int
    {
        return $this->currentUsage;
    }
    
    /**
     * Retorna o limite de uso do elemento
     * 
     * @return int
     */
    public function getUsageLimit(): int
    {
        return $this->usageLimit;
    }
    
    /**
     * Retorna o nível de severidade
     * 
     * @return string
     */
    public function getSeverity(): string
    {
        return $this->severity;
    }
    
    /**
     * Retorna a proporção de uso (uso atual / limite)
     * 
     * @return float
     */
    public function getUsageRatio(): float
    {
        return $this->usageLimit > 0 ? $this->currentUsage / $this->usageLimit : INF;
    }
    
    /**
     * Verifica se a severidade é crítica
     * 
     * @return bool
     */
    public function isCritical(): bool
    {
        return $this->severity === self::SEVERITY_CRITICAL;
    }
    
    /**
     * Retorna sugestões para resolver o problema de sobreutilização
     * 
     * @return array<string, string>
     */
    public function getSuggestions(): array
    {
        return [
            'useAlternative' => 'Considere usar um elemento alternativo',
            'waitPeriod' => sprintf(
                'Aguarde até que o período atual termine para usar este elemento novamente',
                $this->getContext()['period'] ?? 'definido'
            ),
            'increaseLimit' => 'Se apropriado, considere aumentar o limite de uso para este elemento'
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'elementType' => $this->elementType,
            'elementId' => $this->elementId,
            'currentUsage' => $this->currentUsage,
            'usageLimit' => $this->usageLimit,
            'usageRatio' => $this->getUsageRatio(),
            'severity' => $this->severity,
            'suggestions' => $this->getSuggestions()
        ]);
    }
    
    /**
     * Determina o nível de severidade com base na proporção de uso
     * 
     * @param float $ratio Proporção de uso (atual / limite)
     * @return string
     */
    private static function getSeverityFromRatio(float $ratio): string
    {
        return match(true) {
            $ratio >= 2.0 => self::SEVERITY_CRITICAL,
            $ratio >= 1.5 => self::SEVERITY_HIGH,
            $ratio >= 1.0 => self::SEVERITY_MEDIUM,
            default => self::SEVERITY_LOW
        };
    }
}