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
     * Display a listing of the categories.
     *
     * @return \Inertia\Response
     */
    public function index(): \Inertia\Response
    {
        $categories = Category::with('subcategories')->get();

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created or updated category and its subcategories.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): \Illuminate\Http\Response
    {
        $validatedData = $request->validate([
            'categories.*.name' => 'required|string|max:255',
            'categories.*.subcategories.*.name' => 'required|string|max:255',
        ]);

        $categoriesData = $request->get('categories');

        if ($categoriesData === null) {
            Category::query()->delete();
            return response(null, 200);
        }

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

        return response(null, 200);
    }
}
