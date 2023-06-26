<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class SignatureRule implements ValidationRule
{
    private $data;
    private $signature;

    public function __construct($decodedContent)
    {
        $this->data = $decodedContent["data"];
        $this->signature = $decodedContent["signature"];
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $ritit = new RecursiveIteratorIterator(new RecursiveArrayIterator($this->data));
        $results = array();
        foreach ($ritit as $leafValue) {
            $keys = array();
            foreach (range(0, $ritit->getDepth()) as $depth) {
                $keys[] = $ritit->getSubIterator($depth)->key();
            }
            $results[join('.', $keys)] = $leafValue;
        }
        $results;
        $hashedResults = array();

        foreach ($results as $key => $value) {
            $temp_array = array();
            $temp_array[$key] = $value;
            $temp_array;
            array_push($hashedResults, hash('sha256', json_encode($temp_array)));
        }
        sort($hashedResults);
        $hashedFinalResult = hash('sha256', json_encode($hashedResults));

        if ($hashedFinalResult !== $this->signature["targetHash"]) {
            $fail('The file does not have a valid signature.');
        }
    }
}
