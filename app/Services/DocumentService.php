<?php

namespace App\Services;

use App\Validators\RecipientValidator;
use App\Validators\IssuerValidator;
use App\Validators\SignatureValidator;

class DocumentService
{
  private $decodedContent;
  private $fieldsWithErrorMessages;

  public function __construct(array $decodedContent)
  {
    $this->decodedContent = $decodedContent;
    $this->fieldsWithErrorMessages = [];
  }
  public function verify(): array
  {
    $issuerValidationResponse = $this->validateIssuer();
    if (!is_null($issuerValidationResponse)) {
        return $issuerValidationResponse;
    }

    $recipientValidationResponse = $this->validateRecipient();
    if (!is_null($recipientValidationResponse)) {
        return $recipientValidationResponse;
    }

    $signatureValidationResponse = $this->validateSignature();
    if (!is_null($signatureValidationResponse)) {
        return $signatureValidationResponse;
    }

    return [];
  }

  private function validateRecipient()
  {
    $recipientValidator = new RecipientValidator();
    $validatedRecipient = $recipientValidator->validate($this->decodedContent['data']);

    if ($validatedRecipient->fails()) {
      array_push($this->fieldsWithErrorMessages, $validatedRecipient->messages()->get('*'));

      return [
        'status_code' => $recipientValidator->errorCode(),
        'error' => $validatedRecipient->messages()->get('*')
      ];
    }
  }

  private function validateIssuer()
  {
    $issuerValidator = new IssuerValidator();
    $validatedIssuer = $issuerValidator->validate($this->decodedContent['data']);

    if ($validatedIssuer->fails()) {
        array_push($this->fieldsWithErrorMessages, $validatedIssuer->messages()->get('*'));

        return [
            'status_code' => $issuerValidator->errorCode(),
            'error' => $this->fieldsWithErrorMessages
        ];
    }
  }

  private function validateSignature()
  {
    $signatureValidator = new SignatureValidator();
    $validatedSignature = $signatureValidator->validate($this->decodedContent);

    if ($validatedSignature->fails()) {
      array_push($this->fieldsWithErrorMessages, $validatedSignature->messages()->get('*'));

      return [
        'status_code' => $signatureValidator->errorCode(),
        'error' => $this->fieldsWithErrorMessages
      ];
    }
  }
}
