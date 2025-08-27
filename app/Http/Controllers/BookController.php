<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BookService;

class BookController extends Controller
{
    protected $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    // GET /api/books
    public function index()
    {
        $books = $this->bookService->getAll();
        return response()->json($books);
    }

    // GET /api/books/{id}
    public function show($id)
    {
        $book = $this->bookService->getById($id);
        return response()->json($book);
    }

    // POST /api/books
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);

        $book = $this->bookService->create($data);
        return response()->json($book, 201);
    }

    // PUT /api/books/{id}
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'author_id' => 'sometimes|exists:authors,id',
            'category_id' => 'sometimes|exists:categories,id',
            'price' => 'sometimes|numeric',
            'stock' => 'sometimes|integer',
        ]);

        $book = $this->bookService->update($id, $data);
        return response()->json($book);
    }

    // DELETE /api/books/{id}
    public function destroy($id)
    {
        $this->bookService->delete($id);
        return response()->json(['message' => 'Book deleted successfully']);
    }
}
