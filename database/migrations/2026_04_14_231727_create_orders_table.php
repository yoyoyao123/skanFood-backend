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
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->nullable()->constrained(); // Nullable si client anonyme
        $table->integer('total_price'); // En centimes (ex: 5000 pour 5000 FCFA)
        $table->string('status')->default('pending'); // pending, preparing, ready, completed, cancelled
        $table->string('table_number')->nullable();
        $table->text('notes')->nullable(); // Ex: "Pas d'oignons"
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
