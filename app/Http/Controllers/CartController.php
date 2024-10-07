<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
    public function index()
    {
        $items = Cart::instance('cart')->content();
        return view('cart', compact('items'));
    }

    public function addto_cart(Request $request)
    {
        // Menambahkan produk ke keranjang
        Cart::instance('cart')->add(
            $request->id,
            $request->name,
            $request->quantity,
            $request->price
        )->associate('App\Models\Product'); // Mengasosiasikan dengan model Product

        // Redirect kembali ke halaman sebelumnya
        return redirect()->back()->with('success', 'Product added to cart!');
    }

    public function increase_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty + 1;
        Cart::instance('cart')->update($rowId,$qty);
        return redirect()->back();
    }
    public function decrease_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty - 1;
        Cart::instance('cart')->update($rowId,$qty);
        return redirect()->back();
    }

    public function remove_item($rowId)
    {
        Cart::instance('cart')->remove($rowId);
        return redirect()->back();

    }
    public function empty_cart()
    {
        Cart::instance('cart')->destroy();
        return redirect()->back();

    }
    public function checkout()
    {
    // Jika user belum login, arahkan ke halaman login
    if (!Auth::check()) 
    {
        return redirect()->route('login');
    }

    // Jika user sudah login, ambil alamat default
    $address = Address::where('user_id', Auth::user()->id)
                      ->where('isdefault', 1)
                      ->first();
    return view('checkout', compact('address'));
    }
    public function placean_order(Request $request)
    {
        $user_id = Auth::user()->id;
        $address =Address::where('user_id', $user_id)->where('isdefault', true)->first();

        if(!$address)
        {
            $request->validate([
                'name'=> 'required|max:100',
                'phone'=> 'required|numeric|digits:10',
                'zip'=> 'required|numeric|digits:6',
                'state'=> 'required',
                'city'=> 'required',
                'address'=> 'required',
                'locality'=> 'required',
                'landmark'=> 'required',
            ]);
            $address = new Address();
            $address->name = $request->name;
            $address->phone = $request->phone;
            $address->zip = $request->zip;
            $address->state = $request->state;
            $address->city  = $request->city;
            $address->address = $request->address;
            $address->locality = $request->locality;
            $address->landmark = $request->landmark;
            $address->country = 'Indonesia';
            $address->user_id = $user_id;
            $address->isdefault = true;
            $address->save();
        }

        $this->setAmountForCheckout();

        $order = new Order();
        $order->user_id = $user_id;
        $order->subtotal = Session::get('checkout')['subtotal'];
        $order->tax = Session::get('checkout')['tax'];
        $order->total = Session::get('checkout')['total'];
        $order->name = $address->name;
        $order->phone = $address->phone;
        $order->locality = $address->locality;
        $order->address = $address->address;
        $order->city = $address->city;
        $order->state = $address->state;
        $order->country = $address->country; 
        $order->landmark = $address->landmark;
        $order->zip = $address->zip;
        $order->save();

        foreach(Cart::instance('cart')->content() as $item)
        {
            $orderItem = new OrderItem();
            $orderItem->product_id = $item->id;
            $orderItem->order_id = $order->id;
            $orderItem->price = $item->price;
            $orderItem->quantity = $item->qty;
            $orderItem->save();
            
        }
        if($request->mode == "card")
        {
            //
        }
        elseif($request->mode == "paypal")
        {
            //
        }
        elseif($request->mode == "cod")
        {
            $transaction = new Transaction();
            $transaction->user_id = $user_id;
            $transaction->order_id = $order->id;
            $transaction->mode = $request->mode;
            $transaction->status = "pending";
            $transaction->save();
        }
        

        Cart::instance('cart')->destroy();
        Session::forget('checkout');
        Session::put('order_id', $order->id);
        return redirect()->route('cart.order.confirmation');


        
    }

    public function setAmountForCheckout()
{
    if (Cart::instance('cart')->content()->count() == 0) {
        Session::forget('checkout');
        return;
    }
    Session::put('checkout', [
        'subtotal' => Cart::instance('cart')->subtotal(),
        'tax'      => Cart::instance('cart')->tax(),
        'total'    => Cart::instance('cart')->total(),
    ]);
}

public function order_confirmation()
{
    if(Session::has('order_id'))
    {
        $order = Order::find(Session::get('order_id'));
    return view('order_confirmation', compact('order'));

    }
    return redirect()->route('cart.index');
}


    
}

