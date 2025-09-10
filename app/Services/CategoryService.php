<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class CategoryService
{
    public function store(array $data)
    {
        if (isset($data['image'])) {
            $data['image'] = $data['image']->store('categories', 'public');
        }

        return Category::create($data);
    }

    public function update(Category $category, array $data)
    {
        if (isset($data['image'])) {
            // Xóa ảnh cũ nếu có
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $data['image']->store('categories', 'public');
        }

        $category->update($data);
        return $category;
    }

    public function delete(Category $category)
    {
        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }
        return $category->delete();
    }
}
