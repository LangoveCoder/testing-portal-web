<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SuperAdminAuthController;
use App\Http\Controllers\Auth\CollegeAuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ============================================
// PUBLIC WEBSITE ROUTES
// ============================================

// Home/Landing Page
Route::get('/', function () {
    return view('home');
})->name('home');

// About Routes
Route::get('/about-us', function () {
    return view('public.about.us');
})->name('about-us');

Route::get('/organogram', function () {
    return view('public.about.organogram');
})->name('organogram');

// Training Routes
Route::get('/training/events', function () {
    return view('public.training.events');
})->name('training.events');

Route::get('/training/materials', function () {
    return view('public.training.materials');
})->name('training.materials');

Route::get('/training/outcomes', function () {
    return view('public.training.outcomes');
})->name('training.outcomes');

// Tests Routes (Public)
Route::get('/tests/results', function () {
    return view('public.tests.results');
})->name('tests.results');

Route::get('/tests/answer-keys', function () {
    return view('public.tests.answer-keys');
})->name('tests.answer-keys');

Route::get('/tests/upcoming', function () {
    return view('public.tests.upcoming');
})->name('tests.upcoming');

Route::get('/tests/model-papers', function () {
    return view('public.tests.model-papers');
})->name('tests.model-papers');

// Achievements
Route::get('/achievements', function () {
    return view('public.achievements');
})->name('achievements');

// Contact
Route::get('/contact', function () {
    return view('public.contact');
})->name('contact');

// Student Portal (Public Access)
Route::get('/student-portal', [App\Http\Controllers\StudentController::class, 'checkRollNumber'])
    ->name('student.portal');

// ============================================
// SUPER ADMIN ROUTES
// ============================================

