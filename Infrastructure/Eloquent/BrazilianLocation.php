<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Infrastructure\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Modelo Eloquent para a tabela brazilian_locations
 * 
 * @property string $id ID único da localização
 * @property string $city Nome da cidade
 * @property string $region Região ou bairro
 * @property string $traffic_pattern Padrão de tráfego (enum como string)
 * @property string $state_code Código do estado (UF)
 * @property int $usage_count Contador de uso
 * @property \Carbon\Carbon|null $last_used_at Data da última utilização
 * @property \Carbon\Carbon $created_at Data de criação
 * @property \Carbon\Carbon $updated_at Data de atualização
 */
class BrazilianLocation extends Model
{
    use HasFactory;

    /**
     * A tabela associada ao modelo
     *
     * @var string
     */
    protected $table = 'brazilian_locations';

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
        'city',
        'region',
        'traffic_pattern',
        'state_code',
        'usage_count',
        'last_used_at'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos
     *
     * @var array<string, string>
     */
    protected $casts = [
        'usage_count' => 'integer',
        'last_used_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Os padrões de tráfego disponíveis
     *
     * @var array<string>
     */
    public const TRAFFIC_PATTERNS = [
        'light',
        'moderate',
        'heavy',
        'congested'
    ];

    /**
     * Os códigos de estados brasileiros
     *
     * @var array<string>
     */
    public const STATE_CODES = [
        'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA',
        'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN',
        'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'
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
        });
    }
    
    /**
     * Relação com artigos onde esta localização foi usada
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'location_id');
    }
    
    /**
     * Escopo para localizações que não foram usadas recentemente
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $days Número de dias para considerar como uso recente
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotRecentlyUsed($query, int $days = 7)
    {
        return $query->where(function ($query) use ($days) {
            $query->whereNull('last_used_at')
                ->orWhere('last_used_at', '<', now()->subDays($days));
        });
    }
    
    /**
     * Escopo para busca por cidade
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $city Cidade a ser buscada
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCity($query, string $city)
    {
        return $query->where('city', 'like', "%{$city}%");
    }
    
    /**
     * Escopo para busca por região
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $region Região a ser buscada
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByRegion($query, string $region)
    {
        return $query->where('region', 'like', "%{$region}%");
    }
    
    /**
     * Escopo para busca por estado (UF)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $stateCode Código do estado
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByState($query, string $stateCode)
    {
        return $query->where('state_code', $stateCode);
    }
    
    /**
     * Escopo para busca por padrão de tráfego
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $trafficPattern Padrão de tráfego
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByTrafficPattern($query, string $trafficPattern)
    {
        return $query->where('traffic_pattern', $trafficPattern);
    }
    
    /**
     * Escopo para ordenar por número de utilizações (ascendente)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByLeastUsed($query)
    {
        return $query->orderBy('usage_count', 'asc')
            ->orderBy('last_used_at', 'asc');
    }
    
    /**
     * Escopo para busca por texto em cidade, região ou estado
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search Texto a ser buscado
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('city', 'like', "%{$search}%")
                ->orWhere('region', 'like', "%{$search}%")
                ->orWhere('state_code', 'like', "%{$search}%");
        });
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
     * Retorna o nome completo da localização (Cidade - UF)
     *
     * @return string
     */
    public function getFullLocationName(): string
    {
        return "{$this->city} - {$this->state_code}";
    }
}