<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['restaurant_id', 'user_id', 'total_price', 'status', 'table_number', 'notes'];

    public function restaurant() {
        return $this->belongsTo(Restaurant::class);
    }

    public function items() {
        return $this->hasMany(OrderItem::class);
    }
}