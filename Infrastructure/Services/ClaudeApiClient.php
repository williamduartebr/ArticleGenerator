<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Infrastructure\Services;

use DateTimeImmutable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Src\ArticleGenerator\Domain\Entity\BrazilianLocation;
use Src\ArticleGenerator\Domain\Entity\ForumDiscussion;
use Src\ArticleGenerator\Domain\Entity\HumanPersona;
use Src\ArticleGenerator\Domain\Exception\ApiConnectionException;
use Src\ArticleGenerator\Domain\Exception\ContentAnalysisException;
use Src\ArticleGenerator\Domain\Exception\ContentGenerationException;
use Src\ArticleGenerator\Domain\Exception\ContentHumanizationException;
use Src\ArticleGenerator\Domain\Exception\ContentVerificationException;
use Src\ArticleGenerator\Domain\Exception\FaqGenerationException;
use Src\ArticleGenerator\Domain\Exception\InsightExtractionException;
use Src\ArticleGenerator\Domain\Exception\TitleGenerationException;
use Src\ArticleGenerator\Domain\ValueObject\VehicleReference;
use Src\ArticleGenerator\Infrastructure\Config\ClaudeApiConfig;
use Throwable;

/**
 * Cliente para a API Claude
 * 
 * Implementa a comunicação com a API do modelo de linguagem Claude da Anthropic
 * para geração e análise de conteúdo no contexto de artigos automatizados.
 */
class ClaudeApiClient implements ClaudeApiClientInterface
{
    /**
     * Número máximo de tentativas para chamadas à API
     */
    private const MAX_RETRY_ATTEMPTS = 3;

    /**
     * Tempo base para retry em milissegundos
     */
    private const BASE_RETRY_DELAY_MS = 1000;

    /**
     * Timeout padrão para requisições em segundos
     */
    private const DEFAULT_TIMEOUT_SECONDS = 60;

    /**
     * Modelo padrão do Claude a ser utilizado
     */
    private string $defaultModel;

    /**
     * Cliente HTTP configurado para requisições à API
     */
    private PendingRequest $httpClient;

    /**
     * Construtor
     * 
     * @param ClaudeApiConfig $config Configurações para a API Claude
     */
    public function __construct(private readonly ClaudeApiConfig $config)
    {
        $this->defaultModel = $this->config->getDefaultModel();
        $this->initHttpClient();
    }

