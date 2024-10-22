<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Product;
use App\Models\Slide;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $slides = Slide::where('status',1)->get()->take(3);
        $categories = Category::orderBy('name')->get();
        $fproducts = Product::where('featured',1)->get()->take(8);
        $products = Product::all();
        return view('index', compact('slides','categories','fproducts','products'))->with('page_class' , 'home-background');
    }
   

    public function contact()
    {
        return view('contact');
    }

    public function contact_store(Request $request)
    {
        $request->validate([
            'name'=> 'required|max:100',
            'email'=> 'required|email',
            'phone'=> 'required|numeric|digits:10',
            'comment'=> 'required'
        ]);

        $contact = new Contact();
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->phone = $request->phone;
        $contact->comment = $request->comment;
        $contact->save();
        return redirect()->back()->with('success','Your Message has been sent successfully');



    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $results = Product::where('name','LIKE',"%{$query}%")->get()->take(8);
        return response()->json($results);
    }

   

}
