<?php

namespace Tests\Services;

use App\Services\DocumentService;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentServiceTest extends TestCase
{
  private $service;
  private $decodedContent;

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

    $this->decodedContent = json_decode($jsonString, true);
  }

  public function test_that_json_is_valid(): void
  {
    $service = new DocumentService($this->decodedContent);
    $this->assertEquals([], $service->verify());
  }

  public function test_that_missing_recipient_name_return_error(): void
  {
    $decodedContent = $this->decodedContent;
    unset($decodedContent['data']['recipient']['name']);

    $service = new DocumentService($decodedContent);
    $verifiedService = $service->verify();

    $this->assertArrayHasKey('error', $verifiedService);
    $this->assertEquals('invalid_recipient', $verifiedService['status_code']);
  }

  public function test_that_missing_recipient_email_return_error(): void
  {
    $decodedContent = $this->decodedContent;
    unset($decodedContent['data']['recipient']['email']);

    $service = new DocumentService($decodedContent);
    $verifiedService = $service->verify();

    $this->assertArrayHasKey('error', $verifiedService);
    $this->assertEquals('invalid_recipient', $verifiedService['status_code']);
  }

  public function test_that_missing_issuer_name_return_error(): void
  {
    $decodedContent = $this->decodedContent;
    unset($decodedContent['data']['issuer']['name']);

    $service = new DocumentService($decodedContent);
    $verifiedService = $service->verify();

    $this->assertArrayHasKey('error', $verifiedService);
    $this->assertEquals('invalid_issuer', $verifiedService['status_code']);
  }

  public function test_that_missing_identity_proof_return_error(): void
  {
    $decodedContent = $this->decodedContent;
    unset($decodedContent['data']['issuer']['identityProof']);

    $service = new DocumentService($decodedContent);
    $verifiedService = $service->verify();

    $this->assertArrayHasKey('error', $verifiedService);
    $this->assertEquals('invalid_issuer', $verifiedService['status_code']);
  }

  public function test_that_missing_target_hash_return_error(): void
  {
    $decodedContent = $this->decodedContent;
    unset($decodedContent['signature']['targetHash']);

    $service = new DocumentService($decodedContent);
    $verifiedService = $service->verify();

    $this->assertArrayHasKey('error', $verifiedService);
    $this->assertEquals('invalid_signature', $verifiedService['status_code']);
  }
}
