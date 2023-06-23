<?php

namespace App\Http\Controllers\Api;

// use App\Validators\SignatureValidator;
use Debugbar;
use App\Http\Controllers\Controller;
// use App\Validators\RecipientValidator;
// use App\Validators\IssuerValidator;
use App\Http\Requests\VerifiableDocumentRequest;
use App\Services\DocumentService;

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

    public function store(VerifiableDocumentRequest $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $fileContent = $file->get();
            $decodedContent = json_decode($fileContent, true);

            if (is_null($decodedContent)) {
                return response()->json([], 500);
            } else {
                $documentService = new DocumentService($decodedContent);
                $verifiedResult = $documentService->verify();

                if (($verifiedResult)) {
                    return response()->json([
                        'status_code' => $verifiedResult['status_code'],
                        'error' => $verifiedResult['error']
                    ]);
                } else {
                    return response()->json([
                        'message' => 'File is valid.'
                    ], 201);
                }
            }
        } else {
            error_log("no file");
        }
    }
}
