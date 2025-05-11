<?php

namespace Modules\MiniReportB1\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Storage;
use App\Business;
use Illuminate\Support\Facades\Auth;

class GovTaxPdfController extends Controller
{
    /**
     * Display the PDF form for filling
     */
    public function showPdfForm()
    {
        $business = Business::find(Auth::user()->business_id);
        
        return view('minireportb1::MiniReportB1.gov_tax.p101_tax_form', [
            'business' => $business
        ]);
    }
    
    /**
     * Fill PDF form with data from request
     */
    public function fillPdfForm(Request $request)
    {
        // Validate the request
        $request->validate([
            'company_name' => 'required|string|max:255',
            'date' => 'required|date',
            'tin' => 'nullable|string|max:50',
            'month' => 'nullable|string|max:50',
            'year' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            // Add other validation rules as needed
        ]);
        
        try {
            // Get the source PDF (your fillable form)
            $sourcePdf = public_path('modules/minireportb1/pdf/1.pdf');
            
            // Create an instance of FPDI
            $pdf = new Fpdi();
            
            // Add a page from the source PDF
            $pageCount = $pdf->setSourceFile($sourcePdf);
            
            // Process all pages from the source PDF
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                // Import a page
                $templateId = $pdf->importPage($pageNo);
                // Add the imported page
                $pdf->AddPage();
                $pdf->useTemplate($templateId);
                
                // Set font for form filling
                $pdf->SetFont('Helvetica', '', 10);
                $pdf->SetTextColor(0, 0, 0);
                
                // Extract and format date components
                $date = new \DateTime($request->input('date'));
                $day = $date->format('d');
                $month = $request->input('month') ?: $date->format('m');
                $year = $request->input('year') ?: $date->format('Y');
                
                // Map form field positions to coordinates based on the exact PDF form layout
                $fields = [
                    // Form header fields
                    'month' => ['x' => 626, 'y' => 138], // Month field in header
                    'year' => ['x' => 780, 'y' => 138],  // Year field in header
                    'form_number' => ['x' => 688, 'y' => 195], // Form number
                    
                    // Date fields (three separate boxes for day, month, year)
                    'day' => ['x' => 563, 'y' => 242],   // Day box
                    'month_day' => ['x' => 650, 'y' => 242], // Month box
                    'year_day' => ['x' => 764, 'y' => 242],  // Year box
                    
                    // Company information
                    'company_name' => ['x' => 568, 'y' => 390], // Company name field
                    'tin' => ['x' => 568, 'y' => 435],  // Tax ID Number
                    'address' => ['x' => 568, 'y' => 465], // Address field
                    
                    // Tax branch field
                    'tax_branch' => ['x' => 460, 'y' => 340], // Tax branch field
                ];
                
                // Fill the form with data
                foreach ($fields as $field => $position) {
                    $value = '';
                    
                    // Set the value based on field name
                    if ($field === 'day') {
                        $value = $day;
                    } elseif ($field === 'month_day') {
                        $value = $month;
                    } elseif ($field === 'year_day') {
                        $value = $year;
                    } elseif ($request->has($field)) {
                        $value = $request->input($field);
                    }
                    
                    if (!empty($value)) {
                        $pdf->SetXY($position['x'], $position['y']);
                        $pdf->Write(0, $value);
                    }
                }
                
                // Add checkbox for department if checked in the form
                if ($request->has('is_large_taxpayer') && $request->input('is_large_taxpayer')) {
                    $pdf->SetXY(124, 285);
                    $pdf->Write(0, 'X');
                }
                
                // If tax branch is selected
                if ($request->has('is_tax_branch') && $request->input('is_tax_branch')) {
                    $pdf->SetXY(124, 340);
                    $pdf->Write(0, 'X');
                }
            }
            
            // Generate a unique filename
            $filename = 'filled_form_' . time() . '.pdf';
            $storagePath = 'pdf/filled/' . $filename;
            
            // Save the filled PDF to storage
            Storage::disk('public')->put($storagePath, $pdf->Output('S'));
            
            // Return the filled PDF file for download
            return response()->download(
                storage_path('app/public/' . $storagePath),
                $filename,
                ['Content-Type' => 'application/pdf']
            );
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error filling PDF form: ' . $e->getMessage());
        }
    }
    
    /**
     * Pre-fill PDF form with current date for preview
     * This method allows direct viewing of the form with date already filled
     */
    public function previewPdfForm()
    {
        try {
            // Check if FPDI class exists
            if (class_exists('setasign\Fpdi\Fpdi')) {
                // Get the source PDF
                $sourcePdf = public_path('modules/minireportb1/pdf/1.pdf');
                
                // Create an instance of FPDI
                $pdf = new Fpdi();
                
                // Add a page from the source PDF
                $pageCount = $pdf->setSourceFile($sourcePdf);
                
                // Import first page and add it
                $templateId = $pdf->importPage(1);
                $pdf->AddPage();
                $pdf->useTemplate($templateId);
                
                // Set font for form filling
                $pdf->SetFont('Helvetica', '', 10);
                $pdf->SetTextColor(0, 0, 0);
                
                // Get current date
                $currentDate = new \DateTime();
                $day = $currentDate->format('d');
                $month = $currentDate->format('m');
                $year = $currentDate->format('Y');
                
                // Fill date fields with current date
                $pdf->SetXY(563, 242); // Day position
                $pdf->Write(0, $day);
                
                $pdf->SetXY(650, 242); // Month position
                $pdf->Write(0, $month);
                
                $pdf->SetXY(764, 242); // Year position
                $pdf->Write(0, $year);
                
                // Fill month/year in header
                $pdf->SetXY(626, 138); // Month in header
                $pdf->Write(0, $month);
                
                $pdf->SetXY(780, 138); // Year in header
                $pdf->Write(0, $year);
                
                // Generate temp file for viewing
                $filename = 'prefilled_form_' . time() . '.pdf';
                $storagePath = 'pdf/temp/' . $filename;
                
                // Save the filled PDF to storage
                Storage::disk('public')->put($storagePath, $pdf->Output('S'));
                
                // Stream the PDF for viewing
                return response()->file(
                    storage_path('app/public/' . $storagePath),
                    ['Content-Type' => 'application/pdf']
                );
            } else {
                // Fallback to JavaScript approach if FPDI is not available
                return view('minireportb1::MiniReportB1.gov_tax.p101_date_prefill');
            }
            
        } catch (\Exception $e) {
        
            
            // Fallback to JavaScript approach
            return view('minireportb1::MiniReportB1.gov_tax.p101_date_prefill');
        }
    }
    
    /**
     * Handle the submission of a filled PDF
     */
    public function submitFilledPdf(Request $request)
    {
        $request->validate([
            'filled_pdf' => 'required|file|mimes:pdf|max:10240'
        ]);
        
        try {
            // Store the uploaded PDF
            $path = $request->file('filled_pdf')->store('pdf/uploaded', 'public');
            
            // Save record in database if needed
            // ...
            
            return back()->with('success', 'PDF form submitted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error uploading PDF: ' . $e->getMessage());
        }
    }
} 