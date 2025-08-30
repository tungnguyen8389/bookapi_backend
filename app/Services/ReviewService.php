<?php

namespace App\Services;

use App\Models\Review;

class ReviewService
{
    public function getReviewsByBook($bookId, $perPage = 10)
    {
        return Review::with('user')
            ->where('book_id', $bookId)
            ->paginate($perPage);
    }

    public function createReview($data)
    {
        return Review::create($data);
    }

    public function updateReview($id, $data)
    {
        $review = Review::findOrFail($id);
        $review->update($data);
        return $review;
    }

    public function deleteReview($id)
    {
        $review = Review::findOrFail($id);
        return $review->delete();
    }
}

