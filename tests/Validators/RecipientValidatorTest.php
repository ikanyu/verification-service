<?php

namespace Tests\Validators;

use App\Validators\RecipientValidator;
use Tests\TestCase;

class RecipientValidatorTest extends TestCase
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
        }
      }
    }';

    $this->data = json_decode($jsonString, true)['data'];
    $this->validator = new RecipientValidator();
  }

  public function test_that_missing_recipient_name_validator_return_error(): void
  {
    unset(($this->data)['recipient']['name']);

    $response = $this->validator->validate($this->data);

    $this->assertEquals(
      "The recipient.name must be provided with recipient.email",
      $response->errors()->get('recipient.name')[0]
    );
  }

  public function test_that_missing_recipient_email_validator_return_error(): void
  {
    unset(($this->data)['recipient']['email']);

    $response = $this->validator->validate($this->data);
    $errorMessage = $response->messages()->get('*');

    $this->assertEquals(
      "The recipient.email must be provided with recipient.name",
      $response->errors()->get('recipient.email')[0]
    );
  }

  public function test_that_invalid_recipient_email_validator_return_error(): void
  {
    ($this->data)['recipient']['email'] = 'xxxx';

    $response = $this->validator->validate($this->data);

    $this->assertEquals(
      "The recipient.email field must be a valid email address.",
      $response->errors()->get('recipient.email')[0]
    );
  }
}
