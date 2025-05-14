<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela article_generation_sessions para armazenar informações
     * sobre sessões de geração de artigos, incluindo parâmetros e estatísticas.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('article_generation_sessions', function (Blueprint $table) {
            // Chave primária uuid
            $table->uuid('id')->primary();
            
            // Informações básicas da sessão
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('in_progress'); // in_progress, completed, failed
            
            // User que iniciou a sessão (se aplicável)
            $table->uuid('user_id')->nullable();
            
            // Parâmetros de geração
            $table->json('generation_parameters')->nullable();
            $table->json('topic_keywords')->nullable();
            
            // Estatísticas da sessão
            $table->unsignedInteger('articles_requested')->default(0);
            $table->unsignedInteger('articles_generated')->default(0);
            $table->unsignedInteger('successful_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            
            // Métricas de performance
            $table->unsignedInteger('total_generation_time_seconds')->default(0);
            $table->unsignedInteger('average_generation_time_seconds')->default(0);
            
            // Timestamps específicos
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            
            // Soft delete e timestamps padrão
            $table->softDeletes();
            $table->timestamps();
            
            // Índices para consultas frequentes
            $table->index('status');
            $table->index('user_id');
            $table->index('started_at');
            $table->index('completed_at');
            
            // Índice composto para relatórios por usuário e período
            $table->index(['user_id', 'started_at']);
        });
    }

    /**
     * Reverte a criação da tabela.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('article_generation_sessions');
    }
};