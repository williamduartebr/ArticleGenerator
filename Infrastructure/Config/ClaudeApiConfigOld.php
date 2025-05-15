<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Infrastructure\Config;

/**
 * Configurações da API Claude
 * 
 * Esta classe encapsula as configurações necessárias para conexão
 * e utilização da API do modelo de linguagem Claude da Anthropic.
 */
class ClaudeApiConfigOld
{
    /**
     * Construtor
     * 
     * @param string $apiKey Chave de API para autenticação
     * @param string $baseUrl URL base da API
     * @param string $apiVersion Versão da API
     * @param string $defaultModel Modelo padrão do Claude a ser utilizado
     */
    public function __construct(
        private readonly string $apiKey,
        private readonly string $baseUrl = 'https://api.anthropic.com/v1',
        private readonly string $apiVersion = '2023-06-01',
        private readonly string $defaultModel = 'claude-3-opus-20240229'
    ) {
    }

    /**
     * Obtém a chave de API
     * 
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Obtém a URL base da API
     * 
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Obtém a versão da API
     * 
     * @return string
     */
    public function getApiVersion(): string
    {
        return $this->apiVersion;
    }

    /**
     * Obtém o modelo padrão do Claude
     * 
     * @return string
     */
    public function getDefaultModel(): string
    {
        return $this->defaultModel;
    }
}