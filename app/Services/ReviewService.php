<?php

namespace App\Services;

use App\Models\Review;

class ReviewService
{
    public function getAll()
    {
        return Review::with(['user', 'book'])->get();
    }

    public function getById($id)
    {
        return Review::with(['user', 'book'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Review::create($data);
    }

    public function update($id, array $data)
    {
        $review = Review::findOrFail($id);
        $review->update($data);
        return $review;
    }

    public function delete($id)
    {
        $review = Review::findOrFail($id);
        return $review->delete();
    }
}
