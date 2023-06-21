<?php

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class RecipientValidator
{
    public function validate($data)
    {
      return Validator::make($data, $this->rules(), $this->messages());
    }

    public function rules(): array
    {
      return [
        'recipient.name' => ['required_with:recipient.email'],
        'recipient.email' => ['required_with:recipient.name', 'email']
      ];
    }

    public function messages(): array
    {
      return [
        'recipient.name.required_with' => 'The :attribute must be provided with recipient.email',
        'recipient.email.required_with' => 'The :attribute must be provided with recipient.name',
      ];
    }

    public function errorCode(): string
    {
      return 'invalid_recipient';
    }
}
