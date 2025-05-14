<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Infrastructure\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Modelo Eloquent para a tabela human_personas
 * 
 * @property string $id ID único da persona
 * @property string $first_name Primeiro nome
 * @property string $last_name Sobrenome
 * @property string $profession Profissão
 * @property string $location Localização geográfica
 * @property array $preferred_vehicles Veículos preferidos (JSON)
 * @property int $usage_count Contador de uso
 * @property \Carbon\Carbon|null $last_used_at Data da última utilização
 * @property \Carbon\Carbon $created_at Data de criação
 * @property \Carbon\Carbon $updated_at Data de atualização
 */
class HumanPersona extends Model
{
    use HasFactory;

    /**
     * A tabela associada ao modelo
     *
     * @var string
     */
    protected $table = 'human_personas';

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
        'first_name',
        'last_name',
        'profession',
        'location',
        'preferred_vehicles',
        'usage_count',
        'last_used_at'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos
     *
     * @var array<string, string>
     */
    protected $casts = [
        'preferred_vehicles' => 'array',
        'usage_count' => 'integer',
        'last_used_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Os atributos que devem ser escondidos para arrays
     *
     * @var array<string>
     */
    protected $hidden = [];

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
     * Relação com artigos onde esta persona foi usada
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'persona_id');
    }
    
    /**
     * Escopo para personas que não foram usadas recentemente
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
     * Escopo para personas com um contador de uso máximo
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $maxUsage Contador máximo de uso
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMaxUsageCount($query, int $maxUsage)
    {
        return $query->where('usage_count', '<=', $maxUsage);
    }
    
    /**
     * Escopo para busca por nome completo ou parcial
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $name Nome a ser buscado
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchByName($query, string $name)
    {
        $name = '%' . $name . '%';
        
        return $query->where(function ($query) use ($name) {
            $query->where('first_name', 'like', $name)
                ->orWhere('last_name', 'like', $name)
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", [$name]);
        });
    }
    
    /**
     * Escopo para busca por profissão
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $profession Profissão a ser buscada
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByProfession($query, string $profession)
    {
        return $query->where('profession', 'like', "%{$profession}%");
    }
    
    /**
     * Escopo para busca por localização
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $location Localização a ser buscada
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByLocation($query, string $location)
    {
        return $query->where('location', 'like', "%{$location}%");
    }
    
    /**
     * Escopo para busca por veículo preferido
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $make Marca do veículo
     * @param string|null $model Modelo do veículo (opcional)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByPreferredVehicle($query, string $make, ?string $model = null)
    {
        if ($model) {
            // Busca por marca e modelo
            return $query->whereJsonContains('preferred_vehicles', $make . ' ' . $model)
                ->orWhereJsonContains('preferred_vehicles', $make);
        }
        
        // Busca apenas por marca
        return $query->whereJsonContains('preferred_vehicles', $make);
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
     * Retorna o nome completo da persona
     *
     * @return string
     */
    public function getFullName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
    
    /**
     * Retorna as iniciais do nome da persona
     *
     * @return string
     */
    public function getInitials(): string
    {
        return mb_substr($this->first_name, 0, 1) . mb_substr($this->last_name, 0, 1);
    }
}