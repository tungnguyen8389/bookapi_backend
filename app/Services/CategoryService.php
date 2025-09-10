<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryService
{
    public function store(array $data)
    {
        if (isset($data['image'])) {
            $data['image'] = $data['image']->store('categories', 'public');
        }

        return Category::create($data);
    }

    public function update($id, array $data)
    {
        $category = Category::findOrFail($id);

        // Nếu có file image upload thì lưu và gán vào image_url
        if (request()->hasFile('image')) {
            $path = request()->file('image')->store('categories', 'public');
            $data['image_url'] = $path;
        }

        // Nếu slug chưa có hoặc bị trống thì generate từ name
        if (!isset($data['slug']) || empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
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

    public function getCategoryById($id)
    {
        return Category::findOrFail($id);
    }
}
