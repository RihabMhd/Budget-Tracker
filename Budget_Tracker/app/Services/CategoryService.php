<?php

namespace App\Services;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function getForUser(User $user): array
    {
        $systemCategories = Category::whereNull('user_id')
                                    ->orderBy('name')
                                    ->get();

        $customCategories = Category::where('user_id', $user->id)
                                    ->where('is_custom', true)
                                    ->orderBy('name')
                                    ->get();

        return compact('systemCategories', 'customCategories');
    }

    public function store(User $user, array $data): Category|false
    {
        $exists = Category::where('user_id', $user->id)
                          ->whereRaw('LOWER(name) = ?', [strtolower($data['name'])])
                          ->exists();

        if ($exists) {
            return false;
        }

        return Category::create([
            'user_id'   => $user->id,
            'name'      => $data['name'],
            'color'     => $data['color'],
            'is_custom' => true,
        ]);
    }

    public function update(Category $category, array $data): void
    {
        $category->update([
            'name'  => $data['name'],
            'color' => $data['color'],
        ]);
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}