    /**
     * Inicializa o cliente HTTP com as configurações apropriadas
     * 
     * @return void
     */
    private function initHttpClient(): void
    {
        $this->httpClient = Http::withHeaders([
            'x-api-key' => $this->config->getApiKey(),
            'anthropic-version' => $this->config->getApiVersion(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])
        ->timeout(self::DEFAULT_TIMEOUT_SECONDS)
        ->retry(self::MAX_RETRY_ATTEMPTS, function ($exception, $request) {
            // Apenas tenta novamente para erros 429 (rate limit), 500, 502, 503 e 504
            if ($exception instanceof RequestException) {
                $statusCode = $exception->response->status();
                $retryableStatusCodes = [429, 500, 502, 503, 504];
                
                if (in_array($statusCode, $retryableStatusCodes)) {
                    Log::warning('Claude API request failed with status {status}. Retrying...', [
                        'status' => $statusCode,
                        'error' => $exception->getMessage(),
                    ]);
                    return true;
                }
            }
            
            // Tenta novamente para erros de conexão
            if ($exception instanceof ConnectionException) {
                Log::warning('Claude API connection error. Retrying...', [
                    'error' => $exception->getMessage(),
                ]);
                return true;
            }
            
            return false;
        }, function ($attempt) {
            // Implementa exponential backoff
            return self::BASE_RETRY_DELAY_MS * (2 ** ($attempt - 1));
        });
    }

    /**
     * {@inheritDoc}
     */
    public function generateArticleContent(
        string $context,
        array $keywords,
        ?HumanPersona $persona = null,
        ?BrazilianLocation $location = null,
        array $discussions = [],
        ?VehicleReference $vehicle = null,
        array $options = []
    ): array {
        $model = $options['model'] ?? $this->defaultModel;
        $maxTokens = $options['max_tokens'] ?? 4000;
        $temperature = $options['temperature'] ?? 0.7;
        
        try {
            Log::info('Generating article content', [
                'context' => substr($context, 0, 100) . '...',
                'keywords' => $keywords,
                'persona_id' => $persona?->id,
                'location_id' => $location?->id,
                'discussions_count' => count($discussions),
                'vehicle' => $vehicle?->fullDescription(),
            ]);
            
            // Prepara os insights das discussões
            $insights = [];
            foreach ($discussions as $discussion) {
                $insights[] = [
                    'title' => $discussion->getTitle(),
                    'content' => substr($discussion->getContent(), 0, 300) . '...',
                    'category' => $discussion->getCategory()->value,
                ];
            }
            
            // Constrói o prompt para o Claude
            $systemPrompt = $this->buildSystemPrompt($persona, $location, $vehicle);
            $userPrompt = $this->buildArticleGenerationPrompt($context, $keywords, $insights);
            
            $response = $this->makeApiRequest('messages', 'POST', [
                'model' => $model,
                'max_tokens' => $maxTokens,
                'temperature' => $temperature,
                'system' => $systemPrompt,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $userPrompt,
                    ],
                ],
            ]);
            
            Log::info('Article content generated successfully', [
                'content_length' => strlen($response['content'][0]['text'] ?? ''),
                'model' => $model,
                'usage' => $response['usage'] ?? [],
            ]);
            
            return [
                'title' => $this->extractTitleFromContent($response['content'][0]['text'] ?? ''),
                'content' => $response['content'][0]['text'] ?? '',
                'metadata' => [
                    'model' => $model,
                    'generated_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                    'keywords' => $keywords,
                    'persona_id' => $persona?->id,
                    'location_id' => $location?->id,
                    'vehicle' => $vehicle?->fullDescription(),
                    'token_usage' => $response['usage'] ?? [],
                ],
            ];
        } catch (RequestException $e) {
            Log::error('Claude API request failed during article generation', [
                'status' => $e->response->status(),
                'response' => $e->response->json(),
                'error' => $e->getMessage(),
            ]);
            
            throw new ContentGenerationException(
                'Failed to generate article content: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        } catch (ConnectionException $e) {
            Log::error('Claude API connection failed during article generation', [
                'error' => $e->getMessage(),
            ]);
            
            throw new ApiConnectionException(
                'Failed to connect to Claude API: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        } catch (Throwable $e) {
            Log::error('Unexpected error during article generation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw new ContentGenerationException(
                'Unexpected error during content generation: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function analyzeContent(
        string $content,
        array $targetKeywords = [],
        array $seoParams = []
    ): array {
        $model = $seoParams['model'] ?? $this->defaultModel;
        
        try {
            Log::info('Analyzing content', [
                'content_length' => strlen($content),
                'target_keywords' => $targetKeywords,
                'seo_params' => array_keys($seoParams),
            ]);
            
            $systemPrompt = "You are an expert content analyst specializing in SEO optimization and readability. "
                . "Provide detailed analysis of the content with concrete suggestions for improvement.";
            
            $userPrompt = "Analyze the following content for readability, SEO, and engagement. "
                . "Focus on these keywords: " . implode(', ', $targetKeywords) . ".\n\n"
                . "CONTENT TO ANALYZE:\n" . $content . "\n\n"
                . "Provide analysis in JSON format with these sections: "
                . "readability_score, keyword_usage, content_structure, seo_recommendations, and improvement_suggestions.";
            
            $response = $this->makeApiRequest('messages', 'POST', [
                'model' => $model,
                'max_tokens' => 2000,
                'temperature' => 0.2,
                'system' => $systemPrompt,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $userPrompt,
                    ],
                ],
                'response_format' => ['type' => 'json_object'],
            ]);
            
            // Extrair e decodificar a resposta JSON
            $analysisJson = $response['content'][0]['text'] ?? '{}';
            $analysis = json_decode($analysisJson, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new ContentAnalysisException('Failed to parse analysis JSON: ' . json_last_error_msg());
            }
            
            Log::info('Content analysis completed', [
                'readability_score' => $analysis['readability_score'] ?? 'N/A',
                'keyword_count' => count($analysis['keyword_usage'] ?? []),
            ]);
            
            return [
                'analysis' => $analysis,
                'metadata' => [
                    'model' => $model,
                    'analyzed_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                    'content_length' => strlen($content),
                    'target_keywords' => $targetKeywords,
                    'token_usage' => $response['usage'] ?? [],
                ],
            ];
        } catch (RequestException $e) {
            Log::error('Claude API request failed during content analysis', [
                'status' => $e->response->status(),
                'response' => $e->response->json(),
                'error' => $e->getMessage(),
            ]);
            
            throw new ContentAnalysisException(
                'Failed to analyze content: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        } catch (ConnectionException $e) {
            Log::error('Claude API connection failed during content analysis', [
                'error' => $e->getMessage(),
            ]);
            
            throw new ApiConnectionException(
                'Failed to connect to Claude API: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        } catch (ContentAnalysisException $e) {
            // Re-throw custom exception
            throw $e;
        } catch (Throwable $e) {
            Log::error('Unexpected error during content analysis', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw new ContentAnalysisException(
                'Unexpected error during content analysis: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function generateAlternativeTitles(
        string $content,
        string $currentTitle = '',
        int $count = 5
    ): array {
        try {
            Log::info('Generating alternative titles', [
                'content_length' => strlen($content),
                'current_title' => $currentTitle,
                'count' => $count,
            ]);
            
            $model = 'claude-3-haiku-20240307'; // Usando um modelo mais leve para esta tarefa
            
            $systemPrompt = "You are an expert copywriter specializing in creating engaging article titles.";
            
            $userPrompt = "Based on the content below, generate {$count} alternative titles that are engaging, "
                . "SEO-friendly, and accurately reflect the content.";
            
            if (!empty($currentTitle)) {
                $userPrompt .= " The current title is: \"{$currentTitle}\". Your suggestions should be different but maintain the essence.";
            }
            
            $userPrompt .= "\n\nCONTENT:\n" . substr($content, 0, 2000) . "...";
            $userPrompt .= "\n\nProvide exactly {$count} titles in a numbered list format.";
            
            $response = $this->makeApiRequest('messages', 'POST', [
                'model' => $model,
                'max_tokens' => 1000,
                'temperature' => 0.8,
                'system' => $systemPrompt,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $userPrompt,
                    ],
                ],
            ]);
            
            // Extrair títulos da resposta usando regex
            $titlesText = $response['content'][0]['text'] ?? '';
            preg_match_all('/^\s*\d+\.?\s*(.+)$/m', $titlesText, $matches);
            
            $titles = $matches[1] ?? [];
            
            // Garantir que temos exatamente o número solicitado de títulos
            $titles = array_slice($titles, 0, $count);
            
            Log::info('Alternative titles generated', [
                'count' => count($titles),
                'model' => $model,
            ]);
            
            return $titles;
        } catch (RequestException $e) {
            Log::error('Claude API request failed during title generation', [
                'status' => $e->response->status(),
                'response' => $e->response->json(),
                'error' => $e->getMessage(),
            ]);
            
            throw new TitleGenerationException(
                'Failed to generate alternative titles: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        } catch (ConnectionException $e) {
            Log::error('Claude API connection failed during title generation', [
                'error' => $e->getMessage(),
            ]);
            
            throw new ApiConnectionException(
                'Failed to connect to Claude API: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        } catch (Throwable $e) {
            Log::error('Unexpected error during title generation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw new TitleGenerationException(
                'Unexpected error during title generation: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function humanizeContent(
        string $content,
        HumanPersona $persona,
        ?BrazilianLocation $location = null,
        array $options = []
    ): string {
        $model = $options['model'] ?? $this->defaultModel;
        $preserveStructure = $options['preserve_structure'] ?? true;
        
        try {
            Log::info('Humanizing content', [
                'content_length' => strlen($content),
                'persona_id' => $persona->id,
                'location_id' => $location?->id,
                'preserve_structure' => $preserveStructure,
            ]);
            
            // Prepare persona context
            $personaContext = $this->buildPersonaContext($persona, $location);
            
            $systemPrompt = "You are a content humanizer. Your task is to rewrite the provided content from the "
                . "perspective of the described persona, making it sound more natural and human-written. "
                . "Maintain the original meaning and core information but adjust the tone and style.";
            
            $userPrompt = "PERSONA INFORMATION:\n{$personaContext}\n\n"
                . "CONTENT TO HUMANIZE:\n{$content}\n\n"
                . "Rewrite this content from the perspective of the described persona. "
                . ($preserveStructure ? "Maintain the original structure including headers, lists, and paragraphs. " : "")
                . "Make it sound like a real person with the given characteristics wrote it.";
            
            $response = $this->makeApiRequest('messages', 'POST', [
                'model' => $model,
                'max_tokens' => 4000,
                'temperature' => 0.7,
                'system' => $systemPrompt,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $userPrompt,
                    ],
                ],
            ]);
            
            $humanizedContent = $response['content'][0]['text'] ?? '';
            
            Log::info('Content humanized successfully', [
                'original_length' => strlen($content),
                'humanized_length' => strlen($humanizedContent),
                'model' => $model,
            ]);
            
            return $humanizedContent;
        } catch (RequestException $e) {
            Log::error('Claude API request failed during content humanization', [
                'status' => $e->response->status(),
                'response' => $e->response->json(),
                'error' => $e->getMessage(),
            ]);
            
            throw new ContentHumanizationException(
                'Failed to humanize content: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        } catch (ConnectionException $e) {
            Log::error('Claude API connection failed during content humanization', [
                'error' => $e->getMessage(),
            ]);
            
            throw new ApiConnectionException(
                'Failed to connect to Claude API: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        } catch (Throwable $e) {
            Log::error('Unexpected error during content humanization', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw new ContentHumanizationException(
                'Unexpected error during content humanization: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function extractInsightsFromDiscussions(
        array $discussions,
        string $context,
        int $maxInsights = 5
    ): array {
        try {
            if (empty($discussions)) {
                return ['insights' => [], 'metadata' => ['count' => 0]];
            }
            
            Log::info('Extracting insights from discussions', [
                'discussions_count' => count($discussions),
                'context' => substr($context, 0, 100) . '...',
                'max_insights' => $maxInsights,
            ]);
            
            $model = 'claude-3-sonnet-20240229'; // Modelo equilibrado para esta tarefa
            
            // Para discussões grandes, processamos em paralelo
            if (count($discussions) > 2) {
                return $this->extractInsightsInParallel($discussions, $context, $maxInsights, $model);
            }
            
            $systemPrompt = "You are a discussion analyst. Extract the most relevant insights from forum discussions "
                . "related to a specific context. Focus on unique, valuable information.";
            
            $userPrompt = "CONTEXT:\n{$context}\n\n"
                . "FORUM DISCUSSIONS:\n";
            
            $indexSum = $index + 1;

            foreach ($discussions as $index => $discussion) {
                $userPrompt .= "[Discussion {$indexSum}: {$discussion->getTitle()}]\n"
                    . substr($discussion->getContent(), 0, 1000) . "...\n\n";
            }
            
            $userPrompt .= "Extract up to {$maxInsights} key insights from these discussions that are relevant to the context. "
                . "For each insight, include:\n"
                . "1. The insight itself\n"
                . "2. The source discussion number\n"
                . "3. Relevance score (0-100)\n"
                . "4. A brief explanation of why it's relevant\n\n"
                . "Format as a JSON array of insights.";
            
            $response = $this->makeApiRequest('messages', 'POST', [
                'model' => $model,
                'max_tokens' => 2000,
                'temperature' => 0.2,
                'system' => $systemPrompt,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $userPrompt,
                    ],
                ],
                'response_format' => ['type' => 'json_object'],
            ]);
            
            // Extrair e decodificar a resposta JSON
            $insightsJson = $response['content'][0]['text'] ?? '{}';
            $insights = json_decode($insightsJson, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new InsightExtractionException('Failed to parse insights JSON: ' . json_last_error_msg());
            }
            
            Log::info('Insights extraction completed', [
                'insights_count' => count($insights['insights'] ?? []),
            ]);
            
            return [
                'insights' => $insights['insights'] ?? [],
                'metadata' => [
                    'model' => $model,
                    'extracted_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                    'context' => $context,
                    'discussions_count' => count($discussions),
                    'token_usage' => $response['usage'] ?? [],
                ],
            ];
        } catch (RequestException $e) {
            Log::error('Claude API request failed during insights extraction', [
                'status' => $e->response->status(),
                'response' => $e->response->json(),
                'error' => $e->getMessage(),
            ]);
            
            throw new InsightExtractionException(
                'Failed to extract insights: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        } catch (ConnectionException $e) {
            Log::error('Claude API connection failed during insights extraction', [
                'error' => $e->getMessage(),
            ]);
            
            throw new ApiConnectionException(
                'Failed to connect to Claude API: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        } catch (Throwable $e) {
            Log::error('Unexpected error during insights extraction', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw new InsightExtractionException(
                'Unexpected error during insights extraction: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function generateFaqFromContent(
        string $content,
        int $count = 5,
        bool $includeAnswers = true
    ): array {
        try {
            Log::info('Generating FAQ from content', [
                'content_length' => strlen($content),
                'count' => $count,
                'include_answers' => $includeAnswers,
            ]);
            
            $model = 'claude-3-haiku-20240307'; // Modelo mais leve para esta tarefa
            
            $systemPrompt = "You are a FAQ specialist. Generate concise, relevant FAQs based on article content.";
            
            $userPrompt = "Based on the following article content, generate {$count} frequently asked questions"
                . ($includeAnswers ? " with answers" : "") . " that readers might have.\n\n"
                . "ARTICLE CONTENT:\n" . substr($content, 0, 3000) . "...\n\n"
                . "Generate exactly {$count} questions" . ($includeAnswers ? " with answers" : "") . " in JSON format. "
                . "Each item should have a 'question' field" . ($includeAnswers ? " and an 'answer' field" : "") . ".";
            
            $response = $this->makeApiRequest('messages', 'POST', [
                'model' => $model,
                'max_tokens' => 2000,
                'temperature' => 0.4,
                'system' => $systemPrompt,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $userPrompt,
                    ],
                ],
                'response_format' => ['type' => 'json_object'],
            ]);
            
            // Extrair e decodificar a resposta JSON
            $faqJson = $response['content'][0]['text'] ?? '{}';
            $faqData = json_decode($faqJson, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new FaqGenerationException('Failed to parse FAQ JSON: ' . json_last_error_msg());
            }
            
            $faqs = $faqData['faqs'] ?? [];
            
            // Garantir que temos exatamente o número solicitado de FAQs
            $faqs = array_slice($faqs, 0, $count);
            
            Log::info('FAQs generated successfully', [
                'faq_count' => count($faqs),
                'model' => $model,
            ]);
            
            return [
                'faqs' => $faqs,
                'metadata' => [
                    'model' => $model,
                    'generated_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                    'content_length' => strlen($content),
                    'token_usage' => $response['usage'] ?? [],
                ],
            ];
        } catch (RequestException $e) {
            Log::error('Claude API request failed during FAQ generation', [
                'status' => $e->response->status(),
                'response' => $e->response->json(),
                'error' => $e->getMessage(),
            ]);
            
            throw new FaqGenerationException(
                'Failed to generate FAQs: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        } catch (ConnectionException $e) {
            Log::error('Claude API connection failed during FAQ generation', [
                'error' => $e->getMessage(),
            ]);
            
            throw new ApiConnectionException(
                'Failed to connect to Claude API: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        } catch (Throwable $e) {
            Log::error('Unexpected error during FAQ generation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw new FaqGenerationException(
                'Unexpected error during FAQ generation: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function verifyContentGuidelines(
        string $content,
        array $guidelines = []
    ): array {
        try {
            Log::info('Verifying content guidelines', [
                'content_length' => strlen($content),
                'guidelines_count' => count($guidelines),
            ]);
            
            $model = 'claude-3-sonnet-20240229'; // Modelo equilibrado para esta tarefa
            
            $systemPrompt = "You are a content reviewer specialized in checking content against guidelines. "
                . "Your analysis must be thorough, fair, and focused on constructive feedback.";
            
            $userPrompt = "Verify the following content against quality guidelines and provide detailed feedback.\n\n"
                . "CONTENT TO VERIFY:\n" . $content . "\n\n";
            
            if (!empty($guidelines)) {
                $userPrompt .= "SPECIFIC GUIDELINES TO CHECK:\n";
                foreach ($guidelines as $index => $guideline) {
                    $userPrompt .= ($index + 1) . ". " . $guideline . "\n";
                }
                $userPrompt .= "\n";
            } else {
                $userPrompt .= "Check against standard content quality guidelines including accuracy, originality, "
                    . "readability, grammar, formatting consistency, and appropriate tone.\n\n";
            }
            
            $userPrompt .= "Provide a detailed analysis in JSON format including:\n"
                . "1. overall_compliance (0-100 score)\n"
                . "2. issues (array of specific problems found)\n"
                . "3. recommendations (specific suggestions to fix issues)\n"
                . "4. strengths (positive aspects of the content)";
            
            $response = $this->makeApiRequest('messages', 'POST', [
                'model' => $model,
                'max_tokens' => 2000,
                'temperature' => 0.2,
                'system' => $systemPrompt,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $userPrompt,
                    ],
                ],
                'response_format' => ['type' => 'json_object'],
            ]);
            
            // Extrair e decodificar a resposta JSON
            $verificationJson = $response['content'][0]['text'] ?? '{}';
            $verification = json_decode($verificationJson, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new ContentVerificationException('Failed to parse verification JSON: ' . json_last_error_msg());
            }
            
            Log::info('Content verification completed', [
                'overall_compliance' => $verification['overall_compliance'] ?? 'N/A',
                'issues_count' => count($verification['issues'] ?? []),
            ]);
            
            return [
                'verification' => $verification,
                'metadata' => [
                    'model' => $model,
                    'verified_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                    'content_length' => strlen($content),
                    'token_usage' => $response['usage'] ?? [],
                ],
            ];
        } catch (RequestException $e) {
            Log::error('Claude API request failed during content verification', [
                'status' => $e->response->status(),
                'response' => $e->response->json(),
                'error' => $e->getMessage(),
            ]);

            throw new ContentVerificationException(
                'Failed to verify content: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        } catch (ConnectionException $e) {
            Log::error('Claude API connection failed during content verification', [
                'error' => $e->getMessage(),
            ]);
            
            throw new ApiConnectionException(
                'Failed to connect to Claude API: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        } catch (Throwable $e) {
            Log::error('Unexpected error during content verification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw new ContentVerificationException(
                'Unexpected error during content verification: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getApiStatus(): array
    {
        try {
            Log::info('Checking Claude API status');
            
            // Endpoint para verificar o status da API - adeque conforme a API real
            $response = $this->httpClient->get($this->config->getBaseUrl() . '/models');
            
            if ($response->successful()) {
                $models = $response->json('models') ?? [];
                $availableModels = [];
                
                foreach ($models as $model) {
                    $availableModels[] = [
                        'id' => $model['id'] ?? 'unknown',
                        'name' => $model['name'] ?? $model['id'] ?? 'unknown',
                        'max_tokens' => $model['context_window'] ?? 0,
                        'description' => $model['description'] ?? '',
                    ];
                }
                
                $result = [
                    'status' => 'available',
                    'models' => $availableModels,
                    'current_model' => $this->defaultModel,
                    'checked_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                ];
                
                Log::info('Claude API status check successful', [
                    'status' => 'available',
                    'models_count' => count($availableModels),
                ]);
                
                return $result;
            }
            
            Log::warning('Claude API status check returned unexpected response', [
                'status_code' => $response->status(),
                'response' => $response->json(),
            ]);
            
            return [
                'status' => 'unknown',
                'message' => 'Unexpected response from API',
                'status_code' => $response->status(),
                'checked_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            ];
        } catch (RequestException $e) {
            Log::error('Claude API request failed during status check', [
                'status' => $e->response->status(),
                'response' => $e->response->json(),
                'error' => $e->getMessage(),
            ]);
            
            return [
                'status' => 'unavailable',
                'message' => 'API request failed: ' . $e->getMessage(),
                'status_code' => $e->response->status(),
                'checked_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            ];
        } catch (ConnectionException $e) {
            Log::error('Claude API connection failed during status check', [
                'error' => $e->getMessage(),
            ]);
            
            return [
                'status' => 'unavailable',
                'message' => 'Connection error: ' . $e->getMessage(),
                'checked_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            ];
        } catch (Throwable $e) {
            Log::error('Unexpected error during API status check', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return [
                'status' => 'error',
                'message' => 'Unexpected error: ' . $e->getMessage(),
                'checked_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            ];
        }
    }

    /**
     * Método auxiliar para fazer requisições à API Claude
     * 
     * @param string $endpoint Endpoint da API
     * @param string $method Método HTTP (GET, POST, etc.)
     * @param array<string, mixed> $payload Dados da requisição
     * @return array<string, mixed> Resposta da API
     * 
     * @throws RequestException Em caso de erro na requisição
     * @throws ConnectionException Em caso de erro de conexão
     */
    private function makeApiRequest(string $endpoint, string $method, array $payload = []): array
    {
        $url = $this->config->getBaseUrl() . '/' . $endpoint;
        
        $response = match (strtoupper($method)) {
            'GET' => $this->httpClient->get($url, $payload),
            'POST' => $this->httpClient->post($url, $payload),
            'PUT' => $this->httpClient->put($url, $payload),
            'DELETE' => $this->httpClient->delete($url, $payload),
            default => throw new \InvalidArgumentException("Unsupported HTTP method: {$method}"),
        };
        
        if (!$response->successful()) {
            $errorMessage = $response->json('error.message') ?? 'Unknown API error';
            $errorType = $response->json('error.type') ?? 'api_error';
            
            Log::error('Claude API request failed', [
                'url' => $url,
                'method' => $method,
                'status' => $response->status(),
                'error_type' => $errorType,
                'error_message' => $errorMessage,
            ]);
            
            throw new RequestException($response);
        }
        
        return $response->json();
    }

    /**
     * Constrói o prompt do sistema com base na persona e localização
     * 
     * @param HumanPersona|null $persona Persona para contextualização
     * @param BrazilianLocation|null $location Localização para contextualização
     * @param VehicleReference|null $vehicle Veículo para contextualização
     * @return string Prompt do sistema
     */
    private function buildSystemPrompt(
        ?HumanPersona $persona,
        ?BrazilianLocation $location,
        ?VehicleReference $vehicle = null
    ): string {
        $prompt = "You are an expert article writer specializing in creating high-quality, engaging content "
            . "that feels naturally written by a human.";
        
        if ($persona !== null) {
            $prompt .= " Write from the perspective of {$persona->getName()}, a {$persona->getProfession()}.";
        }
        
        if ($location !== null) {
            $prompt .= " Include context relevant to {$location->getFullLocationName()} in Brazil.";
        }
        
        if ($vehicle !== null) {
            $prompt .= " Your content should display knowledge about {$vehicle->fullDescription()}.";
        }
        
        $prompt .= " The content should be well-structured, informative, and conversational, "
            . "as if written by a real person with expertise in the subject matter.";
        
        return $prompt;
    }

    /**
     * Constrói um prompt para geração de artigo
     * 
     * @param string $context Contexto do artigo
     * @param array<string> $keywords Palavras-chave para o artigo
     * @param array<array<string, string>> $insights Insights de discussões
     * @return string Prompt completo
     */
    private function buildArticleGenerationPrompt(
        string $context,
        array $keywords,
        array $insights = []
    ): string {
        $prompt = "Generate a comprehensive article about the following topic:\n\n"
            . "TOPIC: {$context}\n\n";
        
        if (!empty($keywords)) {
            $prompt .= "KEY TERMS TO INCLUDE: " . implode(', ', $keywords) . "\n\n";
        }
        
        if (!empty($insights)) {
            $prompt .= "INCORPORATE THESE INSIGHTS FROM REAL DISCUSSIONS:\n";
            foreach ($insights as $index => $insight) {
                $prompt .= ($index + 1) . ". \"{$insight['title']}\" - {$insight['content']}\n";
            }
            $prompt .= "\n";
        }
        
        $prompt .= "INSTRUCTIONS:\n"
            . "1. Start with an engaging introduction\n"
            . "2. Include appropriate headers to structure the content\n"
            . "3. Write detailed, informative paragraphs\n"
            . "4. Include a meaningful conclusion\n"
            . "5. Ensure the article sounds natural and conversational\n"
            . "6. Use Brazilian Portuguese language conventions and terminology\n";
        
        return $prompt;
    }

    /**
     * Constrói a descrição de uma persona para uso nos prompts
     * 
     * @param HumanPersona $persona Persona a ser descrita
     * @param BrazilianLocation|null $location Localização da persona
     * @return string Descrição textual da persona
     */
    private function buildPersonaContext(HumanPersona $persona, ?BrazilianLocation $location = null): string
    {
        $context = "Name: {$persona->getName()}\n"
            . "Profession: {$persona->getProfession()}\n";
        
        if ($location !== null) {
            $context .= "Location: {$location->getFullLocationName()}\n"
                . "Region: {$location->getRegion()}\n"
                . "Traffic Pattern: {$location->getTrafficPattern()->value}\n";
        } else {
            $context .= "Location: {$persona->getLocation()}\n";
        }
        
        $vehicles = $persona->getPreferredVehicles();
        if (!empty($vehicles)) {
            $context .= "Preferred Vehicles: " . implode(', ', $vehicles) . "\n";
        }
        
        return $context;
    }

    /**
     * Extrai o título do conteúdo gerado
     * 
     * @param string $content Conteúdo do artigo
     * @return string Título extraído ou título genérico
     */
    private function extractTitleFromContent(string $content): string
    {
        // Tentar encontrar um título no formato de cabeçalho H1
        if (preg_match('/^#\s+(.+?)$/m', $content, $matches)) {
            return trim($matches[1]);
        }
        
        // Tentar encontrar a primeira linha não vazia como título
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                return $line;
            }
        }
        
        // Título genérico se nenhum título for encontrado
        return "Artigo Gerado";
    }

    /**
     * Processa discussões em paralelo para extração de insights
     * 
     * @param array<ForumDiscussion> $discussions Discussões para análise
     * @param string $context Contexto para relevância
     * @param int $maxInsights Número máximo de insights
     * @param string $model Modelo da Claude a ser usado
     * @return array<string, mixed> Insights consolidados
     * 
     * @throws InsightExtractionException Em caso de erro na extração
     */
    private function extractInsightsInParallel(
        array $discussions,
        string $context,
        int $maxInsights,
        string $model
    ): array {
        // Dividir as discussões em grupos menores
        $chunks = array_chunk($discussions, 2);
        $systemPrompt = "You are a discussion analyst. Extract the most relevant insights from forum discussions "
            . "related to a specific context. Focus on unique, valuable information.";
        
        $responses = Http::pool(function (Pool $pool) use ($chunks, $context, $model, $systemPrompt) {
            $requests = [];
            
            foreach ($chunks as $index => $discussionChunk) {
                $userPrompt = "CONTEXT:\n{$context}\n\n"
                    . "FORUM DISCUSSIONS:\n";
                
                

                foreach ($discussionChunk as $dIndex => $discussion) {
                    $dIndexSum =  + $dIndex + 1;
                    $userPrompt .= "[Discussion {$dIndexSum}: {$discussion->getTitle()}]\n"
                        . substr($discussion->getContent(), 0, 1000) . "...\n\n";
                }
                
                $userPrompt .= "Extract key insights from these discussions that are relevant to the context. "
                    . "For each insight, include:\n"
                    . "1. The insight itself\n"
                    . "2. The source discussion number\n"
                    . "3. Relevance score (0-100)\n"
                    . "4. A brief explanation of why it's relevant\n\n"
                    . "Format as a JSON array of insights.";
                
                $payload = [
                    'model' => $model,
                    'max_tokens' => 1500,
                    'temperature' => 0.2,
                    'system' => $systemPrompt,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $userPrompt,
                        ],
                    ],
                    'response_format' => ['type' => 'json_object'],
                ];
                
                $requests[] = $pool->withToken($this->config->getApiKey())
                    ->withHeaders([
                        'anthropic-version' => $this->config->getApiVersion(),
                        'Content-Type' => 'application/json',
                    ])
                    ->post($this->config->getBaseUrl() . '/messages', $payload);
            }
            
            return $requests;
        });
        
        // Consolidar os resultados
        $allInsights = [];
        $tokenUsage = ['input_tokens' => 0, 'output_tokens' => 0];
        
        foreach ($responses as $response) {
            if (!$response->successful()) {
                continue;
            }
            
            $data = $response->json();
            $insightsJson = $data['content'][0]['text'] ?? '{}';
            $insightData = json_decode($insightsJson, true);
            
            if (json_last_error() === JSON_ERROR_NONE && isset($insightData['insights'])) {
                $allInsights = array_merge($allInsights, $insightData['insights']);
            }
            
            // Contabilizar uso de tokens
            if (isset($data['usage'])) {
                $tokenUsage['input_tokens'] += $data['usage']['input_tokens'] ?? 0;
                $tokenUsage['output_tokens'] += $data['usage']['output_tokens'] ?? 0;
            }
        }
        
        // Ordenar todos os insights por relevância
        usort($allInsights, function ($a, $b) {
            return ($b['relevance_score'] ?? 0) <=> ($a['relevance_score'] ?? 0);
        });
        
        // Limitar ao número máximo de insights
        $allInsights = array_slice($allInsights, 0, $maxInsights);
        
        Log::info('Parallel insights extraction completed', [
            'chunks_processed' => count($chunks),
            'insights_found' => count($allInsights),
        ]);
        
        return [
            'insights' => $allInsights,
            'metadata' => [
                'model' => $model,
                'extracted_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                'context' => $context,
                'discussions_count' => count($discussions),
                'chunks_count' => count($chunks),
                'token_usage' => $tokenUsage,
            ],
        ];
    }
}