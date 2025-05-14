<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela content_sources para armazenar fontes de conteúdo
     * utilizadas para obtenção de dados para geração de artigos.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('content_sources', function (Blueprint $table) {
            // Chave primária uuid
            $table->uuid('id')->primary();
            
            // Informações básicas da fonte
            $table->string('name');
            $table->string('url')->unique();
            
            // Tipo da fonte, usando o enum de ContentSourceType
            $table->enum('type', [
                'forum', 'social_media', 'blog', 'news', 'review', 'official', 'other'
            ])->default('other');
            
            // Métricas de confiabilidade e tópicos
            $table->float('trust_score', 5, 2)->default(50.00);
            $table->json('topics')->nullable();
            
            // Status e controle
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_crawled_at')->nullable();
            
            // Verificação
            $table->timestamp('verified_at')->nullable();
            $table->string('verified_by')->nullable();
            
            // Campos para rastreamento de uso
            $table->unsignedInteger('usage_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            
            // Campos de configuração do crawler
            $table->json('crawler_config')->nullable();
            $table->json('content_extraction_rules')->nullable();
            $table->unsignedInteger('crawl_frequency_hours')->default(168); // 7 dias
            
            // Soft delete e timestamps
            $table->softDeletes();
            $table->timestamps();
            
            // Índices para consultas frequentes
            $table->index('type');
            $table->index('trust_score');
            $table->index('is_active');
            $table->index('last_crawled_at');
            $table->index('usage_count');
            
            // Índice composto para buscas de fontes ativas com alta confiabilidade
            $table->index(['is_active', 'trust_score']);
        });
    }

    /**
     * Reverte a criação da tabela.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('content_sources');
    }
};