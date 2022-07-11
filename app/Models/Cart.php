<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $with = ['user', 'inventory'];

    protected $fillable = [
        'quantity', 'total_amount',
        'user_id', 'inventory_id',
        'status'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function inventory() {
        return $this->belongsTo(Inventory::class, 'inventory_id', 'id');
    }
}
