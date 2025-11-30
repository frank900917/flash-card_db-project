<?php

use App\Models\FlashCardSet;
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
        Schema::create('flash_card_set_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(FlashCardSet::class)->constrained()->cascadeOnDelete();
            $table->string('word');
            $table->text('word_description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flash_card_set_details');
    }
};
