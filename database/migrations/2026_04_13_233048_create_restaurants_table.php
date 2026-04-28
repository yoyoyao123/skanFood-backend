<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

     
     
    public function up(): void
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
         
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->string('name');
            $table->string('slug')->unique(); 
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->text('description')->nullable(); 
            
            $table->string('currency')->default('XOF'); 
            $table->string('logo')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
