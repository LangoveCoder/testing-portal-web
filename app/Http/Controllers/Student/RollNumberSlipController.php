<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\TestVenue;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RollNumberSlipController extends Controller
{
    public function download(Request $request)
    {
        $request->validate([
            'cnic' => 'required|digits:13',
        ]);

        $student = Student::with(['test.college', 'testDistrict'])
            ->where('cnic', $request->cnic)
            ->first();

        if (!$student) {
            return back()->with('error', 'No record found with the provided CNIC.');
        }

        if (!$student->roll_number) {
            return back()->with('error', 'Roll number has not been generated yet for this student.');
        }

        $venue = TestVenue::where('test_id', $student->test_id)
            ->where('test_district_id', $student->test_district_id)
            ->first();

        // Generate QR code using simple PHP QR code generation
        $qrCodeBase64 = null;
        if ($venue && $venue->google_maps_link) {
            try {
                // Use Google Charts API as fallback for QR generation
                $qrUrl = 'https://chart.googleapis.com/chart?cht=qr&chs=100x100&chl=' . urlencode($venue->google_maps_link);
                $qrImage = @file_get_contents($qrUrl);
                
                if ($qrImage) {
                    $qrCodeBase64 = base64_encode($qrImage);
                }
            } catch (\Exception $e) {
                \Log::error('QR Code generation failed: ' . $e->getMessage());
                $qrCodeBase64 = null;
            }
        }

        $pdf = PDF::loadView('pdfs.roll-number-slip', compact('student', 'venue', 'qrCodeBase64'))
            ->setPaper('a4', 'portrait');

        $filename = 'Roll_Slip_' . $student->roll_number . '_' . $student->name . '.pdf';
        
        return $pdf->download($filename);
    }
}