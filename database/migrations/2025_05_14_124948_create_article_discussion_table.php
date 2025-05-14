<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela article_discussion que relaciona artigos com discussões de fóruns
     * para estabelecer relações many-to-many entre estas entidades.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('article_discussion', function (Blueprint $table) {
            // Chaves da relação
            $table->uuid('article_id');
            $table->uuid('discussion_id');

            // Dados adicionais da relação
            $table->unsignedTinyInteger('relevance_score')->default(0);
            $table->text('usage_context')->nullable();

            // Timestamps
            $table->timestamps();

            // Chave primária composta
            $table->primary(['article_id', 'discussion_id']);

            // Chaves estrangeiras
            $table->foreign('article_id')
                ->references('id')
                ->on('articles')
                ->onDelete('cascade');

            $table->foreign('discussion_id')
                ->references('id')
                ->on('forum_discussions')
                ->onDelete('cascade');

            // Índices
            $table->index('relevance_score');
        });
    }

    /**
     * Reverte a criação da tabela.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('article_discussion');
    }
};
