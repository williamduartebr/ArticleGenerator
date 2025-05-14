<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Cria a tabela forum_discussions para armazenar discussões de fóruns
     * utilizadas como fontes de dados para geração de artigos.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('forum_discussions', function (Blueprint $table) {
            // Chave primária uuid
            $table->uuid('id')->primary();

            // Informações da discussão
            $table->string('title');
            $table->text('content');
            $table->string('forum_url');

            // Categorização usando o enum de ForumCategory
            $table->enum('category', [
                'maintenance',
                'performance',
                'modification',
                'troubleshooting',
                'purchase',
                'comparison',
                'news',
                'other'
            ])->default('other');

            // Tags como JSON
            $table->json('tags')->nullable();

            // Data de publicação da discussão
            $table->timestamp('published_at');

            // Métricas da discussão
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('reply_count')->default(0);
            $table->unsignedTinyInteger('relevance_score')->default(0);

            // Campos para rastreamento de uso
            $table->unsignedInteger('usage_count')->default(0);
            $table->timestamp('last_used_at')->nullable();

            // Soft delete e timestamps
            $table->softDeletes();
            $table->timestamps();

            // Índices para consultas frequentes
            $table->index('category');
            $table->index('published_at');
            $table->index('relevance_score');
            $table->index('usage_count');
            $table->index('last_used_at');
        });

        // Adiciona índice de texto completo para buscas eficientes no MySQL 8.0+
        // Isso deve ser feito fora do Schema::create para evitar problemas
        DB::statement('CREATE FULLTEXT INDEX forum_discussions_fulltext ON forum_discussions(title, content)');
    }

    /**
     * Reverte a criação da tabela.
     *
     * @return void
     */
    public function down(): void
    {
        // Remove o índice de texto completo antes de remover a tabela
        Schema::table('forum_discussions', function (Blueprint $table) {
            // Tentativa de remover o índice fulltext se existir
            try {
                DB::statement('DROP INDEX forum_discussions_fulltext ON forum_discussions');
            } catch (\Exception $e) {
                // Ignora erro se o índice não existir
            }
        });

        Schema::dropIfExists('forum_discussions');
    }
};
