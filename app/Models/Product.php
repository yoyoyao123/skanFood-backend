<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

protected $fillable = [ 'restaurant_id', 'category_id', 'name', 'description', 'price', 'image', 'is_available'];
   public function restaurant()
{
    return $this->belongsTo(Restaurant::class);
}

    public function category() {
        return $this->belongsTo(Category::class);
    }
}