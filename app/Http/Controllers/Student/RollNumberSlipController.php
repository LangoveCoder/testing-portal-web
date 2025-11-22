<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RollNumberSlipController extends Controller
{
    /**
     * Download roll number slip PDF
     */
    public function download(Request $request)
    {
        $request->validate([
            'cnic' => 'required|digits:13',
            'registration_id' => 'required|string',
        ]);

        // Find student
        $student = Student::with(['test.college', 'testDistrict'])
            ->where('cnic', $request->cnic)
            ->where('registration_id', $request->registration_id)
            ->first();

        if (!$student) {
            return back()->with('error', 'No record found with the provided details.');
        }

        // Check if roll number has been generated
        if (!$student->roll_number) {
            return back()->with('error', 'Roll number has not been generated yet for this student.');
        }

        // Generate PDF
        $pdf = PDF::loadView('pdfs.roll-number-slip', compact('student'))
            ->setPaper('a4', 'portrait');

        // Download with filename
        $filename = 'Roll_Slip_' . $student->roll_number . '_' . $student->name . '.pdf';
        
        return $pdf->download($filename);
    }
}