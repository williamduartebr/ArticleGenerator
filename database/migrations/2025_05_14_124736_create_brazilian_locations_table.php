<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela brazilian_locations para armazenar localizações brasileiras
     * utilizadas na contextualização geográfica de artigos gerados.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('brazilian_locations', function (Blueprint $table) {
            // Chave primária uuid
            $table->uuid('id')->primary();
            
            // Informações de localização
            $table->string('city');
            $table->string('region');
            
            // Código UF do estado (2 caracteres), usando o enum de BrazilianStateCode
            $table->enum('state_code', [
                'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA',
                'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN',
                'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'
            ]);
            
            // Padrão de tráfego, usando o enum de TrafficPattern
            $table->enum('traffic_pattern', ['light', 'moderate', 'heavy', 'congested'])
                ->default('moderate');
            
            // Campos para rastreamento de uso
            $table->unsignedInteger('usage_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            
            // Campos adicionais úteis para consultas geográficas
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('postal_code_range')->nullable();
            $table->unsignedInteger('population')->nullable();
            
            // Soft delete e timestamps
            $table->softDeletes();
            $table->timestamps();
            
            // Índices para consultas frequentes
            $table->index('city');
            $table->index('state_code');
            $table->index('traffic_pattern');
            $table->index('usage_count');
            $table->index('last_used_at');
            $table->index(['city', 'state_code']); // Para buscas de cidade+estado
            
            // Índice espacial para consultas geográficas (MySQL 8.0+)
            $table->spatialIndex(['latitude', 'longitude'])->nullable();
        });
    }

    /**
     * Reverte a criação da tabela.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('brazilian_locations');
    }
};