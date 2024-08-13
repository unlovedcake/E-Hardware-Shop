<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {

        $searchVal = $request->searchValue;




        if (!$searchVal) {

            $categories =  Category::paginate(10);
            // $categories =  Category::all();
        } else {
            $categories = Category::select('id', 'name')

                ->whereAny(['name'], 'LIKE', '%' . $searchVal . '%')->paginate(10);
        }


        return Inertia::render('Category/Index', ['categories' => $categories, 'searchTextFilter' => $searchVal]);
    }




    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',

        ]);

        Category::create($request->all());
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',

        ]);
        $category = Category::findOrFail($id);
        $category->update($request->all());
    }

    public function delete($id)

    {
        $category = Category::findOrFail($id);
        $category->delete();
    }
}
