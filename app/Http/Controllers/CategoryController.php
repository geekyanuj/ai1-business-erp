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

        Category::create($validated);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Show category
     */
    public function show(Category $category)
    {
        $category->load('subCategories');

        return response()->json($category);
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

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Delete category
     */
    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return redirect()
                ->route('categories.index')
                ->with('error', 'Cannot delete category because products are assigned to it.');
        }

        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category deleted successfully.');
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
            ->where('is_active', true)
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        return response()->json($subCategories);
    }
}
