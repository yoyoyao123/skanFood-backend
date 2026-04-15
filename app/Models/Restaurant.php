<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'slug', 'address', 'phone', 'logo', 'is_active'];

    public function products() {
        return $this->hasMany(Product::class);
    }

    public function categories() {
        return $this->hasMany(Category::class);
    }

    public function orders() {
        return $this->hasMany(Order::class);
    }
}