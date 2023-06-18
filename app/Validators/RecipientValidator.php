<?php

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class RecipientValidator
{
    public function validate($data) {
      return Validator::make($data, $this->rules());
    }

    public function rules(): array
    {
      return [
        'recipient.name' => 'required_with:recipient.email',
        'recipient.email' => 'required_with:recipient.name',
        'issuer.name' => 'required_with:issuer.identityProof',
        'issuer.identityProof' => 'required_with:issuer.name',
      ];
    }
}
