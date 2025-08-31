<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // role đã check ở middleware
    }

    public function rules(): array
    {
        return [
        'title'         => 'required|string|max:255',
        'slug'          => 'required|string|max:255|unique:books,slug',
        'price'         => 'required|numeric|min:0',
        'discount'      => 'nullable|numeric|min:0|max:100',
        'stock'         => 'required|integer|min:0',
        'status'        => 'required|in:available,out_of_stock',
        'description'   => 'nullable|string',
        'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // đổi từ image_url sang image upload
        'category_id'   => 'required|exists:categories,id',
        'author_id'     => 'required|exists:authors,id',
        'published_year'=> 'nullable|integer|min:1900|max:' . date('Y'),
        'publisher'     => 'nullable|string|max:255',
    ];
    }
}

