<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifiableDocumentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'mimes:json']
        ];
    }
}
