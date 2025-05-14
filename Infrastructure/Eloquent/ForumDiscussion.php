<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Infrastructure\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

/**
 * Modelo Eloquent para a tabela forum_discussions
 * 
 * @property string $id ID único da discussão
 * @property string $title Título da discussão
 * @property string $content Conteúdo da discussão
 * @property array $tags Tags relacionadas à discussão (JSON)
 * @property string $forum_url URL do fórum original
 * @property string $category Categoria da discussão (enum como string)
 * @property Carbon $published_at Data de publicação
 * @property int $usage_count Contador de uso
 * @property Carbon|null $last_used_at Data da última utilização
 * @property int $relevance_score Pontuação de relevância
 * @property int $view_count Número de visualizações (opcional)
 * @property int $reply_count Número de respostas (opcional)
 * @property Carbon $created_at Data de criação
 * @property Carbon $updated_at Data de atualização
 */
class ForumDiscussion extends Model
{
    use HasFactory;

    /**
     * A tabela associada ao modelo
     *
     * @var string
     */
    protected $table = 'forum_discussions';

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
        'title',
        'content',
        'tags',
        'forum_url',
        'category',
        'published_at',
        'usage_count',
        'last_used_at',
        'relevance_score',
        'view_count',
        'reply_count'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tags' => 'array',
        'published_at' => 'datetime',
        'usage_count' => 'integer',
        'last_used_at' => 'datetime',
        'relevance_score' => 'integer',
        'view_count' => 'integer',
        'reply_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * As categorias de discussão disponíveis
     *
     * @var array<string>
     */
    public const CATEGORIES = [
        'maintenance',
        'performance',
        'modification',
        'troubleshooting',
        'purchase',
        'comparison',
        'news',
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
            
            if ($model->relevance_score === null) {
                $model->relevance_score = 0;
            }
            
            if ($model->published_at === null) {
                $model->published_at = now();
            }
        });
    }
    
    /**
     * Relação com artigos onde esta discussão foi usada
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function articles()
    {
        return $this->belongsToMany(
            Article::class,
            'article_discussion',
            'discussion_id',
            'article_id'
        )->withTimestamps();
    }
    
    /**
     * Escopo para discussões que não foram usadas recentemente
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $days Número de dias para considerar como uso recente
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotRecentlyUsed($query, int $days = 30)
    {
        return $query->where(function ($query) use ($days) {
            $query->whereNull('last_used_at')
                ->orWhere('last_used_at', '<', now()->subDays($days));
        });
    }
    
    /**
     * Escopo para discussões recentes
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $days Número de dias para considerar como recente
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecent($query, int $days = 90)
    {
        return $query->where('published_at', '>=', now()->subDays($days));
    }
    
    /**
     * Escopo para busca por categoria
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $category Categoria a ser buscada
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
    
    /**
     * Escopo para busca por pontuação mínima de relevância
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $minScore Pontuação mínima de relevância
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByMinRelevance($query, int $minScore)
    {
        return $query->where('relevance_score', '>=', $minScore);
    }
    
    /**
     * Escopo para busca por fonte (URL do fórum)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $source Fonte a ser buscada
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBySource($query, string $source)
    {
        return $query->where('forum_url', 'like', "%{$source}%");
    }
    
    /**
     * Escopo para busca por tags
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $tag Tag a ser buscada
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByTag($query, string $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }
    
    /**
     * Escopo para busca por texto no título ou conteúdo
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $searchText Texto a ser buscado
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, string $searchText)
    {
        return $query->where(function ($query) use ($searchText) {
            $query->where('title', 'like', "%{$searchText}%")
                ->orWhere('content', 'like', "%{$searchText}%");
        });
    }
    
    /**
     * Escopo para busca por múltiplas palavras-chave (operação AND)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array<string> $keywords Lista de palavras-chave
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByKeywords($query, array $keywords)
    {
        foreach ($keywords as $keyword) {
            $query->where(function ($query) use ($keyword) {
                $query->where('title', 'like', "%{$keyword}%")
                    ->orWhere('content', 'like', "%{$keyword}%")
                    ->orWhereJsonContains('tags', $keyword);
            });
        }
        
        return $query;
    }
    
    /**
     * Escopo para busca por modelo de veículo
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $make Marca do veículo
     * @param string|null $model Modelo do veículo (opcional)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByVehicle($query, string $make, ?string $model = null)
    {
        $query->where(function ($query) use ($make, $model) {
            $query->where('title', 'like', "%{$make}%")
                ->orWhere('content', 'like', "%{$make}%")
                ->orWhereJsonContains('tags', $make);
        });
        
        if ($model) {
            $query->where(function ($query) use ($model) {
                $query->where('title', 'like', "%{$model}%")
                    ->orWhere('content', 'like', "%{$model}%")
                    ->orWhereJsonContains('tags', $model);
            });
        }
        
        return $query;
    }
    
    /**
     * Escopo para ordenar por relevância
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByRelevance($query)
    {
        return $query->orderBy('relevance_score', 'desc');
    }
    
    /**
     * Escopo para ordenar por popularidade (baseado em views e replies)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByPopularity($query)
    {
        return $query->orderByRaw('(view_count + (reply_count * 5)) DESC');
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
     * Verifica se a discussão é recente
     *
     * @param int $days Número de dias para considerar como recente
     * @return bool
     */
    public function isRecent(int $days = 90): bool
    {
        return $this->published_at->greaterThanOrEqualTo(now()->subDays($days));
    }
    
    /**
     * Verifica se a discussão é relevante para determinadas palavras-chave
     *
     * @param array<string> $keywords Palavras-chave para verificar
     * @return bool
     */
    public function isRelevantFor(array $keywords): bool
    {
        $contentLower = strtolower($this->content);
        $titleLower = strtolower($this->title);
        $tagsLower = array_map('strtolower', $this->tags ?? []);
        
        foreach ($keywords as $keyword) {
            $keywordLower = strtolower($keyword);
            
            if (str_contains($contentLower, $keywordLower) || 
                str_contains($titleLower, $keywordLower) || 
                in_array($keywordLower, $tagsLower)) {
                return true;
            }
        }
        
        return false;
    }
}