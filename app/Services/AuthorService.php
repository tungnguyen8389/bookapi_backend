<?php

namespace App\Services;

use App\Models\Author;

class AuthorService
{
    public function getAllAuthors()
    {
        return Author::all();
    }

    public function getAuthorById($id)
    {
        return Author::findOrFail($id);
    }

    public function createAuthor(array $data)
    {
        return Author::create($data);
    }

    public function updateAuthor($id, array $data)
    {
        $author = Author::findOrFail($id);
        $author->update($data);
        return $author;
    }

    public function deleteAuthor($id)
    {
        $author = Author::findOrFail($id);
        $author->delete();
    }
}
