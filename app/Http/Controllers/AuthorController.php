<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthorService;

class AuthorController extends Controller
{
    protected $authorService;

    public function __construct(AuthorService $authorService)
    {
        $this->authorService = $authorService;
        // Middleware này sẽ yêu cầu đăng nhập cho mọi phương thức
        // NGOẠI TRỪ 'index' và 'show'
        $this->middleware('auth:sanctum')->except(['index', 'show']);
        // Chỉ admin mới được thêm/sửa/xóa
        $this->middleware('role:admin')->only(['store', 'update', 'destroy']);
    }

    public function index()
    {
        return response()->json($this->authorService->getAllAuthors());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
        ]);
        return response()->json($this->authorService->createAuthor($data), 201);
    }

    public function show($id)
    {
        $author = $this->authorService->getAuthorById($id);
        return response()->json($author);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'bio' => 'nullable|string',
        ]);
        return response()->json($this->authorService->updateAuthor($id, $data));
    }

    public function destroy($id)
    {
        $this->authorService->deleteAuthor($id);
        return response()->json(['message' => 'Author deleted']);
    }
}
