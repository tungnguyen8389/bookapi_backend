<?php

namespace App\Http\Controllers;

use App\Services\ReviewService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    protected $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
        // Chỉ bắt buộc login cho store, update, destroy
        $this->middleware('auth:sanctum')->only(['store','update','destroy']);
    }

    public function index($bookId, Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $reviews = $this->reviewService->getReviewsByBook($bookId, $perPage);
        return response()->json($reviews);
    }

    public function store(Request $request, $bookId)
    {
        $data = $request->validate([
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $data['user_id'] = auth()->id();
        $data['book_id'] = $bookId;

        $review = $this->reviewService->createReview($data);
        return response()->json($review, 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review = $this->reviewService->updateReview($id, $data);
        return response()->json($review);
    }

    public function destroy($id)
    {
        $this->reviewService->deleteReview($id);
        return response()->json(null, 204);
    }
}

