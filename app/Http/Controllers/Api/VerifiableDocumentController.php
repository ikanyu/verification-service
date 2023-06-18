<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Validators\RecipientValidator;
use App\Http\Requests\VerifiableDocumentRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class VerifiableDocumentController extends Controller
{
    public function index()
    {
        //  xdebug_info();
        // info("=========Heree=========");
        // dd($id);

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
            $decodedContent = json_decode($fileContent, true)['data'];

            if (is_null($decodedContent)) {
                return response()->json([], 500);
            } else {
                $validator = (new RecipientValidator())->validate($decodedContent);

                if ($validator->fails()) {
                    $fieldsWithErrorMessagesArray = $validator->messages()->get('*');

                    return response()->json([
                        'status' => true,
                        'errror' => $fieldsWithErrorMessagesArray
                    ]);
                }

                // $validated = $validator->validated();
            }

        } else {
            error_log("no file");
        }

        return response()->noContent();

    }

}
