<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Exception;

/**
 * Exceção lançada quando uma sessão tenta gerar mais artigos que o permitido
 */
class GenerationSessionLimitExceededException extends DomainException
{
    /**
     * @var string $sessionId ID da sessão de geração
     */
    private string $sessionId;
    
    /**
     * @var int $currentCount Número atual de artigos
     */
    private int $currentCount;
    
    /**
     * @var int $limit Limite de artigos
     */
    private int $limit;
    
    /**
     * @var array<string, mixed> $sessionData Dados adicionais da sessão
     */
    private array $sessionData;
    
    /**
     * Construtor
     * 
     * @param string $message Mensagem de erro
     * @param string $sessionId ID da sessão
     * @param int $currentCount Número atual de artigos
     * @param int $limit Limite de artigos
     * @param array<string, mixed> $sessionData Dados adicionais da sessão
     * @param array<string, mixed> $context Contexto adicional
     */
    public function __construct(
        string $message,
        string $sessionId,
        int $currentCount,
        int $limit,
        array $sessionData = [],
        array $context = []
    ) {
        parent::__construct(
            $message,
            'GENERATION_SESSION_LIMIT_EXCEEDED',
            array_merge($context, [
                'sessionId' => $sessionId,
                'currentCount' => $currentCount,
                'limit' => $limit,
                'sessionData' => $sessionData
            ])
        );
        
        $this->sessionId = $sessionId;
        $this->currentCount = $currentCount;
        $this->limit = $limit;
        $this->sessionData = $sessionData;
    }
    
    /**
     * Cria uma exceção para limite de sessão excedido
     * 
     * @param string $sessionId ID da sessão
     * @param int $currentCount Número atual de artigos
     * @param int $limit Limite de artigos
     * @param array<string, mixed> $sessionData Dados adicionais da sessão
     * @return self
     */
    public static function create(
        string $sessionId,
        int $currentCount,
        int $limit,
        array $sessionData = []
    ): self {
        $message = sprintf(
            'Limite de artigos excedido para a sessão %s. Atual: %d, Limite: %d',
            $sessionId,
            $currentCount,
            $limit
        );
        
        return new self($message, $sessionId, $currentCount, $limit, $sessionData);
    }
    
    /**
     * Cria uma exceção para tentativa de adicionar múltiplos artigos além do limite
     * 
     * @param string $sessionId ID da sessão
     * @param int $currentCount Número atual de artigos
     * @param int $requestedCount Número de artigos solicitados
     * @param int $limit Limite de artigos
     * @param array<string, mixed> $sessionData Dados adicionais da sessão
     * @return self
     */
    public static function createForBulkAddition(
        string $sessionId,
        int $currentCount,
        int $requestedCount,
        int $limit,
        array $sessionData = []
    ): self {
        $message = sprintf(
            'Tentativa de adicionar %d artigos à sessão %s, que já possui %d artigos (limite: %d)',
            $requestedCount,
            $sessionId,
            $currentCount,
            $limit
        );
        
        return new self(
            $message,
            $sessionId,
            $currentCount,
            $limit,
            $sessionData,
            ['requestedCount' => $requestedCount]
        );
    }
    
    /**
     * Retorna o ID da sessão
     * 
     * @return string
     */
    public function getSessionId(): string
    {
        return $this->sessionId;
    }
    
    /**
     * Retorna o número atual de artigos
     * 
     * @return int
     */
    public function getCurrentCount(): int
    {
        return $this->currentCount;
    }
    
    /**
     * Retorna o limite de artigos
     * 
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }
    
    /**
     * Retorna os dados adicionais da sessão
     * 
     * @return array<string, mixed>
     */
    public function getSessionData(): array
    {
        return $this->sessionData;
    }
    
    /**
     * Retorna o número de artigos excedentes
     * 
     * @return int
     */
    public function getExcessCount(): int
    {
        return max(0, $this->currentCount - $this->limit);
    }
    
    /**
     * Retorna sugestões para resolver o problema
     * 
     * @return array<string, string>
     */
    public function getSuggestions(): array
    {
        $excessCount = $this->getExcessCount();
        $suggestedNewSessions = ceil($excessCount / $this->limit);
        
        return [
            'createNewSession' => $suggestedNewSessions === 1
                ? 'Crie uma nova sessão para os artigos excedentes'
                : sprintf('Crie %d novas sessões para distribuir os artigos excedentes', $suggestedNewSessions),
            'removeExcessArticles' => sprintf(
                'Remova %d artigos da sessão atual para ficar dentro do limite',
                $excessCount
            ),
            'increaseLimit' => 'Se apropriado, considere aumentar o limite de artigos por sessão'
        ];
    }
    
    /**
     * Retorna uma sugestão de distribuição otimizada dos artigos em múltiplas sessões
     * 
     * @param int $totalArticles Total de artigos a distribuir (opcional, usa currentCount se não fornecido)
     * @return array<int, int> Distribuição sugerida (sessão => número de artigos)
     */
    public function getSuggestedDistribution(int $totalArticles = null): array
    {
        $totalArticles = $totalArticles ?? $this->currentCount;
        
        if ($totalArticles <= $this->limit) {
            return [1 => $totalArticles];
        }
        
        $sessionsNeeded = ceil($totalArticles / $this->limit);
        $distribution = [];
        $remainingArticles = $totalArticles;
        
        for ($i = 1; $i <= $sessionsNeeded; $i++) {
            $articlesForSession = min($this->limit, $remainingArticles);
            $distribution[$i] = $articlesForSession;
            $remainingArticles -= $articlesForSession;
        }
        
        return $distribution;
    }
    
    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'sessionId' => $this->sessionId,
            'currentCount' => $this->currentCount,
            'limit' => $this->limit,
            'excessCount' => $this->getExcessCount(),
            'suggestions' => $this->getSuggestions(),
            'suggestedDistribution' => $this->getSuggestedDistribution(),
            'sessionData' => $this->sessionData
        ]);
    }
}