<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;



class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }
    

     public function categories()
     {
        $categories = Category::orderBy('id', 'DESC')->paginate(10);
        return view('admin.categories', compact('categories'));
     }
     public function category_add()
     {
        return view('admin.category-add');
     }

     public function category_store(Request $request)
     {
        $request->validate([
            'name' => 'required',
             'slug' => 'required|unique:categories,slug',
             'image' =>'mimes:jpeg,jpg,png|max:2048'
        ]);
        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $image = $request->file('image');
        $file_extention =$request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;
        $this->GenerateCategoryThumbailsImage($image,$file_name);
        $category->image = $file_name;
        $category->save();
        return redirect()->route('admin.categories')->with('status', 'Category has been  added successfully!');
     }
     public function GenerateCategoryThumbailsImage($image, $imagename)
    {
        $destinationPath = public_path('uploads/categories/');
        $image= Image::read($image->path());
        $image->cover(124,124, "top");
        $image->resize(124, 124, function ($constraint) {
            $constraint->aspectRatio();
        });
        $image->save($destinationPath .'/'. $imagename);
        
    }

    public function category_edit($id)
    {
        $category = Category::find($id);
        return view('admin.category-edit', compact('category'));
    }

    public function category_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
             'slug' => 'required|unique:categories,slug,'.$request->id,
             'image' =>'mimes:jpeg,jpg,png|max:2048'
        ]);
        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        if($request->hasFile('image')){
            if(File::exists(public_path('uploads/categories/').'/'.$category->image))
            {
                File::delete(public_path('uploads/categories/').'/'.$category->image);
            }
            $image = $request->file('image');
            $file_extention =$request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extention;
            $this->GenerateCategoryThumbailsImage($image,$file_name);
            $category->image = $file_name;
        }
       
        $category->save();
        return redirect()->route('admin.categories')->with('status', 'Category updated successfully!');
    }

    public function category_delete($id)
     {
        $category = Category::find($id);
        if(File::exists(public_path('uploads/categories/').'/'.$category->image))
            {
                File::delete(public_path('uploads/categories/').'/'.$category->image);
            }
        $category->delete();
        return redirect()->route('admin.categories')->with('status', 'category has been delete sueccesfully');

     }

     public function products()
    {
        $products = Product::orderBy('created_at', 'DESC')->paginate(10);
        return view('admin.products' , compact('products'));
    }
    
    public function products_add()
    {
        $categories = Category::select('id','name')->orderBY('name')->get();
        return view('admin.products-add', compact('categories'));

    }

    public function products_store(Request $request)
    {
        $request->validate([
            'name' =>'required',
            'slug' =>'required|unique:products,slug',
            'short_description' =>'required',
            'description' =>'required',
            'regular_price' =>'required',
            'stock_status' =>'required',
            'featured' =>'required',
            'image' =>'required|mimes:jpg,png,jpeg|max:2048',
            'category_id' =>'required'


        ]);
        $products = new Product();
        $products->name = $request->name;
        $products->slug = Str::slug($request->name);
        $products->short_description = $request->short_description ;
        $products->description = $request->description ;
        $products->regular_price = $request->regular_price;
        $products->stock_status = $request->stock_status;
        $products->featured = $request->featured;
        $products->image = $request->image;
        $products->category_id = $request->category_id;

        $current_timestamp = Carbon::now()->timestamp;


        if($request->hasFile('image'))
        {
            $image = $request->file('image');
            $imageName =   $current_timestamp . '.' . $image->extension();
            $this-> GenerateProductsThumbnailImage($image, $imageName);
            $products->image = $imageName;
        }

        $gallery_arr = array();
        $gallery_images ="";
        $counter = 1;

        if($request->hasFile('images'))
        {
            $allowedfileExtion = ['jpg', 'png','jpeg'];
            $files = $request->file('images');
            foreach($files as $file)
            {
                $gextension = $file->getClientOriginalExtension();
                $gcheck = in_array($gextension ,$allowedfileExtion);
                if($gcheck)
                {
                    $gfileName = $current_timestamp ."-" . $counter ."." . $gextension;
                    $this->GenerateProductsThumbnailImage($file, $gfileName);
                    array_push($gallery_arr,$gfileName);
                    $counter = $counter + 1;

                }
            }
            $gallery_images = implode(',',$gallery_arr);

        }

        $products->images = $gallery_images;
        $products->save();
        return redirect()->route('admin.products')->with('status', 'Products has been added sucessfully');


    }

    public function GenerateProductsThumbnailImage($image, $imageName)
    {
        $destinationPathThumbnail = public_path('uploads/products/thumbnails');
        $destinationPath = public_path('uploads/products/');
        $image= Image::read($image->path());

        $image->cover(540,689, "top");
        $image->resize(540, 689, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath .'/'. $imageName);

        $image->resize(104, 104, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPathThumbnail .'/'. $imageName);
        
    }

    public function products_edit($id)
    {
        $products = Product::find($id);
        $categories = Category::select('id','name')->orderBY('name')->get();
        return view('admin.products-edit', compact('products','categories'));

    }

    public function products_update(Request $request)
{
    $request->validate([
        'name' =>'required',
        'slug' =>'required|unique:products,slug,'.$request->id,
        'short_description' =>'required',
        'description' =>'required',
        'regular_price' =>'required',
        'stock_status' =>'required',
        'featured' =>'required',
        'image' =>'mimes:jpg,png,jpeg|max:2048',
        'category_id' =>'required'
    ]);

    $products = Product::find($request->id);
    $products->name = $request->name;
    $products->slug = Str::slug($request->name);
    $products->short_description = $request->short_description;
    $products->description = $request->description;
    $products->regular_price = $request->regular_price;
    $products->stock_status = $request->stock_status;
    $products->featured = $request->featured;
    $products->category_id = $request->category_id;

    $current_timestamp = Carbon::now()->timestamp;

    // Handle product image
    if($request->hasFile('image'))
    {
        // Delete old image
        if(File::exists(public_path('uploads/products').'/'.$products->image))
        {
            File::delete(public_path('uploads/products').'/'.$products->image);
        }
        if(File::exists(public_path('uploads/products/thumbnails').'/'.$products->image))
        {
            File::delete(public_path('uploads/products/thumbnails').'/'.$products->image);
        }

        // Save new image
        $image = $request->file('image');
        $imageName = $current_timestamp . '.' . $image->extension();
        $this->GenerateProductsThumbnailImage($image, $imageName);
        $products->image = $imageName; 
    }

    $products->save();
    return redirect()->route('admin.products')->with('status', 'Product has been updated successfully!');
    }

    public function products_delete($id)
    {
        
            $products = Product::find($id);
            if(File::exists(public_path('uploads/products').'/'.$products->image))
                {
                    File::delete(public_path('uploads/products').'/'.$products->image);
                }
            if(File::exists(public_path('uploads/products/thumbnails').'/'.$products->image))
                {
                    File::delete(public_path('uploads/products/thumbnails').'/'.$products->image);
                }
            $products->delete();
            return redirect()->route('admin.products')->with('status', 'products has been delete sueccesfully');
    
         
    }
    public function orders()
    {
        $orders = Order::orderBy('created_at','DESC')->paginate(12);
        return view('admin.orders', compact('orders'));
    }

    public function order_details($order_id)
    {
        $order = Order::find($order_id);
        $orderItems = OrderItem::where('order_id', $order_id)->orderBy('id')->paginate('12');
        $transaction= Transaction::where('order_id', $order_id)->first();
        return view('admin.order-details', compact('order', 'orderItems','transaction'));



    }

    public function update_order_status(Request $request)
    {
        $order = Order::find($request->order_id);
        $order->status = $request->order_status;
        if($request->order_status == 'delivered')
        {
            $order->delivered_date = Carbon::now();
        } 
        else if($request->order_status == 'canceled')
        {
            $order->canceled_date = Carbon::now();
        }
        $order->save();

        if($request->order_status == 'delivered')
        {
            $transaction = Transaction::where('order_id',$request->order_id)->first();
            $transaction->status = 'approved';
            $transaction->save();

        }
        return back()->with("status", "Status changed succesfully!!");

    }
}