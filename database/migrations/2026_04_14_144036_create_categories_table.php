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
    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        // On lie la catégorie au restaurant
        $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
        $table->string('name');
        $table->integer('sort_order')->default(0); // Pour choisir l'ordre (Entrées avant Plats)
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
