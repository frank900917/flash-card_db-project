<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_theme_settings', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')
                    ->constrained()
                    ->onDelete('cascade');

            $table->foreignId('theme_id')
                    ->constrained('themes')
                    ->onDelete('cascade');
            
            $table->foreignId('flash_card_set_id')
                    ->constrained('flash_card_sets')
                    ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_theme_settings');
    }
};
