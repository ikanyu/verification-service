<?php

namespace Tests\Validators;

use App\Validators\IssuerValidator;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IssuerValidatorTest extends TestCase
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

    $this->data = json_decode($jsonString, true)['data'];
    $this->validator = new IssuerValidator();
  }

  public function test_that_missing_issuer_name_validator_return_error(): void
  {
    $data = $this->data;
    unset(($data)['issuer']['name']);

    $response = $this->validator->validate($data);

    $this->assertEquals(
      "The issuer.name must be provided together with recipient.identityProof",
      $response->errors()->get('issuer.name')[0]
    );
  }

  public function test_that_missing_issuer_identityProof_validator_return_error(): void
  {
    $data = $this->data;
    unset(($data)['issuer']['identityProof']);

    $response = $this->validator->validate($data);

    $this->assertEquals(
      "The issuer.identity proof must be provided together with issuer.name",
      $response->errors()->get('issuer.identityProof')[0]
    );
  }

  public function test_that_issuer_identity_proof_rule_return_error(): void
  {
    Http::fake([
      'dns.google/*' => Http::response('{"Status": 0}', 200, []),
    ]);

    $data = $this->data;
    $data['issuer']['identityProof']['location'] = 'abc';

    $response = $this->validator->validate($data);

    $this->assertEquals(
      "The file does not have a valid issuer",
      $response->errors()->get('issuer.identityProof')[0]
    );
  }

  public function test_that_invalid_issuer_identity_proof_rule_return_error(): void
  {
    $data = $this->data;
    Http::fake([
      'dns.google/*' => Http::response(
        json_encode(
          [
            'Answer' => [
              [
                'name' => 'ropstore.accredify.io.',
                'data' => "openatts a=dns-did; p=did:ethr:abc#controller; v=1.0;"
              ]
            ]
          ]
        ),
        200,
        []
      ),
    ]);

    $response = $this->validator->validate($data);

    $this->assertEquals(
      "The file does not have a valid identityProof key.",
      $response->errors()->get('issuer.identityProof')[0]
    );
  }
}
