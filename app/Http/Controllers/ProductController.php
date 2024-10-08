<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Traits\ProductTrait;
use App\Traits\ProductTrait as TraitsProductTrait;

class ProductController extends Controller
{
    use TraitsProductTrait;
    public function index(Request $request)
    {


        $searchVal = $request->searchValue;

        $filterCategory = $request->filterCategoryName;
        $filterBrand   = $request->filterBrandName;

        $categories = Category::all();
        $brands = Brand::all();



        //Convert to array
        if ($filterCategory && !is_array($filterCategory) || $filterBrand && !is_array($filterBrand)) {
            $filterCategory = explode(',', $filterCategory);
            $filterBrand = explode(',', $filterBrand);

            //dd($filterCategory);
        }


        if (!$searchVal && !$filterCategory && !$filterBrand) {

            $products = Product::select('id', 'name', 'image', 'description', 'price', 'quantity', 'brand_id', 'category_id')
                ->with(['category:id,name', 'brand:id,name'])
                ->paginate(6);
        } else if ($filterCategory || $filterBrand) {

            $products = $this->filterProduct($filterCategory, $filterBrand);
        } else {

            $products = $this->searchProduct($searchVal);
        }

        return Inertia::render('Product/Index', ['products' => $products, 'categories' => $categories, 'brands' => $brands, 'searchTextFilter' => $searchVal]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|numeric',
            'brand_id' => 'required|numeric',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|numeric|min:1',
            'image' => 'nullable|array|max:3',
            'image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);



        $images = [];
        $path = 'uploads/product/';


        if ($request->has('image') && $request->image != null) {
            $productImages = $request->file('image');
            foreach ($productImages as $image) {
                // Generate a unique name for the image using timestamp and random string
                $uniqueName = time() . '-' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                // Store the image in the public folder with the unique name
                $image->storeAs('public/uploads',  $uniqueName);

                //$image->move($path, $uniqueName);
                $images[] = 'storage/uploads/' . $uniqueName;
            }
        }

        Product::create([
            'name' => $request->name,
            'brand_id' => $request->brand_id,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'image' => $request->image == null ? ['storage/uploads/default.png'] : $images
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            // 'image' => 'nullable|array|max:3',
            // 'image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',


        ]);

        $product = Product::findOrFail($id);




        // $filteredArray2 = array_diff_key($product->image, array_flip($request->index));
        // $filteredArray2[] = 'image4';
        // dd($filteredArray2);

        $images = [];


        if ($request->hasFile('image')) {
            $productImages = $request->file('image');

            foreach ($productImages as $image) {
                // Generate a unique name for the image using timestamp and random string
                $uniqueName = time() . '-' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                // Store the image in the public folder with the unique name

                $image->storeAs('public/uploads',  $uniqueName);
                $images[] = 'storage/uploads/' . $uniqueName;
            }


            if (!$request->url) {

                $images = array_merge($images, $product->image);
            }
        }
        if ($request->url) { //Get the the list remove url image


            // Use array_diff to get the difference between array2 and array1
            $imagesAvailable = array_diff($product->image, $request->url); // Variable $images is the new updated value

            $images = array_merge($images, $imagesAvailable);



            foreach ($request->url as $existingImage) {
                if (file_exists($existingImage)) {

                    @unlink($existingImage);
                }
            }
        }




        $product->name = $request->name;
        $product->description = $request->description;
        $product->brand_id = $request->brand_id;
        $product->category_id = $request->category_id;
        $product->price = $request->price;
        $product->quantity = $request->quantity;
        $product->image = $images === [] ? $product->image : $images;


        $product->save();
        //$product->update($request->all());
    }

    public function delete($id)

    {
        $product = Product::findOrFail($id);
        $product->delete();
    }

    public function getProduct($id)

    {
        $product = Product::findOrFail($id);

        return response()->json($product);
    }
}
