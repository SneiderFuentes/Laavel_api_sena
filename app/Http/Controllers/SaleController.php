<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductSale;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{
    public function index()
    {
        try {
            $sales = $this->getAllSales();
            return response()->json($sales);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    private function getAllSales()
    {
        return Sale::with('productSales.product')
            ->get()
            ->map(function ($sale) {
                $sale->items = $sale->productSales->count();
                $sale->products = $sale->productSales->map(function ($productSale) {
                    return [
                        'name' => $productSale->product->name,
                        'price' => $productSale->product->price,
                        'quantity' => $productSale->quantity,
                        'subtotal' => $productSale->subtotal,
                    ];
                });
                return $sale;
            });
    }
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $sale = $this->createSale($request->products);
            return response()->json($sale->append('products'), 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    private function createSale(array $products)
    {
        return DB::transaction(function () use ($products) {
            $total = 0;
            // Crear la venta
            $sale = Sale::create(['total' => 0]);

            foreach ($products as $product) {
                $producto = Product::findOrFail($product['product_id']);

                if ($producto->quantity < $product['quantity']) {
                    throw new \Exception("Insufficient quantity for product {$producto->name}");
                }

                $subtotal = $producto->price * $product['quantity'];
                $total += $subtotal;

                // Crear la relación ProductSale
                ProductSale::create([
                    'product_id' => $producto->id,
                    'sale_id' => $sale->id,
                    'quantity' => $product['quantity'],
                    'subtotal' => $subtotal,
                ]);

                // Actualizar la cantidad del producto
                $producto->update(['quantity' => $producto->quantity - $product['quantity']]);
            }

            // Actualizar el total de la venta
            $sale->update(['total' => $total]);
            // Cargar los productos asociados a la venta
            //$sale->load('productSales.product');
            return $sale;
        });
    }
}
