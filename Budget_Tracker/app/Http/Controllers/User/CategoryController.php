<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $categoryService) {}

    public function index()
    {
        $data = $this->categoryService->getForUser(Auth::user());

        return view('categories.index', $data);
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = $this->categoryService->store(Auth::user(), $request->validated());

        if ($category === false) {
            return back()
                ->withInput()
                ->withErrors(['name' => 'You already have a category with this name.']);
        }

        return back()->with('success', 'Category "' . $category->name . '" created.');
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        abort_unless($category->user_id === Auth::id(), 403);

        $this->categoryService->update($category, $request->validated());

        return back()->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        abort_unless($category->user_id === Auth::id(), 403);

        $this->categoryService->delete($category);

        return back()->with('success', 'Category deleted.');
    }
}