Route::prefix('super-admin')->name('super-admin.')->group(function () {
    // Authentication
    Route::get('/login', [SuperAdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [SuperAdminAuthController::class, 'login'])->name('login.post');
    
    // Protected Routes (require authentication)
    Route::middleware('auth:super_admin')->group(function () {
        Route::post('/logout', [SuperAdminAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [\App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/notifications/mark-all-read', [\App\Http\Controllers\SuperAdmin\DashboardController::class, 'markAllNotificationsRead'])->name('notifications.mark-all-read');
        
        // College Management
        Route::resource('colleges', \App\Http\Controllers\SuperAdmin\CollegeController::class);
        Route::get('colleges/{college}/add-test-districts', [\App\Http\Controllers\SuperAdmin\CollegeController::class, 'addTestDistricts'])->name('colleges.add-test-districts');
        Route::post('colleges/{college}/add-test-districts', [\App\Http\Controllers\SuperAdmin\CollegeController::class, 'storeTestDistricts'])->name('colleges.store-test-districts');
        Route::get('colleges/{college}/test-districts', [\App\Http\Controllers\SuperAdmin\CollegeController::class, 'getTestDistricts'])->name('colleges.test-districts');
        
        // Test Management
        Route::resource('tests', \App\Http\Controllers\SuperAdmin\TestController::class);

        // Student Management
        Route::resource('students', \App\Http\Controllers\SuperAdmin\StudentController::class);
        
        // Roll Number Generation
        Route::prefix('roll-numbers')->name('roll-numbers.')->group(function () {
            Route::get('/', [\App\Http\Controllers\SuperAdmin\RollNumberController::class, 'index'])->name('index');
            Route::get('/preview/{test}', [\App\Http\Controllers\SuperAdmin\RollNumberController::class, 'preview'])->name('preview');
            Route::post('/generate/{test}', [\App\Http\Controllers\SuperAdmin\RollNumberController::class, 'generate'])->name('generate');
            Route::post('/regenerate/{test}', [\App\Http\Controllers\SuperAdmin\RollNumberController::class, 'regenerate'])->name('regenerate');
        });
        
        // Result Management
        Route::prefix('results')->name('results.')->group(function () {
            Route::get('/', [\App\Http\Controllers\SuperAdmin\ResultController::class, 'index'])->name('index');
            Route::get('/{test}/upload', [\App\Http\Controllers\SuperAdmin\ResultController::class, 'create'])->name('create');
            Route::post('/{test}/upload', [\App\Http\Controllers\SuperAdmin\ResultController::class, 'store'])->name('store');
            Route::get('/{test}', [\App\Http\Controllers\SuperAdmin\ResultController::class, 'show'])->name('show');
            Route::post('/{test}/publish', [\App\Http\Controllers\SuperAdmin\ResultController::class, 'publish'])->name('publish');
            Route::post('/{test}/unpublish', [\App\Http\Controllers\SuperAdmin\ResultController::class, 'unpublish'])->name('unpublish');
            Route::delete('/{test}', [\App\Http\Controllers\SuperAdmin\ResultController::class, 'destroy'])->name('destroy');
        });
        
        // Audit Logs
        Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
            Route::get('/', [\App\Http\Controllers\SuperAdmin\AuditLogController::class, 'index'])->name('index');
            Route::get('/{auditLog}', [\App\Http\Controllers\SuperAdmin\AuditLogController::class, 'show'])->name('show');
            Route::post('/clear', [\App\Http\Controllers\SuperAdmin\AuditLogController::class, 'clear'])->name('clear');
        });
        
        // Bulk Upload
        Route::prefix('bulk-upload')->name('bulk-upload.')->group(function () {
            Route::get('/', [\App\Http\Controllers\SuperAdmin\BulkUploadController::class, 'index'])->name('index');
            Route::get('/tests/{college}', [\App\Http\Controllers\SuperAdmin\BulkUploadController::class, 'getTests'])->name('get-tests');
            Route::post('/download-template', [\App\Http\Controllers\SuperAdmin\BulkUploadController::class, 'downloadTemplate'])->name('download-template');
            Route::post('/upload', [\App\Http\Controllers\SuperAdmin\BulkUploadController::class, 'upload'])->name('upload');
            Route::get('/preview', [\App\Http\Controllers\SuperAdmin\BulkUploadController::class, 'preview'])->name('preview');
            Route::post('/import', [\App\Http\Controllers\SuperAdmin\BulkUploadController::class, 'import'])->name('import');
            Route::get('/download-errors', [\App\Http\Controllers\SuperAdmin\BulkUploadController::class, 'downloadErrors'])->name('download-errors');
        });

        // Merit Lists
        Route::prefix('merit-lists')->name('merit-lists.')->group(function () {
            Route::get('/', [\App\Http\Controllers\SuperAdmin\MeritListController::class, 'index'])->name('index');
            Route::get('/{test}', [\App\Http\Controllers\SuperAdmin\MeritListController::class, 'show'])->name('show');
            Route::get('/{test}/download', [\App\Http\Controllers\SuperAdmin\MeritListController::class, 'downloadExcel'])->name('download');
            Route::get('/{test}/download-all', [\App\Http\Controllers\SuperAdmin\MeritListController::class, 'downloadAllExcel'])->name('download-all');
            Route::get('/{test}/download-pdf', [\App\Http\Controllers\SuperAdmin\MeritListController::class, 'downloadComprehensivePdf'])->name('download-pdf');
        });

        // Seating Plans
        Route::prefix('seating-plans')->name('seating-plans.')->group(function () {
            Route::get('/', [\App\Http\Controllers\SuperAdmin\SeatingPlanController::class, 'index'])->name('index');
            Route::get('/{test}', [\App\Http\Controllers\SuperAdmin\SeatingPlanController::class, 'show'])->name('show');
            Route::get('/{test}/download', [\App\Http\Controllers\SuperAdmin\SeatingPlanController::class, 'download'])->name('download');
        });
        
        // Attendance Sheets
        Route::prefix('attendance-sheets')->name('attendance-sheets.')->group(function () {
            Route::get('/', [\App\Http\Controllers\SuperAdmin\AttendanceSheetController::class, 'index'])->name('index');
            Route::get('/{test}', [\App\Http\Controllers\SuperAdmin\AttendanceSheetController::class, 'show'])->name('show');
            Route::get('/{test}/download', [\App\Http\Controllers\SuperAdmin\AttendanceSheetController::class, 'download'])->name('download');
        });
        
        // Biometric Operators
        Route::prefix('biometric-operators')->name('biometric-operators.')->group(function () {
            Route::get('/', [\App\Http\Controllers\SuperAdmin\BiometricOperatorController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\SuperAdmin\BiometricOperatorController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\SuperAdmin\BiometricOperatorController::class, 'store'])->name('store');
            Route::get('/{biometricOperator}/edit', [\App\Http\Controllers\SuperAdmin\BiometricOperatorController::class, 'edit'])->name('edit');
            Route::put('/{biometricOperator}', [\App\Http\Controllers\SuperAdmin\BiometricOperatorController::class, 'update'])->name('update');
            Route::delete('/{biometricOperator}', [\App\Http\Controllers\SuperAdmin\BiometricOperatorController::class, 'destroy'])->name('destroy');
        });

        // Biometric Status
        Route::prefix('biometric-status')->name('biometric-status.')->group(function () {
            Route::get('/', [\App\Http\Controllers\SuperAdmin\BiometricStatusController::class, 'index'])->name('index');
            Route::get('/{student}', [\App\Http\Controllers\SuperAdmin\BiometricStatusController::class, 'show'])->name('show');
        });

        // Attendance Management
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/', [\App\Http\Controllers\SuperAdmin\AttendanceController::class, 'index'])->name('index');
            Route::get('/export', [\App\Http\Controllers\SuperAdmin\AttendanceController::class, 'export'])->name('export');
        });
    });
});

