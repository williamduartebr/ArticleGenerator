<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela article_generation_events para rastrear eventos
     * durante o processo de geração de artigos, facilitando o diagnóstico
     * e análise do pipeline de geração.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('article_generation_events', function (Blueprint $table) {
            // Chave primária uuid
            $table->uuid('id')->primary();
            
            // Relacionamentos
            $table->uuid('article_id')->nullable();
            $table->uuid('session_id');
            
            // Informações do evento
            $table->string('event_type');
            $table->string('event_name');
            $table->text('description')->nullable();
            $table->string('status')->default('success'); // success, warning, error
            
            // Detalhes adicionais
            $table->json('metadata')->nullable();
            $table->text('error_message')->nullable();
            
            // Timestamp específico para o evento
            $table->timestamp('occurred_at')->useCurrent();
            
            // Timestamps padrão
            $table->timestamps();
            
            // Chaves estrangeiras
            $table->foreign('article_id')
                ->references('id')
                ->on('articles')
                ->onDelete('cascade');
                
            $table->foreign('session_id')
                ->references('id')
                ->on('article_generation_sessions')
                ->onDelete('cascade');
            
            // Índices para consultas frequentes
            $table->index('event_type');
            $table->index('event_name');
            $table->index('status');
            $table->index('occurred_at');
            
            // Índice composto para análise temporal
            $table->index(['session_id', 'occurred_at']);
            $table->index(['article_id', 'occurred_at']);
        });
    }

    /**
     * Reverte a criação da tabela.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('article_generation_events');
    }
};