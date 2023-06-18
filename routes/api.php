<?php

use App\Http\Controllers\Api\VerifiableDocumentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::post('verifiable_documents', [VerifiableDocumentController::class, 'store']);

Route::apiResource('verifiable_documents', VerifiableDocumentController::class);

