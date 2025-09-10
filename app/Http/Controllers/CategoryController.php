<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    protected $CategoryService;

    public function __construct(CategoryService $CategoryService)
    {
        $this->CategoryService = $CategoryService;

        // Middleware này sẽ yêu cầu đăng nhập cho mọi phương thức
        // NGOẠI TRỪ 'index' và 'show'
        $this->middleware('auth:sanctum')->except(['index', 'show']);

        // Chỉ admin mới được thêm/sửa/xóa
        $this->middleware('role:admin')->only(['store', 'update', 'destroy']);
    }


    public function index()
    {
        return response()->json(Category::all());
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = $this->CategoryService->store($request->validated());
        return response()->json($category, 201);
    }

    public function show($id)
    {
        $category = $this->CategoryService->getCategoryById($id);
        return response()->json($category);
    }

        // Cập nhật
    public function update(StoreCategoryRequest $request, $id)
    {
        $category = $this->CategoryService->update($id, $request->validated());
        return response()->json($category);
    }

    public function destroy(Category $category)
    {
        $this->CategoryService->delete($category);
        return response()->json(null, 204);
    }
}
