<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix BiometricOperator table structure
        Schema::table('biometric_operators', function (Blueprint $table) {
            // Drop old JSON columns if they exist
            if (Schema::hasColumn('biometric_operators', 'assigned_colleges')) {
                $table->dropColumn('assigned_colleges');
            }
            if (Schema::hasColumn('biometric_operators', 'assigned_tests')) {
                $table->dropColumn('assigned_tests');
            }
            
            // Add proper foreign key relationships
            if (!Schema::hasColumn('biometric_operators', 'assigned_college_id')) {
                $table->foreignId('assigned_college_id')->nullable()->constrained('colleges')->onDelete('set null');
            }
            if (!Schema::hasColumn('biometric_operators', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            
            // Ensure created_by field is properly set up
            if (!Schema::hasColumn('biometric_operators', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('super_admins')->onDelete('set null');
            }
        });

        // Fix Students table - ensure all biometric fields are properly structured
        Schema::table('students', function (Blueprint $table) {
            // Add college_id foreign key if missing
            if (!Schema::hasColumn('students', 'college_id')) {
                $table->foreignId('college_id')->nullable()->constrained('colleges')->onDelete('set null');
            }
            
            // Ensure test_photo is TEXT type (for base64 images)
            if (Schema::hasColumn('students', 'test_photo')) {
                $table->text('test_photo')->nullable()->change();
            }
        });

        // Fix FingerprintVerification table structure
        Schema::table('fingerprint_verifications', function (Blueprint $table) {
            // Change result enum to include proper values
            if (Schema::hasColumn('fingerprint_verifications', 'result')) {
                $table->dropColumn('result');
            }
            $table->enum('status', ['matched', 'rejected', 'failed', 'pending'])->default('pending')->after('match_score');
            
            // Add notes column if missing
            if (!Schema::hasColumn('fingerprint_verifications', 'notes')) {
                $table->text('notes')->nullable()->after('failure_reason');
            }
        });

        // Create biometric_operator_test pivot table if it doesn't exist
        if (!Schema::hasTable('biometric_operator_test')) {
            Schema::create('biometric_operator_test', function (Blueprint $table) {
                $table->id();
                $table->foreignId('biometric_operator_id')->constrained()->onDelete('cascade');
                $table->foreignId('test_id')->constrained()->onDelete('cascade');
                $table->timestamps();
                
                $table->unique(['biometric_operator_id', 'test_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop pivot table
        Schema::dropIfExists('biometric_operator_test');
        
        // Revert FingerprintVerification changes
        Schema::table('fingerprint_verifications', function (Blueprint $table) {
            if (Schema::hasColumn('fingerprint_verifications', 'notes')) {
                $table->dropColumn('notes');
            }
            if (Schema::hasColumn('fingerprint_verifications', 'status')) {
                $table->dropColumn('status');
            }
            $table->enum('result', ['matched', 'rejected', 'failed'])->default('failed');
        });
        
        // Revert Students table changes
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'college_id')) {
                $table->dropForeign(['college_id']);
                $table->dropColumn('college_id');
            }
        });
        
        // Revert BiometricOperator changes
        Schema::table('biometric_operators', function (Blueprint $table) {
            if (Schema::hasColumn('biometric_operators', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('biometric_operators', 'assigned_college_id')) {
                $table->dropForeign(['assigned_college_id']);
                $table->dropColumn('assigned_college_id');
            }
            
            // Add back JSON columns
            $table->text('assigned_colleges')->nullable();
            $table->text('assigned_tests')->nullable();
        });
    }
};