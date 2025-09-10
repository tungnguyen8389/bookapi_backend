<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchBookRequest extends FormRequest
{
    public function authorize()
    {
        return true; // tạm cho phép, có thể chỉnh quyền
    }

    public function rules()
    {
        return [
            'q'      => 'nullable|string|max:255',
            'page'   => 'nullable|integer|min:1',
            'limit'  => 'nullable|integer|min:1|max:100',
        ];
    }
}
