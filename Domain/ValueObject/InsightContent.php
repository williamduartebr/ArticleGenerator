<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\ValueObject;

/**
 * Value Object para representar o conteúdo de um insight
 * 
 * Implementa uma representação imutável de um insight,
 * com validações e métodos para avaliar sua relevância.
 */
readonly class InsightContent
{
    /**
     * @param string $content Conteúdo do insight
     * @param string $source Fonte do insight
     * @param float $confidence Nível de confiança (0-100%)
     * 
     * @throws \InvalidArgumentException Se os dados não atenderem aos requisitos
     */
    public function __construct(
        public string $content,
        public string $source,
        public float $confidence
    ) {
        $this->validate();
    }

    /**
     * Valida os componentes do insight
     * 
     * @throws \InvalidArgumentException Se os dados não atenderem aos requisitos
     */
    private function validate(): void
    {
        if (empty(trim($this->content))) {
            throw new \InvalidArgumentException('O conteúdo do insight não pode ser vazio');
        }

        if (strlen(trim($this->content)) < 10) {
            throw new \InvalidArgumentException('O conteúdo do insight deve ter pelo menos 10 caracteres');
        }

        if (empty(trim($this->source))) {
            throw new \InvalidArgumentException('A fonte do insight não pode ser vazia');
        }

        if ($this->confidence < 0 || $this->confidence > 100) {
            throw new \InvalidArgumentException('O nível de confiança deve estar entre 0 e 100');
        }
    }

    /**
     * Retorna uma representação de string do insight
     */
    public function __toString(): string
    {
        return sprintf(
            "Insight: %s (Fonte: %s, Confiança: %.1f%%)",
            $this->getExcerpt(50),
            $this->source,
            $this->confidence
        );
    }

    /**
     * Verifica se outro insight é igual a este
     */
    public function equals(self $other): bool
    {
        return $this->content === $other->content && 
               $this->source === $other->source && 
               $this->confidence === $other->confidence;
    }

    /**
     * Avalia se o insight é considerado confiável (confiança >= 70%)
     */
    public function isReliable(): bool
    {
        return $this->confidence >= 70.0;
    }

    /**
     * Avalia se o insight é considerado de alta confiança (confiança >= 90%)
     */
    public function isHighConfidence(): bool
    {
        return $this->confidence >= 90.0;
    }

    /**
     * Avalia se o insight é considerado de baixa confiança (confiança < 30%)
     */
    public function isLowConfidence(): bool
    {
        return $this->confidence < 30.0;
    }

    /**
     * Obtém um trecho do conteúdo do insight
     * 
     * @param int $length Tamanho do trecho
     * @return string
     */
    public function getExcerpt(int $length = 100): string
    {
        if (mb_strlen($this->content) <= $length) {
            return $this->content;
        }
        
        return mb_substr($this->content, 0, $length) . '...';
    }

    /**
     * Avalia a relevância do insight para um determinado tópico
     * 
     * @param string $topic Tópico para verificar relevância
     * @param array<string> $keywordList Lista de palavras-chave relacionadas ao tópico
     * @return bool
     */
    public function isRelevantFor(string $topic, array $keywordList = []): bool
    {
        $contentLower = mb_strtolower($this->content);
        $topicLower = mb_strtolower($topic);
        
        // Verifica se o tópico está contido no conteúdo
        if (mb_strpos($contentLower, $topicLower) !== false) {
            return true;
        }
        
        // Verifica se alguma das palavras-chave está contida no conteúdo
        foreach ($keywordList as $keyword) {
            $keywordLower = mb_strtolower($keyword);
            if (mb_strpos($contentLower, $keywordLower) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Calcula a pontuação de relevância do insight
     * 
     * Combina o nível de confiança com o tamanho do conteúdo para
     * determinar uma pontuação de relevância geral.
     * 
     * @return float Pontuação de relevância (0-100)
     */
    public function getRelevanceScore(): float
    {
        // Conteúdos muito curtos recebem uma penalidade
        $lengthFactor = min(1.0, mb_strlen($this->content) / 200);
        
        // A pontuação combina o nível de confiança com o fator de tamanho
        return $this->confidence * $lengthFactor;
    }

    /**
     * Cria um novo insight a partir do conteúdo, inferindo uma confiança padrão
     * 
     * @param string $content Conteúdo do insight
     * @param string $source Fonte do insight
     * @return self
     */
    public static function fromContent(string $content, string $source): self
    {
        // Confiança padrão baseada no tamanho do conteúdo
        // Conteúdos maiores têm uma confiança inicial maior
        $defaultConfidence = min(75.0, 50.0 + (mb_strlen(trim($content)) / 20));
        
        return new self($content, $source, $defaultConfidence);
    }
}