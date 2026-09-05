<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function index()
    {
        return view('categories.index');
    }

    public function getCategories()
    {
        $categories = Category::with('subCategories')
            ->select('categories.*');

        return DataTables::eloquent($categories)
            ->addColumn('sub_categories', function ($category) {
                if ($category->subCategories->isEmpty()) {
                    return '<span class="text-muted">No sub categories</span>';
                }

                return $category->subCategories
                    ->map(function ($subCategory) {
                        return '<span class="badge bg-light text-dark border me-1 mb-1">'
                            . e($subCategory->name)
                            . '</span>';
                    })
                    ->implode('');
            })
            ->addColumn('status', function ($category) {
                return $category->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>';
            })
            ->addColumn('actions', function ($category) {
                return '
                    <div class="d-flex justify-content-center gap-1">
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-primary edit-category-btn"
                            data-id="' . $category->id . '"
                            title="Edit">
                            <i class="fa-solid fa-pen"></i>
                        </button>

                        <button
                            type="button"
                            class="btn btn-sm btn-outline-danger delete-category-btn"
                            data-id="' . $category->id . '"
                            data-name="' . e($category->name) . '"
                            title="Delete">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns([
                'sub_categories',
                'status',
                'actions',
            ])
            ->make(true);
    }

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

        $validated['is_active'] = $request->boolean('is_active');

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully.',
            'category' => $category,
        ]);
    }

    public function show(Category $category)
    {
        $category->load('subCategories');

        return response()->json([
            'success' => true,
            'category' => $category,
        ]);
    }

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

    /*
    |--------------------------------------------------------------------------
    | Category Options
    |--------------------------------------------------------------------------
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

    /*
    |--------------------------------------------------------------------------
    | Sub Categories
    |--------------------------------------------------------------------------
    */

    public function subCategories(Category $category)
    {
        return response()->json(
            $category->subCategories()
                ->orderBy('name')
                ->get()
        );
    }

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
                'unique:sub_categories,name,' .
                $subCategory->id .
                ',id,category_id,' .
                $category->id,
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

    public function destroySubCategory(
        Category $category,
        SubCategory $subCategory
    ) {
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
