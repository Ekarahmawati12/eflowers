<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $products = Product::orderbY('created_at', 'DESC')->paginate(12);
        return view('shop',compact('products'));
    }

    public function products_details($products_slug)
    {
        $products = Product::where('slug', $products_slug)->first();
        $rproducts = Product::where('slug','<>' ,$products_slug)->get()->take(8);
        return view('details',compact('products','rproducts'));
    }
}
