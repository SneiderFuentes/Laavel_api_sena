<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;
    protected $fillable = [
        'total',
    ];
    public function productSales(): HasMany
    {
        return $this->hasMany(ProductSale::class);
    }
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
    // Método para obtener los productos con los detalles necesarios
    public function getProductsAttribute()
    {
        return $this->productSales->map(function ($productSale) {
            return [
                'name' => $productSale->product->name,
                'price' => $productSale->product->price,
                'quantity' => $productSale->quantity,
                'subtotal' => $productSale->subtotal,
            ];
        });
    }
}
