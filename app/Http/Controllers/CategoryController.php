<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    /**
     * Category page
     */
    public function index()
    {
        return view('categories.index');
    }

    /**
     * DataTables data
     */
    public function getCategories(Request $request)
    {
        $categories = Category::withCount('subCategories')
            ->select('categories.*');

        return DataTables::of($categories)
            ->addColumn('sub_categories', function ($category) {
                return $category->subCategories
                    ->pluck('name')
                    ->implode(', ');
            })
            ->addColumn('status', function ($category) {
                return $category->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>';
            })
            ->addColumn('actions', function ($category) {
                return view('categories.partials.actions', compact('category'))->render();
            })
            ->rawColumns([
                'status',
                'actions',
            ])
            ->make(true);
    }

    /**
     * Store category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                'unique:categories,name',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully.',
            'category' => $category,
        ]);
    }

    /**
     * Show category
     */
    public function show(Category $category)
    {
        $category->load('subCategories');

        return response()->json([
            'success' => true,
            'category' => $category,
        ]);
    }

    /**
     * Update category
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                'unique:categories,name,' . $category->id,
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully.',
        ]);
    }

    /**
     * Delete category
     */
    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This category cannot be deleted because products are assigned to it.',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.',
        ]);
    }

    /**
     * AJAX - get active categories
     */
    public function options()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        return response()->json($categories);
    }

    /**
     * AJAX - get subcategories for category
     */
    public function subCategories(Category $category)
    {
        $subCategories = $category->subCategories()
            ->orderBy('name')
            ->get();

        return response()->json($subCategories);
    }

    /**
     * Store a subcategory.
     */
    public function storeSubCategory(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                'unique:sub_categories,name,NULL,id,category_id,' . $category->id,
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $validated['category_id'] = $category->id;
        $validated['is_active'] = $request->boolean('is_active', true);

        $subCategory = SubCategory::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Sub category created successfully.',
            'sub_category' => $subCategory,
        ]);
    }

    /**
     * Update a subcategory.
     */
    public function updateSubCategory(
        Request $request,
        Category $category,
        SubCategory $subCategory
    ) {
        if ($subCategory->category_id !== $category->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                'unique:sub_categories,name,' . $subCategory->id . ',id,category_id,' . $category->id,
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $subCategory->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Sub category updated successfully.',
        ]);
    }

    /**
     * Delete a subcategory when no products use it.
     */
    public function destroySubCategory(Category $category, SubCategory $subCategory)
    {
        if ($subCategory->category_id !== $category->id) {
            abort(404);
        }

        if ($subCategory->products()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This sub category cannot be deleted because products are assigned to it.',
            ], 422);
        }

        $subCategory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sub category deleted successfully.',
        ]);
    }
}
