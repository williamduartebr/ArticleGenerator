<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Event;

use Src\ArticleGenerator\Domain\ValueObject\VehicleReference;

/**
 * Evento disparado quando a geração de um novo artigo é solicitada
 */
readonly class ArticleGenerationRequestedEvent extends AbstractDomainEvent
{
    /**
     * @param string $articleId ID do artigo a ser gerado
     * @param VehicleReference|null $vehicleModel Modelo do veículo (opcional)
     * @param array<string> $topicKeywords Palavras-chave do tópico
     * @param array<string, mixed> $generationParameters Parâmetros adicionais de geração
     * @param \DateTimeImmutable $requestedAt Timestamp de quando a geração foi solicitada
     */
    public function __construct(
        private string $articleId,
        private ?VehicleReference $vehicleModel,
        private array $topicKeywords,
        private array $generationParameters,
        private \DateTimeImmutable $requestedAt
    ) {
        parent::__construct();
    }
    
    /**
     * Retorna o ID do artigo
     * 
     * @return string
     */
    public function getArticleId(): string
    {
        return $this->articleId;
    }
    
    /**
     * Retorna o modelo do veículo
     * 
     * @return VehicleReference|null
     */
    public function getVehicleModel(): ?VehicleReference
    {
        return $this->vehicleModel;
    }
    
    /**
     * Retorna as palavras-chave do tópico
     * 
     * @return array<string>
     */
    public function getTopicKeywords(): array
    {
        return $this->topicKeywords;
    }
    
    /**
     * Retorna os parâmetros de geração
     * 
     * @return array<string, mixed>
     */
    public function getGenerationParameters(): array
    {
        return $this->generationParameters;
    }
    
    /**
     * Retorna o timestamp de quando a geração foi solicitada
     * 
     * @return \DateTimeImmutable
     */
    public function getRequestedAt(): \DateTimeImmutable
    {
        return $this->requestedAt;
    }
    
    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'articleId' => $this->articleId,
            'vehicleModel' => $this->vehicleModel ? (string)$this->vehicleModel : null,
            'topicKeywords' => $this->topicKeywords,
            'generationParameters' => $this->generationParameters,
            'requestedAt' => $this->requestedAt->format('Y-m-d\TH:i:s.uP')
        ]);
    }
}