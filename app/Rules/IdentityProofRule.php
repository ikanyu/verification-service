<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class IdentityProofRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $baseUrl = 'https://dns.google';
        $recordType = 'TXT';
        $location = $value["location"];
        $response = HTTP::get("$baseUrl/resolve?name=$location&type=$recordType");
        $identityProofKey = $value["key"];

        if ($response->ok()) {
            $answers = $response->json()["Answer"];
            $validKey = false;

            foreach ($answers as $answer) {
                if (str_contains($answer["data"], $identityProofKey)) {
                    $validKey = true;
                    break;
                }
            }

            if ($validKey == false) {
                $fail('The file does not have a valid identityProof key.');
            }

        } else {
            $fail('The file does not have a valid issuer');
        }
    }
}
