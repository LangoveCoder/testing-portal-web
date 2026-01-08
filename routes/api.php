<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\BiometricOperatorApiAuthController;
use App\Http\Controllers\Api\Auth\CollegeApiAuthController;
use App\Http\Controllers\Api\BiometricVerificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ============================================
// API AUTHENTICATION (Desktop App)
// ============================================

Route::post('/biometric-operator/login', [BiometricOperatorApiAuthController::class, 'login']);
Route::post('/college/login', [CollegeApiAuthController::class, 'login']);

// ============================================
// STUDENT BIOMETRIC APIs
// ============================================

Route::prefix('biometric')->group(function () {
    Route::get('colleges', [App\Http\Controllers\Api\StudentBiometricController::class, 'getActiveColleges']);
    Route::post('student/info', [App\Http\Controllers\Api\StudentBiometricController::class, 'getStudentInfo']);
    Route::post('student/upload-photo', [App\Http\Controllers\Api\StudentBiometricController::class, 'uploadTestPhoto']);
    Route::post('student/upload-photo-base64', [App\Http\Controllers\Api\StudentBiometricController::class, 'uploadTestPhotoBase64']);
    
    Route::post('fingerprint/upload-template', [App\Http\Controllers\Api\StudentBiometricController::class, 'uploadFingerprintTemplate']);
    Route::post('fingerprint/upload-image', [App\Http\Controllers\Api\StudentBiometricController::class, 'uploadFingerprintImage']);
    Route::post('fingerprint/verify', [App\Http\Controllers\Api\StudentBiometricController::class, 'verifyFingerprint']);
    Route::post('students/bulk-download', [App\Http\Controllers\Api\StudentBiometricController::class, 'bulkDownload']);
});

// ============================================
// FINGERPRINT VERIFICATION APIs
// ============================================

Route::prefix('biometric-verification')->group(function () {
    Route::get('/search', [BiometricVerificationController::class, 'search']);
    Route::post('/verify', [BiometricVerificationController::class, 'verify']);
    Route::get('/history', [BiometricVerificationController::class, 'history']);
    Route::get('/stats', [BiometricVerificationController::class, 'stats']);
});

// ============================================
// BIOMETRIC OPERATOR APIs
// ============================================

Route::prefix('biometric-operator/registration')->group(function () {
    Route::post('/search-student', [App\Http\Controllers\BiometricOperator\RegistrationController::class, 'searchStudent']);
    Route::post('/save-fingerprint', [App\Http\Controllers\BiometricOperator\RegistrationController::class, 'saveFingerprint']);
});