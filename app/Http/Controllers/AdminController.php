<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Slide;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;



class AdminController extends Controller
{
    public function index()
    {
        $orders = Order::orderBy('created_at','DESC')->get()->take(10);
        $dashboardDatas = DB::select("Select sum(total) As TotalAmount,
                                        sum(if(status='ordered',total,0)) As TotalOrderedAmount,
                                        sum(if(status='delivered',total,0)) As TotalDeliveredAmount,
                                        sum(if(status='canceled',total,0)) As TotalCanceledAmount,
                                        Count(*) As Total,
                                        sum(if(status='ordered',1,0)) As TotalOrdered,
                                        sum(if(status='delivered',1,0)) As TotalDelivered,
                                        sum(if(status='canceled',1,0)) As TotalCanceled
                                        From Orders
                                        ");

            $monthlyDatas = DB::select("SELECT M.id As MonthNo, M.name As MonthName,
                            IFNULL(D.TotalAmount,0) As TotalAmount,
                            IFNULL(D.TotalOrderedAmount,0) As TotalOrderedAmount,
                            IFNULL(D.TotalDeliveredAmount,0) As TotalDeliveredAmount,
                            IFNULL(D.TotalCanceledAmount,0) As TotalCanceledAmount FROM month_names M
                            LEFT JOIN (Select DATE_FORMAT(created_at,'%b') As MonthName,
                            MONTH(created_at) As MonthNo,
                            sum(total) As TotalAmount,
                            sum(if(status='ordered',total,0)) As TotalOrderedAmount,
                            sum(if(status='delivered',total,0)) As TotalDeliveredAmount,
                            sum(if(status='canceled',total,0)) As TotalCanceledAmount
                            From Orders WHERE YEAR(created_at)=YEAR(NOW()) GROUP BY YEAR(created_at), MONTH(created_at), DATE_FORMAT(created_at,'%b')
                            Order By MONTH(created_at)) D on D.MonthNo=M.id"
        );
        $AmountM = implode(',', collect($monthlyDatas)->pluck('TotalAmount')->toArray());
        $OrderedAmountM = implode(',', collect($monthlyDatas)->pluck('TotalOrderedAmount')->toArray());
        $DeliveredAmountM = implode(',', collect($monthlyDatas)->pluck('TotalDeliveredAmount')->toArray());
        $CanceledAmountM = implode(',', collect($monthlyDatas)->pluck('TotalCanceledAmount')->toArray());

        $TotalAmount = collect($monthlyDatas)->sum('TotalAmount');
        $TotalOrderedAmount = collect($monthlyDatas)->sum('TotalOrderedAmount');
        $TotalDeliveredAmount = collect($monthlyDatas)->sum('TotalDeliveredAmount');
        $TotalCanceledAmount = collect($monthlyDatas)->sum('TotalCanceledAmount');

        return view('admin.index' ,compact('orders','dashboardDatas','AmountM','OrderedAmountM','DeliveredAmountM',
        'CanceledAmountM','TotalAmount','TotalOrderedAmount','TotalDeliveredAmount','TotalCanceledAmount'));
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

    public function slides()
    {
        $slides = Slide::orderBy('id','DESC')->paginate(12);
        return view('admin.slides', compact('slides'));
    }

    public function slide_add()
    {
        return view('admin.slide-add');
    }

    public function slide_store(Request $request)
    {
        $request->validate([

            'tagline' => 'required',
            'title'=> 'required',
            'subtitle'=> 'required',
            'link'=> 'required',
            'status'=> 'required',
            'image'=> 'required|mimes:png,jpg,jpeg|max:2048',
        ]);

        $slide = new Slide();
        $slide->tagline = $request->tagline;
        $slide->title = $request->title;
        $slide->subtitle = $request->subtitle;
        $slide->link = $request->link;
        $slide->status = $request->status;

        $image = $request->file('image');
        $file_extention =$request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;
        $this->GenerateSlideThumbailsImage($image,$file_name);
        $slide->image = $file_name;
        $slide->save();
        return redirect()->route('admin.slides')->with("status", "slide added succesfully!");

    }

    public function GenerateSlideThumbailsImage($image, $imagename)
    {
        $destinationPath = public_path('uploads/slides');
        $image= Image::read($image->path());
        $image->cover(400,690, "top");
        $image->resize(400, 690, function ($constraint) {
            $constraint->aspectRatio();
        });
        $image->save($destinationPath .'/'. $imagename);
        
    }

    public function slide_edit($id)
    {
        $slide = Slide::find($id);
        return view('admin.slide-edit', compact('slide'));
    }

    public function slide_update(Request $request)
    {
        $request->validate([

            'tagline' => 'required',
            'title'=> 'required',
            'subtitle'=> 'required',
            'link'=> 'required',
            'status'=> 'required',
            'image'=> 'mimes:png,jpg,jpeg|max:2048',
        ]);

        $slide = Slide::find($request->id);
        $slide->tagline = $request->tagline;
        $slide->title = $request->title;
        $slide->subtitle = $request->subtitle;
        $slide->link = $request->link;
        $slide->status = $request->status;

        if($request->hasFile('image'))
      {
        if(File::exists(public_path('uploads/slides').'/'.$slide->image))
        {
            File::delete((public_path('uploads/slides').'/'.$slide->image));
        }
        $image = $request->file('image');
        $file_extention =$request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;
        $this->GenerateSlideThumbailsImage($image,$file_name);
        $slide->image = $file_name;
      }
        $slide->save();
        return redirect()->route('admin.slides')->with("status", "slide updated succesfully!");

    }

    public function slide_delete($id)
    {
        $slide = Slide::find($id);
        if(File::exists(public_path('uploads/slides').'/'.$slide->image))
        {
            File::delete((public_path('uploads/slides').'/'.$slide->image));
        }

        $slide->delete();
        return redirect()->route('admin.slides')->with("status", "slide deleted succesfully!");
    }
   
}