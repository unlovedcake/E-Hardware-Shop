<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index(Request $request)
    {

        $searchVal = $request->searchValue;




        if (!$searchVal) {

            $brands =  Brand::paginate(10);
        } else {
            $brands =  Brand::select('id', 'name')

                ->whereAny(['name'], 'LIKE', '%' . $searchVal . '%')->paginate(10);
        }


        return Inertia::render('Brand/Index', ['brands' => $brands, 'searchTextFilter' => $searchVal]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',

        ]);

        Brand::create($request->all());
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',

        ]);
        $brand = Brand::findOrFail($id);
        $brand->update($request->all());
    }

    public function delete($id)

    {
        $brand = Brand::findOrFail($id);
        $brand->delete();
    }
}
