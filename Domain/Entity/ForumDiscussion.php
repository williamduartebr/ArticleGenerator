<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Entity;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * Representa uma discussão de fórum automotivo
 * 
 * Esta entidade armazena o conteúdo de discussões reais de fóruns
 * automotivos que podem ser utilizadas como fonte para artigos.
 */
class ForumDiscussion
{
    /**
     * @param string|null $id Identificador único da discussão
     * @param string $title Título da discussão
     * @param string $content Conteúdo da discussão
     * @param array<string> $tags Tags relacionadas à discussão
     * @param string $forumUrl URL do fórum original
     * @param ForumCategory $category Categoria da discussão
     * @param DateTimeInterface $publishedAt Data de publicação da discussão
     * @param int $usageCount Número de vezes que a discussão foi utilizada
     * @param DateTimeInterface|null $lastUsedAt Data da última utilização
     * @param int $relevanceScore Pontuação de relevância da discussão
     */
    public function __construct(
        public readonly ?string $id = null,
        private readonly string $title,
        private readonly string $content,
        private readonly array $tags,
        private readonly string $forumUrl,
        private readonly ForumCategory $category,
        private readonly DateTimeInterface $publishedAt,
        private int $usageCount = 0,
        private ?DateTimeInterface $lastUsedAt = null,
        private int $relevanceScore = 0
    ) {
    }

    /**
     * Retorna o título da discussão
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Retorna o conteúdo da discussão
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Retorna as tags relacionadas à discussão
     * 
     * @return array<string>
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * Retorna a URL do fórum original
     */
    public function getForumUrl(): string
    {
        return $this->forumUrl;
    }

    /**
     * Retorna a categoria da discussão
     */
    public function getCategory(): ForumCategory
    {
        return $this->category;
    }

    /**
     * Retorna a data de publicação da discussão
     */
    public function getPublishedAt(): DateTimeInterface
    {
        return $this->publishedAt;
    }

    /**
     * Retorna a contagem de uso da discussão
     */
    public function getUsageCount(): int
    {
        return $this->usageCount;
    }

    /**
     * Retorna a data da última utilização da discussão
     */
    public function getLastUsedAt(): ?DateTimeInterface
    {
        return $this->lastUsedAt;
    }

    /**
     * Retorna a pontuação de relevância da discussão
     */
    public function getRelevanceScore(): int
    {
        return $this->relevanceScore;
    }

    /**
     * Define a pontuação de relevância da discussão
     */
    public function setRelevanceScore(int $relevanceScore): self
    {
        $this->relevanceScore = $relevanceScore;
        return $this;
    }

    /**
     * Marca a discussão como utilizada
     */
    public function markAsUsed(): self
    {
        $this->lastUsedAt = new DateTimeImmutable();
        $this->incrementUsageCount();
        
        return $this;
    }

    /**
     * Verifica se a discussão foi utilizada recentemente (últimos 30 dias)
     */
    public function isRecentlyUsed(): bool
    {
        if ($this->lastUsedAt === null) {
            return false;
        }

        $now = new DateTimeImmutable();
        $diff = $now->diff($this->lastUsedAt);
        
        return $diff->days < 30;
    }

    /**
     * Incrementa o contador de uso da discussão
     */
    public function incrementUsageCount(): self
    {
        $this->usageCount++;
        
        return $this;
    }

    /**
     * Verifica se a discussão é relevante para um determinado tópico
     * 
     * @param array<string> $keywords Palavras-chave para verificar relevância
     */
    public function isRelevantFor(array $keywords): bool
    {
        $contentLower = strtolower($this->content);
        $titleLower = strtolower($this->title);
        
        foreach ($keywords as $keyword) {
            $keywordLower = strtolower($keyword);
            
            if (str_contains($contentLower, $keywordLower) || 
                str_contains($titleLower, $keywordLower) || 
                in_array($keywordLower, array_map('strtolower', $this->tags))) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Verifica se a discussão é recente
     * 
     * @param int $daysThreshold Número de dias para considerar como recente
     */
    public function isRecent(int $daysThreshold = 90): bool
    {
        $now = new DateTimeImmutable();
        $diff = $now->diff($this->publishedAt);
        
        return $diff->days < $daysThreshold;
    }
}