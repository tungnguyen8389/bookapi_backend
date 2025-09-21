<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Services\BookService;
use Illuminate\Support\Facades\Log;

class BookController extends Controller
{
    protected $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;

        // Middleware này sẽ yêu cầu đăng nhập cho mọi phương thức
        // NGOẠI TRỪ 'index' và 'show'
        $this->middleware('auth:sanctum')->except(['index', 'show']);

        // Chỉ admin mới được thêm/sửa/xóa
        $this->middleware('role:admin')->only(['store', 'update', 'destroy']);
    }

    // Lấy danh sách sách (có phân trang)
    public function index(Request $request)
    {
        // Hỗ trợ cả hai format pagination
        $pageIndex = (int) $request->get('pageIndex', $request->get('page', 1));
        $pageSize = (int) $request->get('pageSize', $request->get('limit', 10));

        $categoryId = $request->get('category_id');

        // Hỗ trợ cả hai format search
        $keyword = $request->get('keyword', $request->get('search'));

        // Debug: Log search parameter để kiểm tra
        if ($keyword) {
            Log::info('Search keyword received: ' . json_encode($keyword));
        }

        $books = $this->bookService->getAllBooks($pageSize, $categoryId, $pageIndex, $keyword);
        return response()->json($books);
    }

    // Xem chi tiết 1 sách
    public function show($id)
    {
        $book = $this->bookService->getBookById($id);
        return response()->json($book);
    }

    // Thêm mới
    public function store(StoreBookRequest $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $book = $this->bookService->createBook($request->validated());
        return response()->json($book, 201);
    }

    // Cập nhật
    public function update(UpdateBookRequest $request, $id)
    {
        $book = $this->bookService->updateBook($id, $request->validated());
        return response()->json($book);
    }

    // Xóa
    public function destroy($id)
    {
        $this->bookService->deleteBook($id);
        return response()->json(null, 204);
    }

    public function updateWithFiles(UpdateBookRequest $request, $id)
{
    $book = $this->bookService->updateBook($id, $request->validated());
    
    return response()->json([
        'message' => 'Book updated successfully',
        'data' => $book
    ]);
}
}