// ============================================
// COLLEGE ADMIN ROUTES
// ============================================

Route::prefix('college')->name('college.')->group(function () {
    // Authentication
    Route::get('/login', [CollegeAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [CollegeAuthController::class, 'login'])->name('login.post');
    
    // Protected Routes (require authentication)
    Route::middleware('auth:college')->group(function () {
        Route::post('/logout', [CollegeAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [\App\Http\Controllers\College\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/notifications/mark-all-read', [\App\Http\Controllers\College\DashboardController::class, 'markAllNotificationsRead'])->name('notifications.mark-all-read');
        
        // Student Management
        Route::resource('students', \App\Http\Controllers\College\StudentController::class);
        Route::get('tests/{test}/districts', [\App\Http\Controllers\College\StudentController::class, 'getTestDistricts'])->name('tests.districts');

        Route::post('/download-bulk-template', [\App\Http\Controllers\SuperAdmin\BulkUploadController::class, 'downloadTemplate'])->name('download-bulk-template');

        // Results
        Route::prefix('results')->name('results.')->group(function () {
            Route::get('/', [\App\Http\Controllers\College\ResultController::class, 'index'])->name('index');
            Route::get('/{test}', [\App\Http\Controllers\College\ResultController::class, 'show'])->name('show');
        });
        
        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [\App\Http\Controllers\College\ReportController::class, 'index'])->name('index');
            Route::post('/student-list', [\App\Http\Controllers\College\ReportController::class, 'downloadStudentList'])->name('download-student-list');
            Route::post('/result-report', [\App\Http\Controllers\College\ReportController::class, 'downloadResultReport'])->name('download-result-report');
        });
        
        // Fingerprint Verification (WEB INTERFACE)
        Route::prefix('fingerprint-verification')->name('fingerprint-verification.')->group(function () {
            Route::get('/', [\App\Http\Controllers\College\FingerprintVerificationController::class, 'index'])->name('index');
            Route::get('/history/{studentId}', [\App\Http\Controllers\College\FingerprintVerificationController::class, 'getVerificationHistory'])->name('history');
            Route::get('/statistics', [\App\Http\Controllers\College\FingerprintVerificationController::class, 'getStatistics'])->name('statistics');
        });

        // Biometric Status
        Route::prefix('biometric-status')->name('biometric-status.')->group(function () {
            Route::get('/', [\App\Http\Controllers\College\BiometricStatusController::class, 'index'])->name('index');
            Route::get('/{student}', [\App\Http\Controllers\College\BiometricStatusController::class, 'show'])->name('show');
        });
    });
});

// ============================================
// STUDENT PORTAL ROUTES
// ============================================

Route::prefix('student')->name('student.')->group(function () {
    // Check Roll Number
    Route::get('/check-roll-number', [App\Http\Controllers\StudentController::class, 'checkRollNumber'])
        ->name('check-roll-number');
    Route::post('/check-roll-number', [App\Http\Controllers\StudentController::class, 'searchRollNumber'])
        ->name('check-roll-number.search');
    
    // Check Result
    Route::get('/check-result', [App\Http\Controllers\StudentController::class, 'checkResult'])
        ->name('check-result');
    Route::post('/check-result', [App\Http\Controllers\StudentController::class, 'searchResult'])
        ->name('check-result.search');

    // Roll Slip PDF Download
    Route::post('/roll-slip/download', [App\Http\Controllers\Student\RollNumberSlipController::class, 'download'])->name('roll-slip.download');
});

// ============================================
// API ROUTES (Android App & Desktop Apps)
// ============================================

// Android App - Biometric APIs
Route::prefix('api/biometric')->group(function () {
    Route::get('colleges', [App\Http\Controllers\Api\StudentBiometricController::class, 'getActiveColleges']);
    Route::post('student/info', [App\Http\Controllers\Api\StudentBiometricController::class, 'getStudentInfo']);
    Route::post('student/upload-photo', [App\Http\Controllers\Api\StudentBiometricController::class, 'uploadTestPhoto']);
});

// Desktop App API Routes (NO CSRF, NO SESSION)
Route::prefix('api/biometric-operator/registration')->group(function () {
    Route::post('/search-student', [App\Http\Controllers\BiometricOperator\RegistrationController::class, 'searchStudent']);
    Route::post('/save-fingerprint', [App\Http\Controllers\BiometricOperator\RegistrationController::class, 'saveFingerprint']);
});

Route::prefix('api/college/fingerprint-verification')->group(function () {
    Route::post('/load-student', [App\Http\Controllers\College\FingerprintVerificationController::class, 'loadStudent']);
    Route::post('/log', [App\Http\Controllers\College\FingerprintVerificationController::class, 'logVerification']);
});

// ============================================
// BIOMETRIC OPERATOR WEB ROUTES (WITH AUTH)
// ============================================

Route::prefix('biometric-operator')->name('biometric-operator.')->group(function () {
    // Authentication
    Route::get('/login', [App\Http\Controllers\Auth\BiometricOperatorAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\BiometricOperatorAuthController::class, 'login'])->name('login.post');
    
    // Protected Routes (require authentication)
    Route::middleware('auth:biometric_operator')->group(function () {
        Route::post('/logout', [App\Http\Controllers\Auth\BiometricOperatorAuthController::class, 'logout'])->name('logout');
        
        Route::get('/dashboard', function () {
            $operator = Auth::guard('biometric_operator')->user();
            $assignedTests = $operator->tests();
            
            return view('biometric_operator.dashboard', compact('assignedTests'));
        })->name('dashboard');
        
        // Biometric Registration (WEB INTERFACE)
        Route::prefix('registration')->name('registration.')->group(function () {
            Route::get('/', [App\Http\Controllers\BiometricOperator\RegistrationController::class, 'index'])->name('index');
            Route::get('/history', [App\Http\Controllers\BiometricOperator\RegistrationController::class, 'history'])->name('history');
        });
    });
});