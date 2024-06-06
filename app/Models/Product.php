<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'quantity',
        'price',
        'status'
    ];

    public function sales(): BelongsToMany
    {
        return $this->belongsToMany(Sale::class);
    }
    public function productSales(): HasMany
    {
        return $this->hasMany(ProductSale::class);
    }
}
