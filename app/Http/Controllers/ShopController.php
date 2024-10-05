<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
{
    // Ambil nilai kategori dan harga dari request
    $category = $request->input('category');

    // Query untuk produk dengan filter kategori dan harga
    $query = Product::orderBy('created_at', 'DESC');

    // Jika kategori dipilih, tambahkan ke query
    if ($category) {
        $query->where('category_id', $category);
    }

    // Jika filter harga dipilih, tambahkan ke query
    

    // Paginate hasilnya
    $products = $query->paginate(12);

    // Ambil semua kategori untuk dropdown filter
    $categories = Category::all();

    return view('shop', compact('products', 'categories', 'category'));
}



    public function products_details($products_slug)
    {
        $products = Product::where('slug', $products_slug)->first();
        $rproducts = Product::where('slug','<>' ,$products_slug)->get()->take(8);
        return view('details',compact('products','rproducts'));
    }
}
