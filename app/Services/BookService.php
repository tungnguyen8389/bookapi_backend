<?php

namespace App\Services;

use App\Models\Book;

class BookService
{
    // Lấy tất cả sách (có phân trang)
    public function getAll($perPage = 10)
    {
        return Book::with(['author', 'category'])->paginate($perPage);
    }

    // Lấy 1 sách theo id
    public function getById($id)
    {
        return Book::with(['author', 'category'])->findOrFail($id);
    }

    // Thêm sách mới
    public function create(array $data)
    {
        return Book::create($data);
    }

    // Cập nhật sách
    public function update($id, array $data)
    {
        $book = Book::findOrFail($id);
        $book->update($data);
        return $book;
    }

    // Xóa sách
    public function delete($id)
    {
        $book = Book::findOrFail($id);
        return $book->delete();
    }
}
