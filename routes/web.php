<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SuperAdminAuthController;
use App\Http\Controllers\Auth\CollegeAuthController;

// Home/Landing Page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Super Admin Routes
Route::prefix('super-admin')->name('super-admin.')->group(function () {
    // Authentication
    Route::get('/login', [SuperAdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [SuperAdminAuthController::class, 'login'])->name('login.post');
    
    // Protected Routes (require authentication)
    Route::middleware('auth:super_admin')->group(function () {
        Route::post('/logout', [SuperAdminAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', function () {
            return view('super_admin.dashboard');
        })->name('dashboard');
        
        // College Management
        Route::resource('colleges', \App\Http\Controllers\SuperAdmin\CollegeController::class);
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
// Merit Lists
Route::prefix('merit-lists')->name('merit-lists.')->group(function () {
    Route::get('/', [\App\Http\Controllers\SuperAdmin\MeritListController::class, 'index'])->name('index');
    Route::get('/{test}', [\App\Http\Controllers\SuperAdmin\MeritListController::class, 'show'])->name('show');
    Route::get('/{test}/download', [\App\Http\Controllers\SuperAdmin\MeritListController::class, 'downloadExcel'])->name('download');
    Route::get('/{test}/download-all', [\App\Http\Controllers\SuperAdmin\MeritListController::class, 'downloadAllExcel'])->name('download-all');
    Route::get('/{test}/download-pdf', [\App\Http\Controllers\SuperAdmin\MeritListController::class, 'downloadComprehensivePdf'])->name('download-pdf');
});
    });
});

// College Admin Routes
Route::prefix('college')->name('college.')->group(function () {
    // Authentication
    Route::get('/login', [CollegeAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [CollegeAuthController::class, 'login'])->name('login.post');
    
    // Protected Routes (require authentication)
    Route::middleware('auth:college')->group(function () {
        Route::post('/logout', [CollegeAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', function () {
    $college = Auth::guard('college')->user();
    $totalStudents = \App\Models\Student::whereHas('test', function($query) use ($college) {
        $query->where('college_id', $college->id);
    })->count();
    
    $studentsWithRollNumbers = \App\Models\Student::whereHas('test', function($query) use ($college) {
        $query->where('college_id', $college->id);
    })->whereNotNull('roll_number')->count();
    
    $availableTests = \App\Models\Test::where('college_id', $college->id)
        ->where('registration_deadline', '>=', now())
        ->orderBy('test_date')
        ->get();
    
    return view('college.dashboard', compact('totalStudents', 'studentsWithRollNumbers', 'availableTests'));
})->name('dashboard');
        
        // Student Management
        Route::resource('students', \App\Http\Controllers\College\StudentController::class);
        Route::get('tests/{test}/districts', [\App\Http\Controllers\College\StudentController::class, 'getTestDistricts'])->name('tests.districts');

        Route::post('/download-bulk-template', [\App\Http\Controllers\SuperAdmin\BulkUploadController::class, 'downloadTemplate'])->name('download-bulk-template');

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
    });
});

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