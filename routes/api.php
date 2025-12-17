<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ============================================
// STUDENT BIOMETRIC APIs
// ============================================

Route::prefix('biometric')->group(function () {
    
    // Android App APIs
    Route::get('colleges', [App\Http\Controllers\Api\StudentBiometricController::class, 'getActiveColleges']);
    Route::post('student/info', [App\Http\Controllers\Api\StudentBiometricController::class, 'getStudentInfo']);
    Route::post('student/upload-photo', [App\Http\Controllers\Api\StudentBiometricController::class, 'uploadTestPhoto']);
    Route::post('student/upload-photo-base64', [App\Http\Controllers\Api\StudentBiometricController::class, 'uploadTestPhotoBase64']);
    
    // Biometric Module / Windows App APIs
    Route::post('fingerprint/upload-template', [App\Http\Controllers\Api\StudentBiometricController::class, 'uploadFingerprintTemplate']);
    Route::post('fingerprint/upload-image', [App\Http\Controllers\Api\StudentBiometricController::class, 'uploadFingerprintImage']);
    Route::post('fingerprint/verify', [App\Http\Controllers\Api\StudentBiometricController::class, 'verifyFingerprint']);
    Route::post('students/bulk-download', [App\Http\Controllers\Api\StudentBiometricController::class, 'bulkDownload']);
});