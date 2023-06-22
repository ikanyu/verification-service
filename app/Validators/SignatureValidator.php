<?php

namespace App\Validators;

use App\Rules\SignatureRule;
use Illuminate\Support\Facades\Validator;

class SignatureValidator
{
  public function validate($data)
  {
    return Validator::make($data, $this->rules($data), $this->messages());
  }

  public function rules($data): array
  {
    return [
      'signature.targetHash' => ['required', new SignatureRule($data)]
    ];
  }

  public function messages(): array
  {
    return [
      'signature.targetHash.required' => 'The :attribute must be provided to be validated',
    ];
  }

  public function errorCode(): string
  {
    return 'invalid_signature';
  }
}
