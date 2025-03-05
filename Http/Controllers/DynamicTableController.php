<?php

namespace Modules\MiniReportB1\Http\Controllers;

use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DynamicTableController extends Controller
{
    /**
     * Display the dynamic table with real data.
     *
     * @return View
     */
    public function index(): View
    {
        try {
            // Debug: Check the raw SQL query
            $sql = Transaction::with(['contact', 'businessLocation', 'purchaseLines', 'payments'])
                ->where('type', 'purchase')
                ->toSql();
            
    
            // Retrieve real data from the database
            $purchases = Transaction::with(['contact', 'businessLocation', 'purchaseLines', 'payments'])
                ->where('type', 'purchase')
                ->get();
    
            // Debug: Inspect the retrieved data
            Log::debug('Retrieved Data: ' . json_encode($purchases));
    
            // Map the data
            $purchases = $purchases->map(function ($purchase) {
                // Debug: Inspect each purchase
                Log::debug('Processing Purchase: ' . json_encode($purchase));
    
                // Calculate quantity remaining
                $quantity_remaining = $purchase->purchaseLines->sum(function ($line) {
                    return $line->quantity - $line->quantity_sold;
                });
    
                // Calculate amount paid
                $amount_paid = $purchase->payments->sum('amount'); // If using payments table
                // OR
                // $amount_paid = $purchase->amount_paid; // If using direct column
    
                // Calculate payment due
                $payment_due = $purchase->final_total - $amount_paid;
    
                return [
                    'date' => $purchase->transaction_date,
                    'reference_no' => $purchase->ref_no,
                    'location' => $purchase->businessLocation?->name, // Handle null
                    'supplier' => $purchase->contact?->name, // Handle null
                    'status' => $purchase->status,
                    'quantity_remaining' => $quantity_remaining,
                    'delivery_status' => $purchase->delivery_status,
                    'added_by' => $purchase->created_by,
                    'purchase_status' => $purchase->purchase_status,
                    'payment_status' => $purchase->payment_status,
                    'grand_total' => (float) $purchase->final_total, // Ensure numeric
                    'amount_paid' => (float) $amount_paid, // Ensure numeric
                    'payment_due' => (float) $payment_due, // Ensure numeric
                ];
            });
        
    
            // Debug: Inspect the mapped data
            Log::debug('Mapped Data: ' . json_encode($purchases));
    
            // Prepare rawData and formatted_data
            $rawData = [
                'categories' => [
                    'purchases' => [
                        'transactions' => [
                            'date',
                            'reference_no',
                            'location',
                            'supplier',
                            'status',
                            'quantity_remaining',
                            'delivery_status',
                            'added_by',
                            'purchase_status',
                            'payment_status',
                            'grand_total',
                            'payment_due',
                            'amount_paid',
                        ],
                    ],
                ],
                'dataMapping' => [
                    'date' => $purchases->pluck('date')->toArray(),
                    'reference_no' => $purchases->pluck('reference_no')->toArray(),
                    'location' => $purchases->pluck('location')->toArray(),
                    'supplier' => $purchases->pluck('supplier')->toArray(),
                    'status' => $purchases->pluck('status')->toArray(),
                    'quantity_remaining' => $purchases->pluck('quantity_remaining')->toArray(),
                    'delivery_status' => $purchases->pluck('delivery_status')->toArray(),
                    'added_by' => $purchases->pluck('added_by')->toArray(),
                    'purchase_status' => $purchases->pluck('purchase_status')->toArray(),
                    'payment_status' => $purchases->pluck('payment_status')->toArray(),
                    'grand_total' => $purchases->pluck('grand_total')->toArray(),
                    'payment_due' => $purchases->pluck('payment_due')->toArray(),
                    'amount_paid' => $purchases->pluck('amount_paid')->toArray(),
                ],
            ];
    
            $formatted_data = $purchases->toArray();
    
            // Define folders (static data for testing)
            $folders = [
                (object) ['id' => 1, 'folder_name' => 'Folder 1', 'type' => 'report_section'],
                (object) ['id' => 2, 'folder_name' => 'Folder 2', 'type' => 'other_type'],
            ];
    
            $folders = collect($folders);
    
            // Pass data to the view
            return view('minireportb1::MiniReportB1.create', compact(
                'rawData',
                'formatted_data',
                'folders'
            ));
        } catch (\Exception $e) {
            Log::error('Error in DynamicTableController@index: ' . $e->getMessage());
            Log::error('Stack Trace: ' . $e->getTraceAsString());
            abort(500, 'Internal Server Error: ' . $e->getMessage());
        }
    }

}
