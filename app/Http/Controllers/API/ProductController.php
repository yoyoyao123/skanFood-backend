<?php

namespace App\Http\Controllers\API; 

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
   
  public function index(Request $request, $restaurant_id = null)
{
   
    if ($restaurant_id) {
        $products = Product::where('restaurant_id', $restaurant_id)
                           ->with('category')
                           ->get();
        return response()->json(['success' => true, 'data' => $products]);
    }
    $restaurant = Restaurant::where('user_id', auth()->id())->first();

    if (!$restaurant) {
        return response()->json(['message' => 'Restaurant introuvable'], 404);
    }

    $products = Product::where('restaurant_id', $restaurant->id)
                       ->with('category')
                       ->get();

    return response()->json(['success' => true, 'data' => $products]);
}
    /**
     * 2. CRÉER un produit 
     */
  public function store(Request $request)
{
   
    $request->validate([
        'restaurant_id' => 'required|exists:restaurants,id',
        'category_id'   => 'required|exists:categories,id',
        'name'          => 'required|string|max:255',
        'price'         => 'required|numeric',
        'image'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Max 2MB
    ]);

    $restaurant = Restaurant::where('id', $request->restaurant_id)
                            ->where('user_id', auth()->id())
                            ->first();

    if (!$restaurant) {
        return response()->json(['message' => 'Action non autorisée'], 403);
    }

   
    $imagePath = null;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('products', 'public');
    }
    $product = Product::create([
        'restaurant_id' => $request->restaurant_id,
        'category_id'   => $request->category_id,
        'name'          => $request->name,
        'description'   => $request->description,
        'price'         => $request->price,
        'image'         => $imagePath,
        'is_available'  => true,
    ]);

    return response()->json([
        'message' => 'Produit créé avec l\'image !',
        'product' => $product,
        'image_url' => asset('storage/' . $imagePath) 
    ], 201);
} 





    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) return response()->json(['message' => 'Introuvable'], 404);
        if ($product->restaurant->user_id !== auth()->id()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $validated = $request->validate([
            'category_id'   => 'sometimes|exists:categories,id',
            'name'          => 'sometimes|string|max:255',
            'description'   => 'nullable|string',
            'price'         => 'sometimes|numeric|min:0',
            'is_available'  => 'boolean',
            'image'         => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) Storage::disk('public')->delete($product->image);
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return response()->json(['success' => true, 'message' => 'Mis à jour !', 'data' => $product]);
    }

    /**
     * 4. SUPPRIMER
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) return response()->json(['message' => 'Introuvable'], 404);

        if ($product->restaurant->user_id !== auth()->id()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        if ($product->image) Storage::disk('public')->delete($product->image);
        $product->delete();

        return response()->json(['success' => true, 'message' => 'Supprimé !']);
    }
}