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
    }

    public function index()
    {
        return response()->json($this->authorService->getAll());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
        ]);
        return response()->json($this->authorService->create($data), 201);
    }

    public function show($id)
    {
        return response()->json($this->authorService->getById($id));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'bio' => 'nullable|string',
        ]);
        return response()->json($this->authorService->update($id, $data));
    }

    public function destroy($id)
    {
        $this->authorService->delete($id);
        return response()->json(['message' => 'Author deleted']);
    }
}
