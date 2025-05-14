<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('maintenance_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('icon_svg');
            $table->string('icon_bg_color')->default('bg-blue-100');
            $table->string('icon_text_color')->default('text-blue-600');
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('seo_info')->nullable();
            $table->json('info_sections')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_categories');
    }
};
