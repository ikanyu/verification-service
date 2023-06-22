<?php

namespace App\Http\Controllers\Api;

use App\Validators\SignatureValidator;
use Debugbar;
use App\Http\Controllers\Controller;
use App\Validators\RecipientValidator;
use App\Validators\IssuerValidator;
use App\Http\Requests\VerifiableDocumentRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class VerifiableDocumentController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'posts' => "abc"
        ]);
    }

    public function store(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $fileContent = $file->get();
            $decodedContent = json_decode($fileContent, true);

            if (is_null($decodedContent)) {
                return response()->json([], 500);
            } else {
                $fieldsWithErrorMessagesArray = [];

                $recipientValidator = new RecipientValidator();
                $issuerValidator = new IssuerValidator();
                $signatureValidator = new SignatureValidator();

                $validatedRecipient = $recipientValidator->validate($decodedContent['data']);
                if ($validatedRecipient->fails()) {
                    array_push($fieldsWithErrorMessagesArray, $validatedRecipient->messages()->get('*'));
                    return response()->json([
                        'status_code' => $recipientValidator->errorCode(),
                        'errror' => $validatedRecipient->messages()->get('*')
                    ]);

                }
                $validatedIssuer = $issuerValidator->validate($decodedContent['data']);

                if ($validatedIssuer->fails()) {
                    array_push($fieldsWithErrorMessagesArray, $validatedIssuer->messages()->get('*'));

                    return response()->json([
                        'status_code' => $recipientValidator->errorCode(),
                        'errror' => $fieldsWithErrorMessagesArray
                    ]);
                }

                $validatedSignature = $signatureValidator->validate($decodedContent);
                if ($validatedSignature->fails()) {
                    array_push($fieldsWithErrorMessagesArray, $validatedSignature->messages()->get('*'));

                    return response()->json([
                        'status_code' => $signatureValidator->errorCode(),
                        'errror' => $fieldsWithErrorMessagesArray
                    ]);
                }
            }
        } else {
            error_log("no file");
        }
        return response()->noContent();
    }
}
