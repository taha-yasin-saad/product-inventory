<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    public function index()
    {
        // Load the existing products from JSON file
        $products = $this->loadProducts();

        // Calculate the sum of total values
        $totalValue = array_sum(array_column($products, 'total_value'));

        return view('products.index', compact('products'));
    }

    public function store(Request $request)
    {
        // Validate incoming data
        $request->validate([
            'product_name' => 'required|string',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        // Get the current data
        $products = $this->loadProducts();

        // Add the new product data with a timestamp
        $newProduct = [
            'product_name' => $request->product_name,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'datetime' => now()->toDateTimeString(),
            'total_value' => $request->quantity * $request->price,
        ];

        $products[] = $newProduct;

        // Save the data back to a JSON file (or XML if preferred)
        $this->saveProducts($products);

        // Return the new product data with index
        return response()->json(array_merge($newProduct, ['index' => count($products) - 1]));
    }

    public function edit(Request $request, $id)
    {
        // Load current products
        $products = $this->loadProducts();

        // Ensure the product exists
        if (!isset($products[$id])) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        // Update product details
        $products[$id]['product_name'] = $request->product_name;
        $products[$id]['quantity'] = $request->quantity;
        $products[$id]['price'] = $request->price;
        $products[$id]['total_value'] = $request->quantity * $request->price;

        // Save updated products
        $this->saveProducts($products);

        // Return updated product
        return response()->json($products[$id]);
    }

    // Helper to load products
    private function loadProducts()
    {
        $filePath = storage_path('app/products.json');
        if (File::exists($filePath)) {
            return json_decode(File::get($filePath), true);
        }
        return [];
    }

    // Helper to save products
    private function saveProducts($products)
    {
        $filePath = storage_path('app/products.json');
        File::put($filePath, json_encode($products, JSON_PRETTY_PRINT));
    }
}
