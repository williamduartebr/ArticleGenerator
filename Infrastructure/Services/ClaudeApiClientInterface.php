<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Infrastructure\Services;

use Src\ArticleGenerator\Domain\Entity\HumanPersona;
use Src\ArticleGenerator\Domain\Entity\BrazilianLocation;
use Src\ArticleGenerator\Domain\Entity\ForumDiscussion;
use Src\ArticleGenerator\Domain\ValueObject\VehicleReference;

/**
 * Interface para o cliente da API Claude
 * 
 * Define os métodos necessários para interagir com a API do Claude
 * para geração e análise de conteúdo no contexto de artigos automatizados.
 */
interface ClaudeApiClientInterface
{
    /**
     * Gera o conteúdo de um artigo com base em um contexto e elementos de humanização
     * 
     * @param string $context Contexto principal do artigo
     * @param array<string> $keywords Palavras-chave relevantes para o artigo
     * @param HumanPersona|null $persona Persona para humanização do conteúdo
     * @param BrazilianLocation|null $location Localização para contextualização geográfica
     * @param array<ForumDiscussion> $discussions Discussões para fornecer insights e opiniões
     * @param VehicleReference|null $vehicle Veículo relacionado ao artigo (opcional)
     * @param array<string, mixed> $options Opções adicionais de geração
     * @return array<string, mixed> Conteúdo gerado e metadados associados
     * 
     * @throws \Src\ArticleGenerator\Domain\Exception\ApiConnectionException Se houver erro de conexão com a API
     * @throws \Src\ArticleGenerator\Domain\Exception\ContentGenerationException Se houver erro na geração do conteúdo
     */
    public function generateArticleContent(
        string $context,
        array $keywords,
        ?HumanPersona $persona = null,
        ?BrazilianLocation $location = null,
        array $discussions = [],
        ?VehicleReference $vehicle = null,
        array $options = []
    ): array;

    /**
     * Analisa um conteúdo existente para melhorar sua legibilidade e SEO
     * 
     * @param string $content Conteúdo a ser analisado
     * @param array<string> $targetKeywords Palavras-chave alvo para otimização
     * @param array<string, mixed> $seoParams Parâmetros de SEO para otimização
     * @return array<string, mixed> Resultados da análise e sugestões de melhoria
     * 
     * @throws \Src\ArticleGenerator\Domain\Exception\ApiConnectionException Se houver erro de conexão com a API
     * @throws \Src\ArticleGenerator\Domain\Exception\ContentAnalysisException Se houver erro na análise do conteúdo
     */
    public function analyzeContent(
        string $content,
        array $targetKeywords = [],
        array $seoParams = []
    ): array;

    /**
     * Gera títulos alternativos para um artigo com base no conteúdo
     * 
     * @param string $content Conteúdo do artigo
     * @param string $currentTitle Título atual do artigo (opcional)
     * @param int $count Número de títulos alternativos a serem gerados
     * @return array<string> Lista de títulos alternativos
     * 
     * @throws \Src\ArticleGenerator\Domain\Exception\ApiConnectionException Se houver erro de conexão com a API
     * @throws \Src\ArticleGenerator\Domain\Exception\TitleGenerationException Se houver erro na geração dos títulos
     */
    public function generateAlternativeTitles(
        string $content,
        string $currentTitle = '',
        int $count = 5
    ): array;

    /**
     * Humaniza um texto existente incorporando a voz e perspectiva de uma persona
     * 
     * @param string $content Conteúdo a ser humanizado
     * @param HumanPersona $persona Persona cuja voz será incorporada
     * @param BrazilianLocation|null $location Localização para contextualização (opcional)
     * @param array<string, mixed> $options Opções adicionais de humanização
     * @return string Conteúdo humanizado
     * 
     * @throws \Src\ArticleGenerator\Domain\Exception\ApiConnectionException Se houver erro de conexão com a API
     * @throws \Src\ArticleGenerator\Domain\Exception\ContentHumanizationException Se houver erro na humanização
     */
    public function humanizeContent(
        string $content,
        HumanPersona $persona,
        ?BrazilianLocation $location = null,
        array $options = []
    ): string;

    /**
     * Extrai insights relevantes de discussões de fórum
     * 
     * @param array<ForumDiscussion> $discussions Discussões a serem analisadas
     * @param string $context Contexto para determinar relevância
     * @param int $maxInsights Número máximo de insights a extrair
     * @return array<string, mixed> Insights extraídos com metadados
     * 
     * @throws \Src\ArticleGenerator\Domain\Exception\ApiConnectionException Se houver erro de conexão com a API
     * @throws \Src\ArticleGenerator\Domain\Exception\InsightExtractionException Se houver erro na extração
     */
    public function extractInsightsFromDiscussions(
        array $discussions,
        string $context,
        int $maxInsights = 5
    ): array;

    /**
     * Gera perguntas FAQ com base no conteúdo do artigo
     * 
     * @param string $content Conteúdo do artigo
     * @param int $count Número de perguntas a gerar
     * @param bool $includeAnswers Se deve incluir respostas para as perguntas
     * @return array<string, mixed> Perguntas e respostas geradas
     * 
     * @throws \Src\ArticleGenerator\Domain\Exception\ApiConnectionException Se houver erro de conexão com a API
     * @throws \Src\ArticleGenerator\Domain\Exception\FaqGenerationException Se houver erro na geração das FAQs
     */
    public function generateFaqFromContent(
        string $content,
        int $count = 5,
        bool $includeAnswers = true
    ): array;

    /**
     * Verifica se o conteúdo gerado atende às diretrizes de qualidade e políticas
     * 
     * @param string $content Conteúdo a ser verificado
     * @param array<string, mixed> $guidelines Diretrizes específicas para verificação
     * @return array<string, mixed> Resultados da verificação e sugestões de correção
     * 
     * @throws \Src\ArticleGenerator\Domain\Exception\ApiConnectionException Se houver erro de conexão com a API
     * @throws \Src\ArticleGenerator\Domain\Exception\ContentVerificationException Se houver erro na verificação
     */
    public function verifyContentGuidelines(
        string $content,
        array $guidelines = []
    ): array;

    /**
     * Retorna o status atual da API (disponibilidade, limites de uso, etc.)
     * 
     * @return array<string, mixed> Informações sobre o status da API
     * 
     * @throws \Src\ArticleGenerator\Domain\Exception\ApiConnectionException Se houver erro de conexão com a API
     */
    public function getApiStatus(): array;
}