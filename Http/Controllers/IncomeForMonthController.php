<?php

namespace Modules\MiniReportB1\Http\Controllers;


use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Accounting\Utils\AccountingUtil;
use PDF;
use App\Business;
use Spatie\LaravelIgnition\Support\Composer\FakeComposer;
use Faker\Factory as Faker;


class IncomeForMonthController extends Controller
{
    // /**
    //  * All Utils instance.
    //  */
    // protected $transactionUtil;

    // protected $productUtil;

    // protected $moduleUtil;
    // protected $accountingUtil;

    // protected $businessUtil;

    // /**
    //  * Create a new controller instance.
    //  *
    //  * @return void
    //  */
    // public function __construct(
    //     TransactionUtil $transactionUtil,
    //     ProductUtil $productUtil,
    //     ModuleUtil $moduleUtil,
    //     BusinessUtil $businessUtil,
    //     AccountingUtil $accountingUtil,
    // ) {
    //     $this->transactionUtil = $transactionUtil;
    //     $this->productUtil = $productUtil;
    //     $this->moduleUtil = $moduleUtil;
    //     $this->businessUtil = $businessUtil;
    //     $this->accountingUtil = $accountingUtil;
    // }



    public function getProductSellReport(Request $request)
    {
        if (!auth()->user()->can('Product_Sell_Report')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $business = Business::findOrFail($business_id);

        // Build base query
        $query = DB::table('transactions as t')
            ->join('contacts as c', 't.contact_id', '=', 'c.id')
            ->join('transaction_sell_lines as tsl', 't.id', '=', 'tsl.transaction_id')
            ->join('products as p', 'tsl.product_id', '=', 'p.id')
            ->leftJoin('categories as cat', 'p.category_id', '=', 'cat.id')
            ->leftJoin('exchangerateb1_main as er', function ($join) use ($business_id) {
                $join->on(DB::raw('DATE(t.transaction_date)'), '=', DB::raw('DATE(er.Date_1)'))
                    ->where('er.business_id', '=', $business_id);
            })
            ->where('t.business_id', $business_id)
            ->where('t.type', 'sell')
            ->where('t.status', 'final');

        // Handle show_all flag
        if (!$request->has('show_all')) {
            if ($request->has('today')) {
                // Show only today's transactions
                $query->whereDate('t.transaction_date', today());
            } else if (!$request->has('year') && !$request->has('month_only')) {
                // If neither show_all nor any other filter is present, default to current month
                $query->whereYear('t.transaction_date', now()->year)
                    ->whereMonth('t.transaction_date', now()->month);
            } else {
                // Apply year filter if present
                if ($request->filled('year')) {
                    $query->whereYear('t.transaction_date', $request->year);
                }

                // Apply month filter if present
                if ($request->filled('month_only')) {
                    $query->whereMonth('t.transaction_date', $request->month_only);
                }
            }
        }

        // Select required fields
        $query->select(
            'c.name as customer',
            'c.supplier_business_name',
            'c.contact_id',
            't.id as transaction_id',
            't.invoice_no',
            't.transaction_date',
            't.tax_amount as item_tax',
            't.final_total as subtotal',
            DB::raw('t.final_total - t.tax_amount as unit_price'),
            DB::raw('COALESCE(er.KHR_2, 0) as exchange_rate_khr'),
            'er.Date_1 as exchange_rate_date',
            DB::raw('GROUP_CONCAT(DISTINCT cat.name SEPARATOR " & ") as category_name'),
            DB::raw('CASE 
                WHEN er.KHR_2 IS NOT NULL AND er.KHR_2 != 1.000 
                THEN t.final_total * er.KHR_2 
                ELSE NULL 
            END as khr_amount')
        )
            ->groupBy(
                't.id',
                't.invoice_no',
                't.transaction_date',
                'c.name',
                'c.supplier_business_name',
                'c.contact_id',
                't.tax_amount',
                't.final_total',
                'er.KHR_2',
                'er.Date_1'
            )
            ->orderBy('t.transaction_date', 'desc');

        // Get available years
        $available_years = DB::table('transactions')
            ->where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->selectRaw('DISTINCT YEAR(transaction_date) as year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // Ensure current year is included
        $current_year = now()->year;
        if (!in_array($current_year, $available_years)) {
            array_unshift($available_years, $current_year);
        }

        // Get raw data
        $raw_data = $query->get();

        $total_cross_sale = 0;
        $total_net_price = 0;
        $total_khr = 0;

        // Format data
        $formatted_data = $raw_data->map(function ($row) use (&$total_cross_sale, &$total_khr, &$total_net_price) {
            // Use category_name instead of product_names
            $description = $row->category_name ?? 'Uncategorized';

            $customer_name = !empty($row->supplier_business_name) ? 
                $row->supplier_business_name . ' - ' . $row->customer :
                $row->customer;

                $subtotal = is_numeric($row->subtotal) ? (float)$row->subtotal : 0;
                $khr_amount = is_numeric($row->khr_amount) ? (float)$row->khr_amount : 0;
                $unit_price = is_numeric($row->unit_price) ? (float)$row->unit_price : 0; 

                $total_cross_sale += $subtotal;
                $total_khr += $khr_amount;
                $total_net_price += $unit_price;

            return [
                'date' => $row->transaction_date ? \Carbon\Carbon::parse($row->transaction_date)->format('d/m/Y') : 'N/A',
                'voucher' => $row->transaction_id,
                'unit_price' => number_format((float)$row->unit_price, 2, '.', ''),
                'item_tax' => number_format(abs((float)$row->item_tax), 2, '.', ''),
                'subtotal' => number_format((float)$row->subtotal, 2, '.', ''),
                'customer' => $customer_name,
                'invoice_no' => $row->invoice_no ?? 'N/A',
                'description' => $description,
                'exchange_rate_khr' => number_format((float)$row->exchange_rate_khr, 0, '.', ''),
                'exchange_rate_date' => $row->exchange_rate_date ?
                    \Carbon\Carbon::parse($row->exchange_rate_date)->format('m/d/Y') :
                    'N/A',
                'khr_amount' => $row->khr_amount ? number_format((float)$row->khr_amount, 0, '.', ',') : 'N/A'
            ];
        });

        // dd($total_cross_sale, $total_khr);
        return view('minireportb1::MiniReportB1.card-menu.products.income_for_month', compact(
            'formatted_data',
            'available_years',
            'business',
            'total_cross_sale',
            'total_khr',
            'total_net_price'
        ));
    }

    public function viewFileWithFakeData()
    {
        try {
            // Initialize Faker
            $faker = Faker::create();

            // Generate fake data for the table
            $formatted_data = [];
            for ($i = 0; $i < 10; $i++) { // Generate 10 rows of fake data
                $formatted_data[] = [
                    'date' => $faker->date('Y-m-d'), // Random date
                    'voucher' => $faker->randomNumber(5), // Random voucher number
                    'unit_price' => $faker->randomFloat(2, 10, 1000), // Random unit price
                    'item_tax' => $faker->randomFloat(2, 1, 100), // Random tax
                    'subtotal' => $faker->randomFloat(2, 100, 5000), // Random subtotal
                    'customer' => $faker->name, // Random customer name
                    'invoice_no' => $faker->randomNumber(6), // Random invoice number
                    'description' => $faker->sentence, // Random description
                    'exchange_rate_khr' => $faker->randomFloat(2, 4000, 4100), // Random exchange rate
                ];
            }

            // Return the view with fake data
            return view('minireportb1::MiniReportB1.card-menu.products.exspend_list', [
                'formatted_data' => $formatted_data,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error generating fake data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

           
        }
    }
}
