<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        
        Schema::table('products', function (Blueprint $table) {
    
         $table->text('allergens')->nullable()->after('description');
         $table->boolean('is_spicy')->default(false)->after('allergens');
        $table->boolean('is_featured')->default(false)->after('is_available');
        });
        
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('qr_primary_color')->default('#000000')->after('slug');
            $table->string('qr_logo_path')->nullable()->after('qr_primary_color');
        });

        
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->integer('rating');
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['allergens', 'is_featured']);
        });
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['qr_primary_color', 'qr_logo_path']);
        });
    }
};
