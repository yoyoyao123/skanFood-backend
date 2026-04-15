<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // On enrichit la table products (Allergènes + Mise en avant)
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_spicy')->default(false)->after('allergens');
            $table->text('allergens')->nullable()->after('description');
            $table->boolean('is_featured')->default(false)->after('is_available');
        });

        // On enrichit la table restaurants (Personnalisation QR)
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('qr_primary_color')->default('#000000')->after('slug');
            $table->string('qr_logo_path')->nullable()->after('qr_primary_color');
        });

        // On crée la table des avis (Notation client)
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
