<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BookService
{
    public function getAllBooks($perPage = 10, $categoryId = null, $page = 1, $keyword = null)
    {
        $query = Book::query();

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('description', 'LIKE', '%' . $keyword . '%');
            });
        }

        // Sort theo ngày đăng gần nhất lên đầu
        $query->orderBy('created_at', 'desc');

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
        if (isset($data['title']) && (!isset($data['slug']) || empty($data['slug']))) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Xu ly status
        if (isset($data['stock'])) {
            if ($data['stock'] <= 0) {
                $data['status'] = 'out_of_stock';
            } else {
                $data['status'] = 'available';
            }
        }

        $book->update($data);
        return $book;
    }

    public function deleteBook($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();
    }

    /**
     * Get the most ordered books (top 10 by default)
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMostOrderedBooks($limit = 10)
    {
        // First, get the book IDs with their total quantities
        $bookIds = DB::table('books')
            ->select('books.id')
            ->selectRaw('SUM(order_items.quantity) as total_ordered_quantity')
            ->join('order_items', 'books.id', '=', 'order_items.book_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('books.id')
            ->orderByDesc('total_ordered_quantity')
            ->limit($limit)
            ->pluck('books.id');

        // Then get the full book models with relationships
        return Book::whereIn('id', $bookIds)
            ->with(['author', 'category'])
            ->get()
            ->map(function ($book) use ($bookIds) {
                // Add the total_ordered_quantity to each book
                $book->total_ordered_quantity = DB::table('order_items')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->where('order_items.book_id', $book->id)
                    ->where('orders.status', '!=', 'cancelled')
                    ->sum('order_items.quantity');
                return $book;
            })
            ->sortByDesc('total_ordered_quantity')
            ->values();
    }
}
