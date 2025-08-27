<?php

namespace App\Services;

use App\Models\Author;

class AuthorService
{
    public function getAll()
    {
        return Author::with('books')->paginate(10);
    }

    public function getById($id)
    {
        return Author::with('books')->findOrFail($id);
    }

    public function create(array $data)
    {
        return Author::create($data);
    }

    public function update($id, array $data)
    {
        $author = Author::findOrFail($id);
        $author->update($data);
        return $author;
    }

    public function delete($id)
    {
        $author = Author::findOrFail($id);
        return $author->delete();
    }
}
