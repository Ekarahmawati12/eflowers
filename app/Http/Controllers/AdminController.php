<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
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
        $image->save($destinationPath.'/'. $imagename);
        
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

}
