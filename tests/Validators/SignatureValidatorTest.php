<?php

namespace Tests\Validators;

use App\Validators\SignatureValidator;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SignatureValidatorTest extends TestCase
{
  private $data;
  private $validator;

  protected function setUp(): void
  {
    parent::setUp();

    $jsonString =
      '{
      "data": {
        "id": "63c79bd9303530645d1cca00",
        "name": "Certificate of Completion",
        "recipient": {
          "name": "Marty McFly",
          "email": "marty.mcfly@gmail.com"
        },
        "issuer": {
          "name": "Accredify",
          "identityProof": {
            "type": "DNS-DID",
            "key": "did:ethr:0x05b642ff12a4ae545357d82ba4f786f3aed84214#controller",
            "location": "ropstore.accredify.io"
          }
        },
        "issued": "2022-12-23T00:00:00+08:00"
      },
      "signature": {
        "type": "SHA3MerkleProof",
        "targetHash": "288f94aadadf486cfdad84b9f4305f7d51eac62db18376d48180cc1dd2047a0e"
      }
    }';

    $this->data = json_decode($jsonString, true);
    $this->validator = new SignatureValidator();
  }

  public function test_that_missing_signature_target_hash_validator_return_error(): void
  {
    unset(($this->data)['signature']['targetHash']);

    $response = $this->validator->validate($this->data);

    $this->assertEquals(
      "The signature.target hash must be provided to be validated",
      $response->errors()->get('signature.targetHash')[0]
    );
  }
}