<?php

namespace App\Validators;

use App\Rules\IdentityProofRule;
use Illuminate\Support\Facades\Validator;

class IssuerValidator
{
  public function validate($data)
  {
    return Validator::make($data, $this->rules(), $this->messages());
  }

  public function rules(): array
  {
    return [
      'issuer.name' => ['required_with:issuer.identityProof'],
      'issuer.identityProof' => ['required_with:issuer.name', new IdentityProofRule()]
    ];
  }

  public function messages(): array
  {
    return [
      'issuer.name.required_with' => 'The :attribute must be provided together with recipient.identityProof',
      'issuer.identityProof.required_with' => 'The :attribute must be provided together with issuer.name',
    ];
  }

  public function errorCode(): string
  {
    return 'invalid_issuer';
  }
}
