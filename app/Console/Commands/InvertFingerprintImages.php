<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use Illuminate\Support\Facades\Storage;

class InvertFingerprintImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fingerprints:invert {--dry-run : Run without actually inverting images}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Invert all existing fingerprint images (black to white)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No images will be modified');
            $this->newLine();
        }

        // Get all students with fingerprint images
        $students = Student::whereNotNull('fingerprint_image')
            ->whereNotNull('fingerprint_template')
            ->get();

        if ($students->count() === 0) {
            $this->warn('⚠ No fingerprint images found in database.');
            return Command::SUCCESS;
        }

        $this->info("📊 Found {$students->count()} fingerprint images to process");
        $this->newLine();

        $successCount = 0;
        $errorCount = 0;
        $skippedCount = 0;

        $progressBar = $this->output->createProgressBar($students->count());
        $progressBar->start();

        foreach ($students as $student) {
            $progressBar->advance();

            $imagePath = $student->fingerprint_image;

            // Check if file exists
            if (!Storage::disk('public')->exists($imagePath)) {
                $errorCount++;
                continue;
            }

            if ($dryRun) {
                $successCount++;
                continue;
            }

            try {
                // Get the full path
                $fullPath = storage_path('app/public/' . $imagePath);

                // Try to load as PNG first, then BMP
                $image = @imagecreatefrompng($fullPath);
                
                if ($image === false) {
                    // Try BMP format (SecuGen scanners often output BMP)
                    $image = @imagecreatefrombmp($fullPath);
                }
                
                if ($image === false) {
                    // Try as generic image
                    $image = @imagecreatefromstring(file_get_contents($fullPath));
                }

                if ($image === false) {
                    $this->newLine();
                    $this->error("✗ Failed to load image for Roll #{$student->roll_number}");
                    $errorCount++;
                    continue;
                }

                // Apply same processing as controller: invert + brightness + contrast
                imagefilter($image, IMG_FILTER_NEGATE);           // Invert colors
                imagefilter($image, IMG_FILTER_BRIGHTNESS, 80);   // Increase brightness
                imagefilter($image, IMG_FILTER_CONTRAST, -30);    // Increase contrast

                // Save as PNG
                imagepng($image, $fullPath);
                imagedestroy($image);

                $successCount++;

            } catch (\Exception $e) {
                $this->newLine();
                $this->error("✗ Error processing Roll #{$student->roll_number}: " . $e->getMessage());
                $errorCount++;
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->info('═══════════════════════════════════════════════');
        $this->info('📋 INVERSION SUMMARY');
        $this->info('═══════════════════════════════════════════════');
        
        if ($dryRun) {
            $this->line("🔍 Dry Run: {$successCount} images would be inverted");
        } else {
            $this->line("✓ Successfully inverted: {$successCount}");
            $this->line("⊘ Already inverted (skipped): {$skippedCount}");
            $this->line("✗ Errors: {$errorCount}");
        }
        
        $this->info('═══════════════════════════════════════════════');
        $this->newLine();

        if (!$dryRun && $successCount > 0) {
            $this->info('✓ All fingerprint images have been inverted!');
            $this->info('  Fingerprints will now show as white on black background.');
        }

        return Command::SUCCESS;
    }
}