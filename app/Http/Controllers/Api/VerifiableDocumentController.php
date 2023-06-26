<?php

namespace App\Http\Controllers\Api;

use App\Models\VerifiableDocument;
use App\Http\Controllers\Controller;

use App\Http\Requests\VerifiableDocumentRequest;
use App\Services\DocumentService;

class VerifiableDocumentController extends Controller
{
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

                // store result
                $verifiableDocument = new VerifiableDocument;
                $verifiableDocument->file_type = $request->file->extension();

                if (($verifiedResult)) {
                    $verifiableDocument->verified = 0;
                    $verifiableDocument->save();

                    return response()->json([
                        'status_code' => $verifiedResult['status_code'],
                        'error' => $verifiedResult['error']
                    ], 200);
                } else {
                    $verifiableDocument->verified = 1;
                    $verifiableDocument->save();

                    return response()->json([
                        'message' => 'File is valid.'
                    ], 200);
                }
            }
        } else {
            error_log("no file");
        }
    }
}
