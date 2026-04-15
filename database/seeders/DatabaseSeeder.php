<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
   public function run(): void
{
    // 1. Créer un utilisateur test (le propriétaire)
    $user = \App\Models\User::factory()->create([
        'name' => 'Test Owner',
        'email' => 'owner@skanfood.com',
        'role' => 'owner',
    ]);

    // 2. Créer un restaurant pour cet utilisateur
    $restaurant = \App\Models\Restaurant::create([
        'user_id' => $user->id,
        'name' => 'La Paillote Abidjan',
        'slug' => 'la-paillote-abidjan',
        'address' => 'Cocody, Abidjan',
        'phone' => '0707070707',
        'is_active' => true,
    ]);

    // 3. Créer quelques catégories
    $cat1 = \App\Models\Category::create(['restaurant_id' => $restaurant->id, 'name' => 'Entrées']);
    $cat2 = \App\Models\Category::create(['restaurant_id' => $restaurant->id, 'name' => 'Plats']);

    // 4. Créer des produits
    \App\Models\Product::create([
        'restaurant_id' => $restaurant->id,
        'category_id' => $cat1->id,
        'name' => 'Alloco',
        'description' => 'Bananes frites délicieuses',
        'price' => 1500,
        'is_available' => true,
    ]);

    \App\Models\Product::create([
        'restaurant_id' => $restaurant->id,
        'category_id' => $cat2->id,
        'name' => 'Garba Royal',
        'description' => 'Attiéké, poisson thon frit, piment',
        'price' => 2500,
        'is_available' => true,
    ]);
}
}
