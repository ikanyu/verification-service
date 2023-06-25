<?php

namespace Tests\Api\Controllers;

use App\Http\Controllers\Api\VerifiableDocumentController;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

class VerifiableDocumentControllerTest extends TestCase
{
  private $service;
  private $decodedContent;

  use RefreshDatabase;
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

    // $this->decodedContent = json_decode($jsonString, true);
    $this->decodedContent = $jsonString;
  }

  public function test_valid_file_is_verified_and_stored(): void
  {
    $response = $this->post(
      '/api/verifiable_documents',
      ['file' => UploadedFile::fake()->createWithContent('sample.json', $this->decodedContent)]
    );

    $response->assertStatus(Response::HTTP_OK);
    // $this->assertArrayHasKey('ddd', []);

    $this->assertDatabaseHas('verifiable_documents', [
      'file_type' => 'json',
      'verified' => 1
    ]);
  }

  public function test_invalid_file_is_verified_and_stored(): void
  {
    $response = $this->post(
      '/api/verifiable_documents',
      [
        'file' => UploadedFile::fake()->createWithContent(
          'sample.json',
      '{
      "data": {
        "id": "63c79bd9303530645d1cca00",
        "name": "Certificate of Completion",
        "recipient": {
          "name": "Marty McFly",
          "email": "marty.mcfly@gmail.com"
        }
      },
      "signature": {
        "type": "SHA3MerkleProof",
        "targetHash": "288f94aadadf486cfdad84b9f4305f7d51eac62db18376d48180cc1dd2047a0e"
      }
    }'
        )
      ]
    );

    $response->assertStatus(Response::HTTP_OK);

    $this->assertDatabaseHas('verifiable_documents', [
      'file_type' => 'json',
      'verified' => 0
    ]);
  }
}
