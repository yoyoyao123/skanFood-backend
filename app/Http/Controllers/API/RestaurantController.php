<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Support\Str; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RestaurantController extends Controller
{
    /**
     * 1. CRÉER UN RESTAURANT (Action Admin SkanFood)
     * Utilisé pour enregistrer un nouveau client dans le système.
     */
   public function show($id)
    {
        $restaurant = Restaurant::with('categories.products')->find($id);

        if (!$restaurant) {
            return response()->json(['message' => 'Restaurant introuvable'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $restaurant
        ]);
    }

     
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'     => 'required|exists:users,id',
            'name'        => 'required|string|max:255',
            'address'     => 'required|string',
            'phone'       => 'required|string',
            'description' => 'nullable|string',
            'currency'    => 'nullable|string|default:XOF',
            'logo'        => 'nullable|image|max:2048',
        ]);

        // Génération du slug unique (avec l'import Str)
        $validated['slug'] = Str::slug($request->name) . '-' . rand(1000, 9999);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $validated['logo'] = $path;
        }

        $restaurant = Restaurant::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Restaurant créé avec succès',
            'data'    => $restaurant
        ], 201);
    }

    /**
     * 2. AFFICHER LE PROFIL (Via ID - Pour l'Admin/Dashboard)
     */
    
    /**
     * 3. RÉCUPÉRER LE MENU (Via SLUG - Pour le client qui scanne le QR Code)
     */
    public function getBySlug($slug)
    {
        // On charge les catégories ET les produits liés
        $restaurant = Restaurant::with(['categories.products' => function($query) {
            // Remplace 'is_active' par 'is_available' si c'est le nom de ta colonne
            $query->where('is_available', true); 
        }])->where('slug', $slug)->first();

        if (!$restaurant) {
            return response()->json(['message' => 'Ce menu n\'existe pas'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $restaurant
        ]);
    }

    /**
     * 4. METTRE À JOUR LE PROFIL
     */
    public function update(Request $request, $id)
    {
        $restaurant = Restaurant::find($id);

        if (!$restaurant) {
            return response()->json(['message' => 'Restaurant introuvable'], 404);
        }

        $validated = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'address'     => 'sometimes|string',
            'phone'       => 'sometimes|string',
            'description' => 'nullable|string',
            'logo'        => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ]);

        if ($request->hasFile('logo')) {
            if ($restaurant->logo) {
                Storage::disk('public')->delete($restaurant->logo);
            }

            $path = $request->file('logo')->store('logos', 'public');
            $validated['logo'] = $path;
        }

        $restaurant->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour !',
            'data'    => $restaurant,
            'logo_url' => asset('storage/' . $restaurant->logo)
        ]);
    }

    /**
     * 5. GÉNÉRER LE QR CODE
     */
    public function generateQrCode($id, Request $request)
    {
        $restaurant = Restaurant::find($id);
        if (!$restaurant) {
            return response()->json(['message' => 'Restaurant introuvable'], 404);
        }

        // URL pointant vers le futur front-end React
        $url = "http://localhost:3000/menu/" . $restaurant->slug;

        $hexColor = $request->query('color', 'FF5722'); 
        list($r, $g, $b) = sscanf($hexColor, "%02x%02x%02x");

        $qrCode = QrCode::format('svg')
            ->size(400)
            ->margin(2)
            ->color($r, $g, $b)
            ->backgroundColor(255, 255, 255)
            ->eye('circle')
            ->style('round')
            ->generate($url);

        if ($request->has('download')) {
            return response($qrCode)
                ->header('Content-Type', 'image/svg+xml')
                ->header('Content-Disposition', 'attachment; filename="qrcode-'.$restaurant->slug.'.svg"');
        }

        return response($qrCode)->header('Content-Type', 'image/svg+xml');
    }
}