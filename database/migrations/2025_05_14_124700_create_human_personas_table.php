<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela human_personas para armazenar personas humanas fictícias
     * utilizadas na humanização de artigos gerados automaticamente.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('human_personas', function (Blueprint $table) {
            // Chave primária uuid
            $table->uuid('id')->primary();
            
            // Informações básicas da persona
            $table->string('first_name');
            $table->string('last_name');
            $table->string('profession');
            $table->string('location');
            
            // Veículos preferidos armazenados como JSON
            $table->json('preferred_vehicles')->nullable();
            
            // Campos para rastreamento de uso
            $table->unsignedInteger('usage_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            
            // Campos adicionais que podem ser úteis
            $table->unsignedTinyInteger('age')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('education_level')->nullable();
            $table->text('bio')->nullable();
            
            // Soft delete e timestamps
            $table->softDeletes();
            $table->timestamps();
            
            // Índices para consultas frequentes
            $table->index('profession');
            $table->index('location');
            $table->index('usage_count');
            $table->index('last_used_at');
            $table->index(['first_name', 'last_name']);
        });
    }

    /**
     * Reverte a criação da tabela.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('human_personas');
    }
};