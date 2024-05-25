<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IdentifierRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'identifier' => 'required|integer|min:1|max:255',
        ];
    }
}
