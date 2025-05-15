<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Infrastructure\Config;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;

/**
* Configurações da API Claude
* 
* Esta classe gerencia todas as configurações necessárias para integração
* com a API do modelo de linguagem Claude da Anthropic.
*/
class ClaudeApiConfig
{
   /**
    * Cache TTL em segundos (1 hora)
    */
   private const CACHE_TTL = 3600;

   /**
    * Prefixo para chaves de cache
    */
   private const CACHE_PREFIX = 'claude_api_config:';

   /**
    * Modelos disponíveis da API Claude
    */
   private const AVAILABLE_MODELS = [
       'claude-3-opus-20240229',
       'claude-3-sonnet-20240229',
       'claude-3-haiku-20240307',
       'claude-2.0',
       'claude-2.1',
       'claude-instant-1.2',
   ];

   /**
    * Chave da API Claude
    */
   private string $apiKey;

   /**
    * URL base da API
    */
   private string $baseUrl;

   /**
    * Versão da API
    */
   private string $apiVersion;

   /**
    * Modelo padrão a ser utilizado
    */
   private string $defaultModel;

   /**
    * Temperatura padrão para geração de conteúdo
    */
   private float $defaultTemperature;

   /**
    * Número máximo de tokens padrão
    */
   private int $defaultMaxTokens;

   /**
    * Timeout da requisição em segundos
    */
   private int $requestTimeout;

   /**
    * Número máximo de tentativas para retry
    */
   private int $maxRetryAttempts;

   /**
    * Tempo base de espera para retry em milissegundos
    */
   private int $baseRetryDelayMs;

   /**
    * Limite de requisições por minuto
    */
   private int $rateLimit;

   /**
    * Configurações de modelos específicos
    * 
    * @var array<string, array<string, mixed>>
    */
   private array $modelConfigs;

   /**
    * Indica se o cache deve ser utilizado
    */
   private bool $useCache;

   /**
    * Tempo de expiração do cache em segundos
    */
   private int $cacheTtl;

   /**
    * Construtor
    * 
    * @param CacheRepository|null $cache Repositório de cache
    * 
    * @throws InvalidArgumentException Se as configurações críticas não forem válidas
    */
   public function __construct(private readonly ?CacheRepository $cache = null)
   {
       $this->loadConfigFromEnv();
       $this->validateConfig();
   }

   /**
    * Carrega as configurações do arquivo .env e config
    * 
    * @return void
    */
   private function loadConfigFromEnv(): void
   {
       // Configurações principais da API
       $this->apiKey = env('CLAUDE_API_KEY', '');
       $this->baseUrl = env('CLAUDE_API_BASE_URL', 'https://api.anthropic.com/v1');
       $this->apiVersion = env('CLAUDE_API_VERSION', '2023-06-01');
       $this->defaultModel = env(
           'CLAUDE_DEFAULT_MODEL',
           'claude-3-opus-20240229'
       );

       // Parâmetros de geração de conteúdo
       $this->defaultTemperature = (float) env('CLAUDE_DEFAULT_TEMPERATURE', '0.7');
       $this->defaultMaxTokens = (int) env('CLAUDE_DEFAULT_MAX_TOKENS', '4000');

       // Configurações de timeout e retry
       $this->requestTimeout = (int) env('CLAUDE_REQUEST_TIMEOUT', '60');
       $this->maxRetryAttempts = (int) env('CLAUDE_MAX_RETRY_ATTEMPTS', '3');
       $this->baseRetryDelayMs = (int) env('CLAUDE_BASE_RETRY_DELAY_MS', '1000');

       // Rate limiting
       $this->rateLimit = (int) env('CLAUDE_RATE_LIMIT', '60');

       // Configurações de cache
       $this->useCache = (bool) env('CLAUDE_USE_CACHE', 'true');
       $this->cacheTtl = (int) env('CLAUDE_CACHE_TTL', (string) self::CACHE_TTL);

       // Carrega configurações específicas para cada modelo a partir do arquivo de configuração
       $this->loadModelConfigs();
   }

