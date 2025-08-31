<?php

namespace App\Services;

use App\Models\Book;

class BookService
{
    public function getAllBooks()
    {
        return Book::all();
    }

    public function getBookById($id)
    {
        return Book::findOrFail($id);
    }

    public function createBook(array $data)
{
    if (request()->hasFile('image')) {
        $path = request()->file('image')->store('books', 'public'); 
        $data['image_url'] = $path;
    }

    return Book::create($data);
}

    public function updateBook($id, array $data)
{
    $book = Book::findOrFail($id);

    if (request()->hasFile('image')) {
        $path = request()->file('image')->store('books', 'public');
        $data['image_url'] = $path;
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
