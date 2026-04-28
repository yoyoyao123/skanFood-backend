<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // 1. Lister les catégories DU RESTAURANT de l'utilisateur connecté
    public function index()
    {
        $restaurant = Restaurant::where('user_id', auth()->id())->first();

        if (!$restaurant) {
            return response()->json(['message' => 'Restaurant non trouvé'], 404);
        }

        $categories = Category::where('restaurant_id', $restaurant->id)
        ->orderBy('sort_order', 'asc') 
        ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    // 2. Créer une catégorie
    public function store(Request $request)
    {
        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'name' => 'required|string|max:255',
            'sort_order' => 'nullable|integer'
        ]);
        if (!Restaurant::where('id', $request->restaurant_id)->where('user_id', auth()->id())->exists()) {
            return response()->json(['message' => 'Action non autorisée'], 403);
        }

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Catégorie créée !',
            'data' => $category
        ], 201);
    }

    // 3. Modifier une catégorie (avec vérification propriétaire)
    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        
        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Introuvable'], 404);
        }

        if ($category->restaurant->user_id !== auth()->id()) {
            return response()->json(['message' => 'Action non autorisée'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'sort_order' => 'sometimes|integer'
        ]);

        $category->update($validated);

        return response()->json(['success' => true, 'data' => $category]);
    }

    // 4. Supprimer une catégorie
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category || $category->restaurant->user_id !== auth()->id()) {
            return response()->json(['message' => 'Non autorisé ou introuvable'], 403);
        }

        $category->delete();

        return response()->json(['success' => true, 'message' => 'Catégorie supprimée']);
    }
}