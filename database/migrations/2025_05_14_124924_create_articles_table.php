<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela articles para armazenar os artigos gerados
     * que posteriormente serão humanizados com elementos específicos.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            // Chave primária uuid
            $table->uuid('id')->primary();
            
            // Informações básicas do artigo
            $table->string('title');
            $table->text('content');
            $table->string('slug')->unique();
            
            // Metadados
            $table->string('status')->default('draft'); // draft, published, archived
            $table->json('keywords')->nullable();
            $table->json('metadata')->nullable();
            
            // Humanização (referências às outras tabelas)
            $table->uuid('persona_id')->nullable();
            $table->uuid('location_id')->nullable();
            
            // Estatísticas
            $table->unsignedInteger('word_count')->default(0);
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('engagement_score')->default(0);
            
            // Veículo relacionado (se aplicável)
            $table->string('vehicle_make')->nullable();
            $table->string('vehicle_model')->nullable();
            $table->unsignedSmallInteger('vehicle_year')->nullable();
            
            // Rastreamento de geração
            $table->uuid('generation_session_id')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('published_at')->nullable();
            
            // Soft delete e timestamps
            $table->softDeletes();
            $table->timestamps();
            
            // Chaves estrangeiras
            $table->foreign('persona_id')
                ->references('id')
                ->on('human_personas')
                ->onDelete('set null');
                
            $table->foreign('location_id')
                ->references('id')
                ->on('brazilian_locations')
                ->onDelete('set null');
            
            // Índices para consultas frequentes
            $table->index('status');
            $table->index('vehicle_make');
            $table->index('vehicle_model');
            $table->index('generated_at');
            $table->index('published_at');
            $table->index('generation_session_id');
            
            // Índice composto para buscas de veículos
            $table->index(['vehicle_make', 'vehicle_model', 'vehicle_year']);
        });
    }

    /**
     * Reverte a criação da tabela.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};