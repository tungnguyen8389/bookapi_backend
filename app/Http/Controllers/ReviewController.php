<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReviewService;

class ReviewController extends Controller
{
    protected $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    public function index()
    {
        return response()->json($this->reviewService->getAll());
    }

    public function show($id)
    {
        return response()->json($this->reviewService->getById($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review = $this->reviewService->create($data);

        return response()->json($review, 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'rating'  => 'sometimes|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review = $this->reviewService->update($id, $data);

        return response()->json($review);
    }

    public function destroy($id)
    {
        $this->reviewService->delete($id);

        return response()->json(null, 204);
    }
}
