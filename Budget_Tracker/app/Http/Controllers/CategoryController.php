<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Global (system) categories
        $systemCategories = Category::whereNull('user_id')
                                    ->orderBy('name')
                                    ->get();

        // User's custom categories
        $customCategories = Category::where('user_id', $user->id)
                                    ->where('is_custom', true)
                                    ->orderBy('name')
                                    ->get();

        return view('categories.index', compact('systemCategories', 'customCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => ['required', 'string', 'max:28'],
            'color' => ['required', 'string', 'max:28', 'regex:/^#[0-9A-Fa-f]{3,6}$/'],
        ]);

        // Prevent duplicate names for this user
        $exists = Category::where('user_id', Auth::id())
                          ->whereRaw('LOWER(name) = ?', [strtolower($request->name)])
                          ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['name' => 'You already have a category with this name.']);
        }

        Category::create([
            'user_id'   => Auth::id(),
            'name'      => $request->name,
            'color'     => $request->color,
            'is_custom' => true,
        ]);

        return back()->with('success', 'Category "' . $request->name . '" created.');
    }

    public function update(Request $request, Category $category)
    {
        abort_unless($category->user_id === Auth::id(), 403);

        $request->validate([
            'name'  => ['required', 'string', 'max:28'],
            'color' => ['required', 'string', 'max:28', 'regex:/^#[0-9A-Fa-f]{3,6}$/'],
        ]);

        $category->update([
            'name'  => $request->name,
            'color' => $request->color,
        ]);

        return back()->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        abort_unless($category->user_id === Auth::id(), 403);

        $category->delete();

        return back()->with('success', 'Category deleted.');
    }
}