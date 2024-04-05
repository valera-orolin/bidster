<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('subcategories')->get();

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'categories.*.id' => 'nullable|exists:categories,id',
            'categories.*.name' => 'required|string|max:255',
            'categories.*.subcategories.*.id' => 'nullable|exists:subcategories,id',
            'categories.*.subcategories.*.name' => 'required|string|max:255',
        ]);

        $categoriesData = $request->get('categories');

        $currentCategoryIds = Category::pluck('id')->toArray();
        $currentSubcategoryIds = Subcategory::pluck('id')->toArray();

        $requestCategoryIds = [];
        $requestSubcategoryIds = [];

        foreach ($categoriesData as $categoryData) {
            $category = Category::updateOrCreate(
                ['id' => $categoryData['id']],
                ['name' => $categoryData['name']]
            );

            $requestCategoryIds[] = $category->id;

            if (isset($categoryData['subcategories'])) {
                foreach ($categoryData['subcategories'] as $subcategoryData) {
                    $subcategory = Subcategory::updateOrCreate(
                        ['id' => $subcategoryData['id']],
                        ['name' => $subcategoryData['name'], 'category_id' => $category->id]
                    );

                    $requestSubcategoryIds[] = $subcategory->id;
                }
            }
        }

        $categoriesToDelete = array_diff($currentCategoryIds, $requestCategoryIds);
        $subcategoriesToDelete = array_diff($currentSubcategoryIds, $requestSubcategoryIds);

        Category::destroy($categoriesToDelete);
        Subcategory::destroy($subcategoriesToDelete);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        //
    }
}
