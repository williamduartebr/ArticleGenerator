<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela element_usage_stats para armazenar estatísticas agregadas 
     * sobre o uso de elementos de humanização, permitindo análises e otimizações.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('element_usage_stats', function (Blueprint $table) {
            // Chave primária uuid
            $table->uuid('id')->primary();
            
            // Identificação do elemento
            $table->enum('element_type', ['persona', 'location', 'discussion', 'source']);
            $table->uuid('element_id');
            
            // Período da estatística
            $table->date('period_start');
            $table->date('period_end');
            $table->enum('period_type', ['daily', 'weekly', 'monthly', 'yearly']);
            
            // Métricas de uso
            $table->unsignedInteger('usage_count')->default(0);
            $table->unsignedInteger('article_count')->default(0);
            $table->unsignedTinyInteger('performance_score')->default(0);
            
            // Combinações mais frequentes
            $table->json('common_combinations')->nullable();
            
            // Timestamp de atualização
            $table->timestamp('last_updated_at')->useCurrent();
            
            // Timestamps padrão
            $table->timestamps();
            
            // Índices para consultas frequentes
            $table->index('element_type');
            $table->index('element_id');
            $table->index('period_start');
            $table->index('period_type');
            $table->index('usage_count');
            
            // Índices compostos para relatórios
            $table->index(['element_type', 'period_type', 'period_start']);
            $table->unique(['element_type', 'element_id', 'period_type', 'period_start']);
        });
    }

    /**
     * Reverte a criação da tabela.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('element_usage_stats');
    }
};