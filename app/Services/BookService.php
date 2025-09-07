<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Support\Str;

class BookService
{
    public function getAllBooks($perPage = 10, $categoryId = null, $page = 1)
    {
        $query = Book::query();
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function getBookById($id)
    {
        return Book::findOrFail($id);
    }

    public function createBook(array $data)
    {
        // Nếu có file image upload thì lưu và gán vào image_url
        if (request()->hasFile('image')) {
            $path = request()->file('image')->store('books', 'public');
            $data['image_url'] = $path;
        }

        // Nếu chưa có slug thì tự động tạo từ title
        if (!isset($data['slug']) || empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Xử lý status theo stock, nếu rỗng hoặc bằng 0 thì đặt là out_of_stock
        if (!isset($data['stock']) || $data['stock'] <= 0) {
            $data['status'] = 'out_of_stock';
        } else {
            $data['status'] = 'available';
        }

        return Book::create($data);
    }

    public function updateBook($id, array $data)
    {
        $book = Book::findOrFail($id);

        // Nếu có file image upload thì lưu và gán vào image_url
        if (request()->hasFile('image')) {
            $path = request()->file('image')->store('books', 'public');
            $data['image_url'] = $path;
        }

        // Nếu slug chưa có hoặc bị trống thì generate từ title
        if (!isset($data['slug']) || empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Xu ly status
        if (!isset($data['stock']) || $data['stock'] <= 0) {
            $data['status'] = 'out_of_stock';
        } else {
            $data['status'] = 'available';
        }

        $book->update($data);
        return $book;
    }

    public function deleteBook($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();
    }
}
