<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela element_combinations para rastrear combinações de elementos
     * de humanização utilizadas em artigos, permitindo análise de compatibilidade
     * e evitando repetições excessivas.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('element_combinations', function (Blueprint $table) {
            // Chave primária uuid
            $table->uuid('id')->primary();
            
            // Elementos combinados
            $table->uuid('persona_id');
            $table->uuid('location_id');
            
            // Estatísticas da combinação
            $table->unsignedInteger('usage_count')->default(1);
            $table->float('compatibility_score', 4, 2)->default(0.5);
            $table->unsignedTinyInteger('engagement_score')->default(0);
            
            // Contextos de uso (para análise)
            $table->json('contexts')->nullable();
            
            // Timestamps de uso
            $table->timestamp('first_used_at')->useCurrent();
            $table->timestamp('last_used_at')->useCurrent();
            
            // Timestamps padrão
            $table->timestamps();
            
            // Chaves estrangeiras
            $table->foreign('persona_id')
                ->references('id')
                ->on('human_personas')
                ->onDelete('cascade');
                
            $table->foreign('location_id')
                ->references('id')
                ->on('brazilian_locations')
                ->onDelete('cascade');
            
            // Chave única para a combinação
            $table->unique(['persona_id', 'location_id']);
            
            // Índices para consultas frequentes
            $table->index('usage_count');
            $table->index('compatibility_score');
            $table->index('last_used_at');
        });
    }

    /**
     * Reverte a criação da tabela.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('element_combinations');
    }
};