   /**
    * Carrega configurações específicas para cada modelo
    * 
    * @return void
    */
   private function loadModelConfigs(): void
   {
       // Valores padrão para cada modelo
       $defaultModelConfigs = [
           'claude-3-opus-20240229' => [
               'max_tokens' => 4096,
               'temperature' => 0.7,
               'top_p' => 1.0,
               'context_window' => 200000,
               'description' => 'Claude 3 Opus - Most powerful model for complex tasks',
           ],
           'claude-3-sonnet-20240229' => [
               'max_tokens' => 4096,
               'temperature' => 0.7,
               'top_p' => 1.0,
               'context_window' => 180000,
               'description' => 'Claude 3 Sonnet - Balanced model for most tasks',
           ],
           'claude-3-haiku-20240307' => [
               'max_tokens' => 4096,
               'temperature' => 0.7,
               'top_p' => 1.0,
               'context_window' => 150000,
               'description' => 'Claude 3 Haiku - Fastest model for simple tasks',
           ],
           'claude-2.0' => [
               'max_tokens' => 4096,
               'temperature' => 0.7,
               'top_p' => 1.0,
               'context_window' => 100000,
               'description' => 'Claude 2.0 - Legacy model',
           ],
           'claude-2.1' => [
               'max_tokens' => 4096,
               'temperature' => 0.7,
               'top_p' => 1.0,
               'context_window' => 100000,
               'description' => 'Claude 2.1 - Legacy model with improvements',
           ],
           'claude-instant-1.2' => [
               'max_tokens' => 4096,
               'temperature' => 0.7,
               'top_p' => 1.0,
               'context_window' => 100000,
               'description' => 'Claude Instant 1.2 - Fast legacy model',
           ],
       ];

       // Carrega configurações customizadas do arquivo de configuração
       $customModelConfigs = Config::get('claude.models', []);

       // Mescla as configurações padrão com as customizadas
       $this->modelConfigs = array_merge($defaultModelConfigs, $customModelConfigs);
   }

   /**
    * Valida as configurações críticas
    * 
    * @return void
    * 
    * @throws InvalidArgumentException Se as configurações críticas não forem válidas
    */
   private function validateConfig(): void
   {
       // Validação da API Key
       if (empty($this->apiKey)) {
           throw new InvalidArgumentException(
               'Claude API Key is required. Set CLAUDE_API_KEY in your .env file.'
           );
       }

       // Validação da URL base
       if (empty($this->baseUrl) || !filter_var($this->baseUrl, FILTER_VALIDATE_URL)) {
           throw new InvalidArgumentException(
               'Invalid Claude API Base URL. Set CLAUDE_API_BASE_URL in your .env file.'
           );
       }

       // Validação do modelo padrão
       if (!$this->isValidModel($this->defaultModel)) {
           throw new InvalidArgumentException(
               "Invalid default model: {$this->defaultModel}. Available models: " . implode(', ', self::AVAILABLE_MODELS)
           );
       }

       // Validação de temperatura
       if ($this->defaultTemperature < 0.0 || $this->defaultTemperature > 1.0) {
           throw new InvalidArgumentException(
               "Invalid temperature value: {$this->defaultTemperature}. Must be between 0.0 and 1.0."
           );
       }

       // Validação de max tokens
       if ($this->defaultMaxTokens <= 0) {
           throw new InvalidArgumentException(
               "Invalid max tokens value: {$this->defaultMaxTokens}. Must be greater than 0."
           );
       }

       // Validação de timeout
       if ($this->requestTimeout <= 0) {
           throw new InvalidArgumentException(
               "Invalid request timeout: {$this->requestTimeout}. Must be greater than 0."
           );
       }

       // Validação de max retry attempts
       if ($this->maxRetryAttempts < 0) {
           throw new InvalidArgumentException(
               "Invalid max retry attempts: {$this->maxRetryAttempts}. Must be 0 or greater."
           );
       }

       // Validação de base retry delay
       if ($this->baseRetryDelayMs <= 0) {
           throw new InvalidArgumentException(
               "Invalid base retry delay: {$this->baseRetryDelayMs}. Must be greater than 0."
           );
       }

       // Validação de rate limit
       if ($this->rateLimit <= 0) {
           throw new InvalidArgumentException(
               "Invalid rate limit: {$this->rateLimit}. Must be greater than 0."
           );
       }
   }

   /**
    * Verifica se um modelo é válido
    * 
    * @param string $model Nome do modelo
    * @return bool
    */
   public function isValidModel(string $model): bool
   {
       return in_array($model, self::AVAILABLE_MODELS, true);
   }

   /**
    * Obtém a chave de API
    * 
    * @return string
    */
   public function getApiKey(): string
   {
       return $this->getCachedValue('api_key', $this->apiKey);
   }

   /**
    * Obtém a URL base da API
    * 
    * @return string
    */
   public function getBaseUrl(): string
   {
       return $this->getCachedValue('base_url', $this->baseUrl);
   }

