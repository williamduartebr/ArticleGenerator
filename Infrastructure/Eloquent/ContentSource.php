<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Infrastructure\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

/**
 * Modelo Eloquent para a tabela content_sources
 * 
 * @property string $id ID único da fonte
 * @property string $name Nome da fonte de conteúdo
 * @property string $url URL da fonte de conteúdo
 * @property string $type Tipo da fonte (enum como string)
 * @property float $trust_score Pontuação de confiabilidade (0-100)
 * @property array $topics Tópicos principais abordados pela fonte (JSON)
 * @property Carbon|null $last_crawled_at Data da última extração de conteúdo
 * @property bool $is_active Indica se a fonte está ativa para extração
 * @property int $usage_count Contador de uso
 * @property Carbon|null $last_used_at Data da última utilização
 * @property Carbon|null $verified_at Data de verificação (opcional)
 * @property string|null $verified_by Responsável pela verificação (opcional)
 * @property Carbon $created_at Data de criação
 * @property Carbon $updated_at Data de atualização
 */
class ContentSource extends Model
{
    use HasFactory;

    /**
     * A tabela associada ao modelo
     *
     * @var string
     */
    protected $table = 'content_sources';

    /**
     * Indica se o modelo deve ser marcado com timestamps
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * O tipo de ID do modelo
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indica se o ID é auto-incrementado
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Os atributos que são atribuíveis em massa
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'url',
        'type',
        'trust_score',
        'topics',
        'last_crawled_at',
        'is_active',
        'usage_count',
        'last_used_at',
        'verified_at',
        'verified_by'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos
     *
     * @var array<string, string>
     */
    protected $casts = [
        'trust_score' => 'float',
        'topics' => 'array',
        'last_crawled_at' => 'datetime',
        'is_active' => 'boolean',
        'usage_count' => 'integer',
        'last_used_at' => 'datetime',
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Os tipos de fonte disponíveis
     *
     * @var array<string>
     */
    public const SOURCE_TYPES = [
        'forum',
        'social_media',
        'blog',
        'news',
        'review',
        'official',
        'other'
    ];

    /**
     * Hook para eventos de 'creating'
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
            
            if ($model->usage_count === null) {
                $model->usage_count = 0;
            }
            
            if ($model->is_active === null) {
                $model->is_active = true;
            }
            
            if ($model->trust_score === null) {
                $model->trust_score = 50.0; // Pontuação padrão média
            }
        });
    }
    
    /**
     * Escopo para fontes ativas
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Escopo para busca por tipo
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type Tipo a ser buscado
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
    
    /**
     * Escopo para busca por pontuação mínima de confiabilidade
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param float $minScore Pontuação mínima de confiabilidade
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByMinTrustScore($query, float $minScore)
    {
        return $query->where('trust_score', '>=', $minScore);
    }
    
    /**
     * Escopo para fontes que precisam ser extraídas
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $daysThreshold Número de dias para considerar como desatualizado
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNeedsCrawling($query, int $daysThreshold = 7)
    {
        return $query->where('is_active', true)
            ->where(function ($query) use ($daysThreshold) {
                $query->whereNull('last_crawled_at')
                    ->orWhere('last_crawled_at', '<', now()->subDays($daysThreshold));
            });
    }
    
    /**
     * Escopo para busca por tópico
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $topic Tópico a ser buscado
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByTopic($query, string $topic)
    {
        return $query->whereJsonContains('topics', $topic);
    }
    
    /**
     * Escopo para busca por texto no nome ou URL
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search Texto a ser buscado
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('url', 'like', "%{$search}%");
        });
    }
    
    /**
     * Escopo para fontes verificadas
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }
    
    /**
     * Escopo para ordenar por confiabilidade (descendente)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByTrustScore($query)
    {
        return $query->orderBy('trust_score', 'desc');
    }
    
    /**
     * Incrementa o contador de uso e atualiza a data de último uso
     *
     * @return bool
     */
    public function incrementUsage(): bool
    {
        $this->usage_count++;
        $this->last_used_at = now();
        
        return $this->save();
    }
    
    /**
     * Marca a fonte como extraída
     *
     * @return bool
     */
    public function markAsCrawled(): bool
    {
        $this->last_crawled_at = now();
        
        return $this->save();
    }
    
    /**
     * Ativa a fonte para extração
     *
     * @return bool
     */
    public function activate(): bool
    {
        $this->is_active = true;
        
        return $this->save();
    }
    
    /**
     * Desativa a fonte para extração
     *
     * @return bool
     */
    public function deactivate(): bool
    {
        $this->is_active = false;
        
        return $this->save();
    }
    
    /**
     * Verifica se a fonte precisa ser extraída
     *
     * @param int $daysThreshold Número de dias para considerar como desatualizado
     * @return bool
     */
    public function needsCrawling(int $daysThreshold = 7): bool
    {
        if (!$this->is_active) {
            return false;
        }
        
        if ($this->last_crawled_at === null) {
            return true;
        }
        
        return $this->last_crawled_at->lessThan(now()->subDays($daysThreshold));
    }
    
    /**
     * Marca a fonte como verificada
     *
     * @param string $verifiedBy Responsável pela verificação
     * @return bool
     */
    public function markAsVerified(string $verifiedBy): bool
    {
        $this->verified_at = now();
        $this->verified_by = $verifiedBy;
        
        return $this->save();
    }
    
    /**
     * Calcula a pontuação de confiabilidade ponderada com base no uso
     *
     * @return float
     */
    public function getWeightedTrustScore(): float
    {
        $usageMultiplier = min(1.0 + ($this->usage_count / 100), 1.5);
        return $this->trust_score * $usageMultiplier;
    }
    
    /**
     * Verifica se a fonte é relevante para determinados tópicos
     *
     * @param array<string> $targetTopics Tópicos para verificar relevância
     * @return bool
     */
    public function isRelevantFor(array $targetTopics): bool
    {
        $sourceTópicos = $this->topics ?? [];
        
        foreach ($targetTopics as $topic) {
            if (in_array($topic, $sourceTópicos)) {
                return true;
            }
        }
        
        return false;
    }
}