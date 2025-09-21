<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

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
            $data['image'] = $path;
        }

        // Nếu slug chưa có hoặc bị trống thì generate từ name (nếu name tồn tại)
        if (!isset($data['slug']) || empty($data['slug'])) {
            // Chỉ tạo slug nếu name tồn tại và không rỗng,giữ slug cũ
            if (isset($data['name']) && !empty($data['name'])) {
                $data['slug'] = Str::slug($data['name']);
            } else {
                $data['slug'] = $category->slug; // Giữ slug hiện tại nếu name không có
            }
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