   /**
    * Obtém a versão da API
    * 
    * @return string
    */
   public function getApiVersion(): string
   {
       return $this->getCachedValue('api_version', $this->apiVersion);
   }

   /**
    * Obtém o modelo padrão
    * 
    * @return string
    */
   public function getDefaultModel(): string
   {
       return $this->getCachedValue('default_model', $this->defaultModel);
   }

   /**
    * Obtém a temperatura padrão
    * 
    * @return float
    */
   public function getDefaultTemperature(): float
   {
       return $this->getCachedValue('default_temperature', $this->defaultTemperature);
   }

   /**
    * Obtém o número máximo de tokens padrão
    * 
    * @return int
    */
   public function getDefaultMaxTokens(): int
   {
       return $this->getCachedValue('default_max_tokens', $this->defaultMaxTokens);
   }

   /**
    * Obtém o timeout da requisição em segundos
    * 
    * @return int
    */
   public function getRequestTimeout(): int
   {
       return $this->getCachedValue('request_timeout', $this->requestTimeout);
   }

   /**
    * Obtém o número máximo de tentativas para retry
    * 
    * @return int
    */
   public function getMaxRetryAttempts(): int
   {
       return $this->getCachedValue('max_retry_attempts', $this->maxRetryAttempts);
   }

   /**
    * Obtém o tempo base de espera para retry em milissegundos
    * 
    * @return int
    */
   public function getBaseRetryDelayMs(): int
   {
       return $this->getCachedValue('base_retry_delay_ms', $this->baseRetryDelayMs);
   }

   /**
    * Obtém o limite de requisições por minuto
    * 
    * @return int
    */
   public function getRateLimit(): int
   {
       return $this->getCachedValue('rate_limit', $this->rateLimit);
   }

   /**
    * Obtém a configuração para um modelo específico
    * 
    * @param string $model Nome do modelo
    * @return array<string, mixed>
    * 
    * @throws InvalidArgumentException Se o modelo não for válido
    */
   public function getModelConfig(string $model): array
   {
       if (!$this->isValidModel($model)) {
           throw new InvalidArgumentException("Invalid model: {$model}");
       }

       $cacheKey = "model_config:{$model}";
       
       return $this->getCachedValue($cacheKey, $this->modelConfigs[$model] ?? []);
   }

   /**
    * Obtém a lista de todos os modelos disponíveis
    * 
    * @return array<string>
    */
   public function getAvailableModels(): array
   {
       return $this->getCachedValue('available_models', self::AVAILABLE_MODELS);
   }

   /**
    * Obtém a configuração completa de todos os modelos
    * 
    * @return array<string, array<string, mixed>>
    */
   public function getAllModelConfigs(): array
   {
       return $this->getCachedValue('all_model_configs', $this->modelConfigs);
   }

   /**
    * Verifica se deve utilizar cache
    * 
    * @return bool
    */
   public function shouldUseCache(): bool
   {
       return $this->useCache && $this->cache !== null;
   }

   /**
    * Obtém o tempo de expiração do cache em segundos
    * 
    * @return int
    */
   public function getCacheTtl(): int
   {
       return $this->cacheTtl;
   }

   /**
    * Obtém um valor do cache ou o valor padrão se o cache não estiver disponível
    * 
    * @param string $key Chave do cache
    * @param mixed $defaultValue Valor padrão
    * @return mixed
    */
   private function getCachedValue(string $key, mixed $defaultValue): mixed
   {
       if (!$this->shouldUseCache()) {
           return $defaultValue;
       }

       $cacheKey = self::CACHE_PREFIX . $key;
       
       return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($defaultValue) {
           return $defaultValue;
       });
   }

   /**
    * Limpa o cache de configurações
    * 
    * @return bool
    */
   public function clearCache(): bool
   {
       if (!$this->shouldUseCache()) {
           return false;
       }

       // Lista de todas as chaves a serem limpas
       $keys = [
           'api_key',
           'base_url',
           'api_version',
           'default_model',
           'default_temperature',
           'default_max_tokens',
           'request_timeout',
           'max_retry_attempts',
           'base_retry_delay_ms',
           'rate_limit',
           'available_models',
           'all_model_configs',
       ];

       // Adiciona chaves para cada modelo
       foreach (self::AVAILABLE_MODELS as $model) {
           $keys[] = "model_config:{$model}";
       }

       // Limpa cada chave
       foreach ($keys as $key) {
           $this->cache->forget(self::CACHE_PREFIX . $key);
       }

       return true;
   }
}