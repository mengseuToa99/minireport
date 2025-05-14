<?php

namespace Modules\MiniReportB1\Http\Controllers\StandardReport;


use App\Brands;
use App\Business;
use App\Product;
use App\Variation;

use App\Transaction;
use App\BusinessLocation;
use App\Category;
use App\Contact;
use App\CustomerGroup;
use App\Discount;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\VariationLocationDetails;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\PurchaseLine;
use App\SellingPriceGroup;
use App\StockAdjustmentLine;
use App\TransactionSellLine;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\MiniReportB1\Http\Services\DateFilterService;
use App\Http\Controllers\ProductController;
use App\Unit;
use Faker\Factory as Faker;
use App\Utils\TransactionUtil;
use App\Charts\CommonChart;
use App\TransactionSellLinesPurchaseLines;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProductReportController extends Controller
{


    protected $transactionUtil;
    protected $productUtil;

    protected $moduleUtil;

    protected $productByGroupPrice;
    protected $commonUtil;
    protected $dateFilterService;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ProductUtil $productUtil, ModuleUtil $moduleUtil, ProductController $productByGroupPrice, Util $commonUtil, DateFilterService $dateFilterService, TransactionUtil $transactionUtil)
    {
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->productByGroupPrice = $productByGroupPrice;
        $this->commonUtil = $commonUtil;
        $this->dateFilterService = $dateFilterService;
        $this->transactionUtil = $transactionUtil;
    }

    public function getProfitProduce(Request $request)
    {
        if (!auth()->user()->can('product.view')) {
            abort(403, 'Unauthorized action.');
        }
        
        $business_id = request()->session()->get('user.business_id');
        
        // Get filters if provided
        $type_filter = $request->input('type_filter');
        $category_filter = $request->input('category_filter');
        $price_group_filter = $request->input('price_group_filter');
        
        // Get all selling price groups
        $price_groups = SellingPriceGroup::where('business_id', $business_id)->get();
        
        // Base query for products
        $query = Product::where('products.business_id', $business_id)
            ->with(['category', 'unit'])
            ->select(
                'products.id',
                'products.name',
                'products.type',
                'products.category_id',
                'products.unit_id',
                'products.tax',
                'products.tax_type',
                'products.enable_stock',
                'products.alert_quantity'
            )
            ->orderBy('products.name', 'asc');
        
        // Join with variations
        $query->join('variations', 'variations.product_id', '=', 'products.id')
            ->whereNull('variations.deleted_at')
            ->groupBy('products.id');
        
        // Apply type filter if provided
        if (!empty($type_filter)) {
            $query->where('products.type', $type_filter);
        }
        
        // Apply category filter if provided
        if (!empty($category_filter)) {
            $query->where('products.category_id', $category_filter);
        }
        
        // Get the products with their variations
        $products = $query->get()
            ->map(function ($product) use ($price_group_filter, $price_groups) {
                // Now get variations with their group prices
                $variations = Variation::where('product_id', $product->id)
                    ->whereNull('deleted_at')
                    ->with(['group_prices' => function($q) use ($price_group_filter) {
                        if (!empty($price_group_filter)) {
                            $q->where('price_group_id', $price_group_filter);
                        }
                    }])
                    ->get()
                    ->map(function ($variation) use ($price_group_filter, $price_groups) {
                        $purchase_price = $variation->default_purchase_price;
                        
                        // Default to default sell price if no price group selected
                        $sell_price = $variation->default_sell_price;
                        $group_price_name = 'Default Price';
                        
                        // If price group filter is selected and group prices exist
                        if (!empty($price_group_filter) && $variation->group_prices->isNotEmpty()) {
                            $group_price = $variation->group_prices->first();
                            if ($group_price) {
                                $sell_price = $group_price->price_inc_tax;
                                $selected_group = $price_groups->where('id', $price_group_filter)->first();
                                $group_price_name = $selected_group ? $selected_group->name : 'Group Price';
                            }
                        }
                        
                        $profit = $sell_price - $purchase_price;
                        $profit_percent = ($purchase_price > 0) ? ($profit / $purchase_price) * 100 : 0;
                        
                        return [
                            'variation_name' => $variation->name,
                            'purchase_price' => $purchase_price,
                            'sell_price' => $sell_price,
                            'profit' => $profit,
                            'profit_percent' => $profit_percent,
                            'group_price_name' => $group_price_name
                        ];
                    });
                
                return [
                    'id' => $product->id,
                    'product_name' => $product->name,
                    'type' => $product->type,
                    'category_id' => $product->category_id,
                    'category_name' => $product->category->name ?? 'Uncategorized',
                    'unit_name' => $product->unit->short_name ?? '',
                    'variations' => $variations,
                    'tax_rate' => $product->tax
                ];
            });
        
        // Get categories for filter
        $categories = Category::forDropdown($business_id, 'product');
        
        // Get product types for filter
        $types = [
            'single' => __('lang_v1.single'),
            'variable' => __('lang_v1.variable'),
            'combo' => __('lang_v1.combo')
        ];
        
        if ($request->ajax()) {
            return response()->json([
                'products' => $products,
                'categories' => $categories,
                'types' => $types,
                'price_groups' => $price_groups
            ]);
        }
        
        return view('minireportb1::MiniReportB1.StandardReport.ProductReports.profit_product_report', compact(
            'products',
            'categories',
            'types',
            'price_groups'
        ));
    }

    public function getMonthlyStock(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $location_id = $request->input('location_id', 'all');

        // Get date range from the request
        $date_range = $this->dateFilterService->calculateDateRange($request);
        $start_date = $date_range['start_date'];
        $end_date = $date_range['end_date'];

        // Convert 'all' locations to null
        $location_id = $location_id === 'all' ? null : $location_id;

        $products_by_category = $this->getMonthlyStockReport(
            $business_id,
            $location_id,
            $month,
            $year
        );

        // Get business locations for dropdown
        $business_locations = BusinessLocation::forDropdown($business_id);

        // Get categories for filter modal
        $categories = Category::where('business_id', $business_id)
            ->pluck('name', 'id');

        // Month names for dropdown
        $months = [
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        ];

        return view('minireportb1::MiniReportB1.StandardReport.ProductReports.monthly_stock_product')
            ->with(compact(
                'products_by_category',
                'business_locations',
                'months',
                'categories',
                'month',
                'year',
                'location_id'
            ));
    }

    public function getMonthlyStockReport($business_id, $location_id, $month, $year)
    {
        $startDate = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $previousMonthEnd = $startDate->copy()->subMonth()->endOfMonth();
    
        // Fetch products with variations, selling prices, and units
        $products = Product::where('business_id', $business_id)
            ->with(['variations', 'variations.product_variation', 'category', 'unit'])
            ->get();
    
        $reportData = [];
    
        foreach ($products as $product) {
            $productData = [
                'id' => $product->id,
                'product_name' => $product->name,
                'sku' => $product->sku,
                'category_id' => $product->category->id ?? null,
                'category_name' => $product->category->name ?? __('lang_v1.uncategorized'),
                'unit_name' => $product->unit->short_name ?? 'Pc(s)', // Default to Pieces if no unit
                'selling_price' => 0,
                'opening_stock' => 0,
                'total_purchases' => 0,
                'total_sales' => 0,
                'total_adjustments' => 0,
                'total_transfers_in' => 0,
                'total_transfers_out' => 0,
                'total_production_purchases' => 0,
                'total_production_sales' => 0,
                'final_stock' => 0,
                'product_link' => ''
            ];
    
            if (auth()->user()->can('product.view')) {
                $productData['product_link'] = action([\App\Http\Controllers\ProductController::class, 'productStockHistory'], [$product->id]);
            }
    
            foreach ($product->variations as $variation) {
                $sellingPrice = $variation->sell_price_inc_tax ?? $variation->default_sell_price;
                $productData['selling_price'] = $sellingPrice;
    
                $openingStock = $this->getVariationStockUpToDate(
                    $business_id,
                    $variation->id,
                    $location_id,
                    $previousMonthEnd
                );
    
                $productData['opening_stock'] += $openingStock;
    
                $transactions = $this->getVariationTransactionsInPeriod(
                    $business_id,
                    $variation->id,
                    $location_id,
                    $startDate,
                    $endDate
                );
    
                $productData['total_purchases'] += $transactions['total_purchases'];
                $productData['total_sales'] += $transactions['total_sales'];
                $productData['total_adjustments'] += $transactions['total_adjustments'];
                $productData['total_transfers_in'] += $transactions['total_transfers_in'];
                $productData['total_transfers_out'] += $transactions['total_transfers_out'];
                $productData['total_production_purchases'] += $transactions['total_production_purchases'];
                $productData['total_production_sales'] += $transactions['total_production_sales'];
            }
    
            $productData['final_stock'] = $productData['opening_stock']
                + $productData['total_purchases']
                + $productData['total_transfers_in']
                + $productData['total_production_purchases']
                - $productData['total_sales']
                - $productData['total_adjustments']
                - $productData['total_transfers_out']
                - $productData['total_production_sales'];
    
            $categoryKey = $productData['category_id'] ?? 'uncategorized';
            if (!isset($reportData[$categoryKey])) {
                $reportData[$categoryKey] = [];
            }
            $reportData[$categoryKey][] = $productData;
        }
    
        return $reportData;
    }

    protected function getVariationStockUpToDate($business_id, $variation_id, $location_id, $endDate)
    {
        $query = PurchaseLine::join('transactions as t', 'purchase_lines.transaction_id', '=', 't.id')
            ->where('t.business_id', $business_id)
            ->where('purchase_lines.variation_id', $variation_id)
            ->where('t.transaction_date', '<=', $endDate);

        if ($location_id) {
            $query->where('t.location_id', $location_id);
        }

        $purchaseData = $query->select(
            DB::raw("SUM(IF(t.type='purchase' AND t.status='received', purchase_lines.quantity, 0)) as total_purchase"),
            DB::raw("SUM(IF(t.type IN ('purchase', 'purchase_return'), purchase_lines.quantity_returned, 0)) as total_purchase_return"),
            DB::raw("SUM(IF(t.type='opening_stock', purchase_lines.quantity, 0)) as total_opening_stock"),
            DB::raw("SUM(IF(t.type='purchase_transfer' AND t.status='received', purchase_lines.quantity, 0)) as total_purchase_transfer"),
            DB::raw("SUM(IF(t.type='production_purchase' AND t.status='received', purchase_lines.quantity, 0)) as total_production_purchases") // Add this
        )->first();

        $sellQuery = TransactionSellLine::join('transactions as t', 'transaction_sell_lines.transaction_id', '=', 't.id')
            ->where('t.business_id', $business_id)
            ->where('transaction_sell_lines.variation_id', $variation_id)
            ->where('t.transaction_date', '<=', $endDate)
            ->where('t.status', 'final');

        if ($location_id) {
            $sellQuery->where('t.location_id', $location_id);
        }

        $sellData = $sellQuery->select(
            DB::raw("SUM(IF(t.type='sell', transaction_sell_lines.quantity, 0)) as total_sold"),
            DB::raw("SUM(IF(t.type='sell_transfer', transaction_sell_lines.quantity, 0)) as total_sell_transfer"),
            DB::raw("SUM(IF(t.type='production_sell', transaction_sell_lines.quantity, 0)) as total_production_sales") // Add this
        )->first();

        $adjustmentQuery = StockAdjustmentLine::join('transactions as t', 'stock_adjustment_lines.transaction_id', '=', 't.id')
            ->where('t.business_id', $business_id)
            ->where('stock_adjustment_lines.variation_id', $variation_id)
            ->where('t.transaction_date', '<=', $endDate);

        if ($location_id) {
            $adjustmentQuery->where('t.location_id', $location_id);
        }

        $adjustments = $adjustmentQuery->sum('stock_adjustment_lines.quantity');

        return ($purchaseData->total_opening_stock ?? 0)
            + ($purchaseData->total_purchase ?? 0)
            + ($purchaseData->total_purchase_transfer ?? 0)
            + ($purchaseData->total_production_purchases ?? 0) // Add this line
            - ($purchaseData->total_purchase_return ?? 0)
            - ($sellData->total_sold ?? 0)
            - ($sellData->total_sell_transfer ?? 0)
            - ($sellData->total_production_sales ?? 0) // Add this line
            - $adjustments;
    }


    protected function getVariationTransactionsInPeriod($business_id, $variation_id, $location_id, $startDate, $endDate)
    {
        // Purchases and returns
        $purchaseQuery = PurchaseLine::join('transactions as t', 'purchase_lines.transaction_id', '=', 't.id')
            ->where('t.business_id', $business_id)
            ->where('purchase_lines.variation_id', $variation_id)
            ->whereBetween('t.transaction_date', [$startDate, $endDate]);

        if ($location_id) {
            $purchaseQuery->where('t.location_id', $location_id);
        }

        $purchaseData = $purchaseQuery->select(
            DB::raw("SUM(IF(t.type='purchase' AND t.status='received', purchase_lines.quantity, 0)) as total_purchase"),
            DB::raw("SUM(IF(t.type='purchase_return', purchase_lines.quantity_returned, 0)) as total_purchase_return"),
            DB::raw("SUM(IF(t.type='purchase_transfer' AND t.status='received', purchase_lines.quantity, 0)) as total_purchase_transfer"),
            DB::raw("SUM(IF(t.type='production_purchase' AND t.status='received', purchase_lines.quantity, 0)) as total_production_purchases") // Add this
        )->first();

        // Sales and transfers
        $sellQuery = TransactionSellLine::join('transactions as t', 'transaction_sell_lines.transaction_id', '=', 't.id')
            ->where('t.business_id', $business_id)
            ->where('transaction_sell_lines.variation_id', $variation_id)
            ->whereBetween('t.transaction_date', [$startDate, $endDate])
            ->where('t.status', 'final');

        if ($location_id) {
            $sellQuery->where('t.location_id', $location_id);
        }

        $sellData = $sellQuery->select(
            DB::raw("SUM(IF(t.type='sell', transaction_sell_lines.quantity, 0)) as total_sold"),
            DB::raw("SUM(IF(t.type='sell_transfer', transaction_sell_lines.quantity, 0)) as total_sell_transfer"),
            DB::raw("SUM(IF(t.type='production_sell', transaction_sell_lines.quantity, 0)) as total_production_sales") // Add this
        )->first();

        // Adjustments
        $adjustmentQuery = StockAdjustmentLine::join('transactions as t', 'stock_adjustment_lines.transaction_id', '=', 't.id')
            ->where('t.business_id', $business_id)
            ->where('stock_adjustment_lines.variation_id', $variation_id)
            ->whereBetween('t.transaction_date', [$startDate, $endDate]);

        if ($location_id) {
            $adjustmentQuery->where('t.location_id', $location_id);
        }

        $adjustments = $adjustmentQuery->sum('stock_adjustment_lines.quantity');

        return [
            'total_purchases' => ($purchaseData->total_purchase ?? 0) - ($purchaseData->total_purchase_return ?? 0),
            'total_transfers_in' => $purchaseData->total_purchase_transfer ?? 0,
            'total_sales' => $sellData->total_sold ?? 0,
            'total_transfers_out' => $sellData->total_sell_transfer ?? 0,
            'total_adjustments' => $adjustments,
            'total_production_purchases' => $purchaseData->total_production_purchases ?? 0, // Add this
            'total_production_sales' => $sellData->total_production_sales ?? 0, // Add this
        ];
    }



    public function getProductSalesReport(Request $request)
    {
        if (!auth()->user()->can('Product_Sell_Report')) {
            abort(403, 'Unauthorized action.');
        }
    
        $business_id = $request->session()->get('user.business_id');
        $business = Business::findOrFail($business_id);
    
        // Initialize date filters
        $date_range = $this->dateFilterService->calculateDateRange($request);
        $start_date = $date_range['start_date'];
        $end_date = $date_range['end_date'];
    
        // Get location filter
        $location_id = $request->get('location_id', null);
    
        // Fetch categories for filter dropdown
        $categories = Category::forDropdown($business_id, 'product');
        
        // Fetch locations for filter dropdown
        $locations = BusinessLocation::forDropdown($business_id);
    
        // Build base query - start from transactions to properly filter by location
        $query = DB::table('transactions as t')
            ->join('transaction_sell_lines as tsl', 't.id', '=', 'tsl.transaction_id')
            ->join('products as p', 'tsl.product_id', '=', 'p.id')
            ->leftJoin('categories as c1', 'p.category_id', '=', 'c1.id')
            ->leftJoin('categories as c2', 'p.sub_category_id', '=', 'c2.id')
            ->where('t.business_id', $business_id)
            ->whereIn('t.type', ['sell', 'production_sell', 'production_purchase'])
            ->where('t.status', 'final')
            ->when($start_date && $end_date, function ($q) use ($start_date, $end_date) {
                $q->whereBetween('t.transaction_date', [$start_date, $end_date]);
            })
            ->when($location_id, function ($q) use ($location_id) {
                $q->where('t.location_id', $location_id);
            })
            ->select(
                'p.id as product_id',
                'p.name as product_name',
                'p.sku',
                'p.category_id',
                'p.sub_category_id',
                'c1.name as category_name',
                'c2.name as sub_category_name',
                DB::raw('SUM(tsl.quantity) as total_quantity')
            )
            ->groupBy('p.id', 'p.name', 'p.sku', 'p.category_id', 'p.sub_category_id', 'c1.name', 'c2.name')
            ->orderBy('p.name', 'asc');
    
        $raw_data = $query->get();
    
        $formatted_data = $raw_data->map(function ($row) {
            $description = $row->category_name;
            if ($row->sub_category_name) {
                $description .= ' > ' . $row->sub_category_name;
            }
    
            return [
                'product_id' => $row->product_id,
                'product_name' => $row->product_name,
                'sku' => $row->sku,
                'category_id' => $row->category_id ?? null,
                'sub_category_id' => $row->sub_category_id ?? null,
                'category_name' => $row->category_name,
                'sub_category_name' => $row->sub_category_name,
                'description' => $description,
                'quantity' => $row->total_quantity
            ];
        });
    
        $products_by_category = [];
        foreach ($formatted_data as $product) {
            $category_id = $product['category_id'] ?? 'no_category';
            if (!isset($products_by_category[$category_id])) {
                $products_by_category[$category_id] = [
                    'name' => $product['category_name'] ?? null,
                    'products' => []
                ];
            }
            $products_by_category[$category_id]['products'][] = $product;
        }
    
        if ($request->has('export_excel')) {
            return $this->exportToExcel($formatted_data, $start_date, $end_date, $location_id, $locations);
        }
    
        if ($request->ajax()) {
            return response()->json([
                'data' => $formatted_data,
            ]);
        }
    
        return view('minireportb1::MiniReportB1.StandardReport.ProductReports.monthly_product_sale', compact(
            'formatted_data',
            'business',
            'start_date',
            'end_date',
            'raw_data',
            'categories',
            'products_by_category',
            'locations',
            'location_id'
        ));
    }
    
    private function exportToExcel($data, $start_date, $end_date, $location_id, $locations)
    {
        $location_name = $location_id ? $locations[$location_id] : 'All Locations';
        
        return Excel::download(new class($data, $start_date, $end_date, $location_name) implements FromCollection, WithHeadings, WithTitle {
            private $data;
            private $start_date;
            private $end_date;
            private $location_name;
    
            public function __construct($data, $start_date, $end_date, $location_name)
            {
                $this->data = $data;
                $this->start_date = $start_date;
                $this->end_date = $end_date;
                $this->location_name = $location_name;
            }
    
            public function collection()
            {
                return collect($this->data)->map(function ($item, $index) {
                    return [
                        'No.' => $index + 1,
                        'Product Name' => $item['product_name'],
                        'SKU' => $item['sku'],
                        'Category' => $item['description'],
                        'Quantity Sold' => $item['quantity']
                    ];
                });
            }
    
            public function headings(): array
            {
                return [
                    ['Product Sales Report'],
                    ['Date Range: ' . $this->start_date . ' to ' . $this->end_date],
                    ['Location: ' . $this->location_name],
                    [],
                    ['No.', 'Product Name', 'SKU', 'Category', 'Quantity Sold']
                ];
            }
    
            public function title(): string
            {
                return 'Product Sales Report';
            }
        }, 'product_sales_report_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function productPriceListbyCostGroup()
    {
        // Authorization check
        if (!auth()->user()->can('product.view') && !auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        // Fetch business ID from session
        $business_id = request()->session()->get('user.business_id');

        // Fetch all group prices
        $group_prices = SellingPriceGroup::where('business_id', $business_id)->get();

        // Fetch all categories for the filter dropdown (using forDropdown)
        $categories = Category::forDropdown($business_id, 'product');

        // Fetch products with their group price values for all group prices
        $query = DB::table('products')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->join('units as primary_units', 'products.unit_id', '=', 'primary_units.id')
            ->leftJoin('units as secondary_units', 'products.secondary_unit_id', '=', 'secondary_units.id')
            // Join to find base units (units that this unit is based on)
            ->leftJoin('units as base_units', function ($join) {
                $join->on('secondary_units.base_unit_id', '=', 'base_units.id')
                    ->orOn('primary_units.base_unit_id', '=', 'base_units.id');
            })
            // Join to find conversion units (units that use this unit as their base)
            ->leftJoin('units as conversion_units', function ($join) {
                $join->on('conversion_units.base_unit_id', '=', 'primary_units.id')
                    ->orOn('conversion_units.base_unit_id', '=', 'secondary_units.id');
            })
            ->leftJoin('categories as c1', 'products.category_id', '=', 'c1.id')
            ->leftJoin('categories as c2', 'products.sub_category_id', '=', 'c2.id')
            ->leftJoin('tax_rates', 'products.tax', '=', 'tax_rates.id')
            ->join('variations', 'variations.product_id', '=', 'products.id')
            ->leftJoin('variation_group_prices', function ($join) {
                $join->on('variation_group_prices.variation_id', '=', 'variations.id');
            })
            ->whereNull('variations.deleted_at')
            ->where('products.business_id', $business_id)
            ->where('products.type', '!=', 'modifier')
            ->select(
                'products.id',
                'products.name as product_name',
                'products.type',
                'c1.name as category',
                'c2.name as sub_category',
                'products.category_id',
                'primary_units.id as primary_unit_id',
                'primary_units.actual_name as primary_unit_name',
                'primary_units.short_name as primary_unit_short_name',
                'secondary_units.id as secondary_unit_id',
                'secondary_units.actual_name as secondary_unit_name',
                'secondary_units.short_name as secondary_unit_short_name',
                'secondary_units.base_unit_multiplier',
                'secondary_units.base_unit_id',
                'base_units.actual_name as base_unit_name',
                'base_units.short_name as base_unit_short_name',
                // Conversion unit info
                'conversion_units.id as conversion_unit_id',
                'conversion_units.actual_name as conversion_unit_name',
                'conversion_units.short_name as conversion_unit_short_name',
                'conversion_units.base_unit_multiplier as conversion_multiplier',
                'brands.name as brand',
                'tax_rates.name as tax',
                'products.sku',
                'products.image',
                'products.enable_stock',
                'products.is_inactive',
                'products.not_for_selling',
                DB::raw('MAX(variations.dpp_inc_tax) as max_purchase_price'),
                DB::raw('MIN(variations.dpp_inc_tax) as min_purchase_price'),
                'variation_group_prices.price_group_id',
                'variation_group_prices.price_inc_tax as group_price_value'
            )
            ->groupBy(
                'products.id',
                'products.name',
                'products.type',
                'c1.name',
                'c2.name',
                'products.category_id',
                'primary_units.id',
                'primary_units.actual_name',
                'primary_units.short_name',
                'secondary_units.id',
                'secondary_units.actual_name',
                'secondary_units.short_name',
                'secondary_units.base_unit_multiplier',
                'secondary_units.base_unit_id',
                'base_units.actual_name',
                'base_units.short_name',
                'conversion_units.id',
                'conversion_units.actual_name',
                'conversion_units.short_name',
                'conversion_units.base_unit_multiplier',
                'brands.name',
                'tax_rates.name',
                'products.sku',
                'products.image',
                'products.enable_stock',
                'products.is_inactive',
                'products.not_for_selling',
                'variation_group_prices.price_group_id',
                'variation_group_prices.price_inc_tax'
            )
            ->get();

        // Group products by their group prices
        $grouped_products = [];
        foreach ($query as $product) {
            // Determine the display unit and multiplier
            $display_unit = $product->secondary_unit_short_name ?? $product->primary_unit_short_name;
            $base_multiplier = $product->base_unit_multiplier ?? 1;

            // Check for conversion relationship (like unit 65 -> unit 63)
            $unit_conversion_info = '';
            if ($product->conversion_unit_id && $product->conversion_multiplier) {
                $formatted_multiplier = number_format($product->conversion_multiplier, 0, '.', '');
                $unit_conversion_info = $product->conversion_unit_short_name . ' = ' .
                    $formatted_multiplier . ' ' .
                    ($product->primary_unit_short_name ?? $product->primary_unit_name);
            }

            if (!isset($grouped_products[$product->id])) {
                $grouped_products[$product->id] = [
                    'id' => $product->id,
                    'product_name' => $product->product_name,
                    'category_name' => $product->category,
                    'sub_category_name' => $product->sub_category,
                    'category_id' => $product->category_id ?? null,
                    'sku' => $product->sku,
                    'unit_id' => $product->secondary_unit_id ?? $product->primary_unit_id,
                    'unit_actual_name' => $display_unit,
                    'unit_base_multiplier' => $base_multiplier,
                    'unit_conversion_info' => $unit_conversion_info, // This will show "កេស = 155 sabuដុំ"
                    'brand' => $product->brand,
                    'tax' => $product->tax,
                    'image' => $product->image,
                    'enable_stock' => $product->enable_stock,
                    'is_inactive' => $product->is_inactive,
                    'not_for_selling' => $product->not_for_selling,
                    'min_purchase_price' => $product->min_purchase_price ?? 0,
                    'max_purchase_price' => $product->max_purchase_price ?? 0,
                    'group_prices' => []
                ];
            }

            // Add group prices for the product
            if ($product->price_group_id) {
                $grouped_products[$product->id]['group_prices'][$product->price_group_id] = $product->group_price_value;
            }
        }

        // Organize products by category and product name
        $products_by_category = [];
        foreach ($grouped_products as $product) {
            $category_id = $product['category_id'];
            $product_name = $product['product_name'];

            if (!isset($products_by_category[$category_id])) {
                $products_by_category[$category_id] = [];
            }

            // Add product to its category
            $products_by_category[$category_id][] = $product;
        }

        // Pass data to the view
        return view(
            'minireportb1::MiniReportB1.StandardReport.ProductReports.productPriceListbyCostGroup',
            compact('group_prices', 'categories', 'grouped_products', 'products_by_category')
        );
    }

    public function productPriceListByBatchCostGroup()
    {
        // Authorization check
        if (!auth()->user()->can('product.view') && !auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        // Fetch business ID from session
        $business_id = request()->session()->get('user.business_id');

        // Fetch all group prices
        $group_prices = SellingPriceGroup::where('business_id', $business_id)->get();

        // Fetch all categories for the filter dropdown (using forDropdown)
        $categories = Category::forDropdown($business_id, 'product');

        // Fetch products with their group price values for all group prices
        $query = DB::table('products')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->join('units as primary_units', 'products.unit_id', '=', 'primary_units.id')
            ->leftJoin('units as secondary_units', 'products.secondary_unit_id', '=', 'secondary_units.id')
            // Join to find base units (units that this unit is based on)
            ->leftJoin('units as base_units', function ($join) {
                $join->on('secondary_units.base_unit_id', '=', 'base_units.id')
                    ->orOn('primary_units.base_unit_id', '=', 'base_units.id');
            })
            // Join to find conversion units (units that use this unit as their base)
            ->leftJoin('units as conversion_units', function ($join) {
                $join->on('conversion_units.base_unit_id', '=', 'primary_units.id')
                    ->orOn('conversion_units.base_unit_id', '=', 'secondary_units.id');
            })
            ->leftJoin('categories as c1', 'products.category_id', '=', 'c1.id')
            ->leftJoin('categories as c2', 'products.sub_category_id', '=', 'c2.id')
            ->leftJoin('tax_rates', 'products.tax', '=', 'tax_rates.id')
            ->join('variations', 'variations.product_id', '=', 'products.id')
            ->Join('variation_group_prices', function ($join) {
                $join->on('variation_group_prices.variation_id', '=', 'variations.id');
            })
            ->whereNull('variations.deleted_at')
            ->where('products.business_id', $business_id)
            ->where('products.type', '!=', 'modifier')
            // Exclude products with primary unit named "ពិដុង" or "កាន"
            ->where(function ($query) {
                $query->where('primary_units.actual_name', '!=', 'ពិដុង')
                    ->where('primary_units.actual_name', '!=', 'កាន');
            })
            // Exclude products with secondary unit named "ពិដុង" or "កាន"
            ->where(function ($query) {
                $query->whereNull('secondary_units.actual_name')
                    ->orWhere(function ($q) {
                        $q->where('secondary_units.actual_name', '!=', 'ពិដុង')
                            ->where('secondary_units.actual_name', '!=', 'កាន');
                    });
            })
            ->select(
                'products.id',
                'products.name as product_name',
                'products.type',
                'c1.name as category',
                'c2.name as sub_category',
                'products.category_id',
                'primary_units.id as primary_unit_id',
                'primary_units.actual_name as primary_unit_name',
                'primary_units.short_name as primary_unit_short_name',
                'secondary_units.id as secondary_unit_id',
                'secondary_units.actual_name as secondary_unit_name',
                'secondary_units.short_name as secondary_unit_short_name',
                'secondary_units.base_unit_multiplier',
                'secondary_units.base_unit_id',
                'base_units.actual_name as base_unit_name',
                'base_units.short_name as base_unit_short_name',
                // Conversion unit info
                'conversion_units.id as conversion_unit_id',
                'conversion_units.actual_name as conversion_unit_name',
                'conversion_units.short_name as conversion_unit_short_name',
                'conversion_units.base_unit_multiplier as conversion_multiplier',
                'brands.name as brand',
                'tax_rates.name as tax',
                'products.sku',
                'products.image',
                'products.enable_stock',
                'products.is_inactive',
                'products.not_for_selling',
                DB::raw('MAX(variations.dpp_inc_tax) as max_purchase_price'),
                DB::raw('MIN(variations.dpp_inc_tax) as min_purchase_price'),
                'variation_group_prices.price_group_id',
                'variation_group_prices.price_inc_tax as group_price_value'
            )
            ->groupBy(
                'products.id',
                'products.name',
                'products.type',
                'c1.name',
                'c2.name',
                'products.category_id',
                'primary_units.id',
                'primary_units.actual_name',
                'primary_units.short_name',
                'secondary_units.id',
                'secondary_units.actual_name',
                'secondary_units.short_name',
                'secondary_units.base_unit_multiplier',
                'secondary_units.base_unit_id',
                'base_units.actual_name',
                'base_units.short_name',
                'conversion_units.id',
                'conversion_units.actual_name',
                'conversion_units.short_name',
                'conversion_units.base_unit_multiplier',
                'brands.name',
                'tax_rates.name',
                'products.sku',
                'products.image',
                'products.enable_stock',
                'products.is_inactive',
                'products.not_for_selling',
                'variation_group_prices.price_group_id',
                'variation_group_prices.price_inc_tax'
            )
            ->get();

        // Group products by their group prices
        $grouped_products = [];
        foreach ($query as $product) {
            // Determine the display unit and multiplier
            $display_unit = $product->secondary_unit_short_name ?? $product->primary_unit_short_name;
            $base_multiplier = $product->base_unit_multiplier ?? 1;

            // Check for conversion relationship (like unit 65 -> unit 63)
            $unit_conversion_info = '';
            if ($product->conversion_unit_id && $product->conversion_multiplier) {
                $formatted_multiplier = number_format($product->conversion_multiplier, 0, '.', '');
                $unit_conversion_info = $product->conversion_unit_short_name . ' = ' .
                    $formatted_multiplier . ' ' .
                    ($product->primary_unit_short_name ?? $product->primary_unit_name);
            }

            if (!isset($grouped_products[$product->id])) {
                $grouped_products[$product->id] = [
                    'id' => $product->id,
                    'product_name' => $product->product_name,
                    'category_name' => $product->category,
                    'sub_category_name' => $product->sub_category,
                    'category_id' => $product->category_id ?? null,
                    'sku' => $product->sku,
                    'unit_id' => $product->secondary_unit_id ?? $product->primary_unit_id,
                    'unit_actual_name' => $display_unit,
                    'unit_base_multiplier' => $base_multiplier,
                    'unit_conversion_info' => $unit_conversion_info, // This will show "កេស = 155 sabuដុំ"
                    'brand' => $product->brand,
                    'tax' => $product->tax,
                    'image' => $product->image,
                    'enable_stock' => $product->enable_stock,
                    'is_inactive' => $product->is_inactive,
                    'not_for_selling' => $product->not_for_selling,
                    'min_purchase_price' => $product->min_purchase_price ?? 0,
                    'max_purchase_price' => $product->max_purchase_price ?? 0,
                    'group_prices' => []
                ];
            }

            // Add group prices for the product
            if ($product->price_group_id) {
                $grouped_products[$product->id]['group_prices'][$product->price_group_id] = $product->group_price_value;
            }
        }

        // Organize products by category and product name
        $products_by_category = [];
        foreach ($grouped_products as $product) {
            $category_id = $product['category_id'];
            $product_name = $product['product_name'];

            if (!isset($products_by_category[$category_id])) {
                $products_by_category[$category_id] = [];
            }

            // Add product to its category
            $products_by_category[$category_id][] = $product;
        }

        // Pass data to the view
        return view(
            'minireportb1::MiniReportB1.StandardReport.ProductReports.productPriceListByBatchCostGroup',
            compact('group_prices', 'categories', 'grouped_products', 'products_by_category')
        );
    }

    public function getCustomerSuppliers(Request $request)
    {
        if (!auth()->user()->can('contacts_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $contacts = Contact::where('contacts.business_id', $business_id)
                ->join('transactions AS t', 'contacts.id', '=', 't.contact_id')
                ->active()
                ->groupBy('contacts.id')
                ->select(
                    DB::raw("SUM(IF(t.type = 'purchase', final_total, 0)) as total_purchase"),
                    DB::raw("SUM(IF(t.type = 'purchase_return', final_total, 0)) as total_purchase_return"),
                    DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', final_total, 0)) as total_invoice"),
                    DB::raw("SUM(IF(t.type = 'purchase', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as purchase_paid"),
                    DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as invoice_received"),
                    DB::raw("SUM(IF(t.type = 'sell_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as sell_return_paid"),
                    DB::raw("SUM(IF(t.type = 'purchase_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as purchase_return_received"),
                    DB::raw("SUM(IF(t.type = 'sell_return', final_total, 0)) as total_sell_return"),
                    DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                    DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid"),
                    DB::raw("SUM(IF(t.type = 'ledger_discount' AND sub_type='sell_discount', final_total, 0)) as total_ledger_discount_sell"),
                    DB::raw("SUM(IF(t.type = 'ledger_discount' AND sub_type='purchase_discount', final_total, 0)) as total_ledger_discount_purchase"),
                    'contacts.supplier_business_name',
                    'contacts.name',
                    'contacts.id',
                    'contacts.type as contact_type'
                );
            $permitted_locations = auth()->user()->permitted_locations();

            if ($permitted_locations != 'all') {
                $contacts->whereIn('t.location_id', $permitted_locations);
            }

            if (!empty($request->input('customer_group_id'))) {
                $contacts->where('contacts.customer_group_id', $request->input('customer_group_id'));
            }

            if (!empty($request->input('location_id'))) {
                $contacts->where('t.location_id', $request->input('location_id'));
            }

            if (!empty($request->input('contact_id'))) {
                $contacts->where('t.contact_id', $request->input('contact_id'));
            }

            if (!empty($request->input('contact_type'))) {
                $contacts->whereIn('contacts.type', [$request->input('contact_type'), 'both']);
            }

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (!empty($start_date) && !empty($end_date)) {
                $contacts->where('t.transaction_date', '>=', $start_date)
                    ->where('t.transaction_date', '<=', $end_date);
            }

            return Datatables::of($contacts)
                ->editColumn('name', function ($row) {
                    $name = $row->name;
                    if (!empty($row->supplier_business_name)) {
                        $name .= ', ' . $row->supplier_business_name;
                    }

                    return '<a href="' . action([\App\Http\Controllers\ContactController::class, 'show'], [$row->id]) . '" target="_blank" class="no-print">' .
                        $name .
                        '</a>';
                })
                ->editColumn(
                    'total_purchase',
                    '<span class="total_purchase" data-orig-value="{{$total_purchase}}">@format_currency($total_purchase)</span>'
                )
                ->editColumn(
                    'total_purchase_return',
                    '<span class="total_purchase_return" data-orig-value="{{$total_purchase_return}}">@format_currency($total_purchase_return)</span>'
                )
                ->editColumn(
                    'total_sell_return',
                    '<span class="total_sell_return" data-orig-value="{{$total_sell_return}}">@format_currency($total_sell_return)</span>'
                )
                ->editColumn(
                    'total_invoice',
                    '<span class="total_invoice" data-orig-value="{{$total_invoice}}">@format_currency($total_invoice)</span>'
                )

                ->addColumn('due', function ($row) {
                    $total_ledger_discount_purchase = $row->total_ledger_discount_purchase ?? 0;
                    $total_ledger_discount_sell = $total_ledger_discount_sell ?? 0;
                    $due = ($row->total_invoice - $row->invoice_received - $total_ledger_discount_sell) - ($row->total_purchase - $row->purchase_paid - $total_ledger_discount_purchase) - ($row->total_sell_return - $row->sell_return_paid) + ($row->total_purchase_return - $row->purchase_return_received);

                    if ($row->contact_type == 'supplier') {
                        $due -= $row->opening_balance - $row->opening_balance_paid;
                    } else {
                        $due += $row->opening_balance - $row->opening_balance_paid;
                    }

                    $due_formatted = $this->transactionUtil->num_f($due, true);

                    return '<span class="total_due" data-orig-value="' . $due . '">' . $due_formatted . '</span>';
                })
                ->addColumn(
                    'opening_balance_due',
                    '<span class="opening_balance_due" data-orig-value="{{$opening_balance - $opening_balance_paid}}">@format_currency($opening_balance - $opening_balance_paid)</span>'
                )
                ->removeColumn('supplier_business_name')
                ->removeColumn('invoice_received')
                ->removeColumn('purchase_paid')
                ->removeColumn('id')
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                            ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['total_purchase', 'total_invoice', 'due', 'name', 'total_purchase_return', 'total_sell_return', 'opening_balance_due'])
                ->make(true);
        }

        $customer_group = CustomerGroup::forDropdown($business_id, false, true);
        $types = [
            '' => __('lang_v1.all'),
            'customer' => __('report.customer'),
            'supplier' => __('report.supplier'),
        ];

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        $contact_dropdown = Contact::contactDropdown($business_id, false, false);

        return view('minireportb1::MiniReportB1.StandardReport.ProductReports.contact')
            ->with(compact('customer_group', 'types', 'business_locations', 'contact_dropdown'));
    }

    public function getproductSellReport(Request $request)
    {
        if (!auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $custom_labels = json_decode(session('business.custom_labels'), true);

        $product_custom_field1 = !empty($custom_labels['product']['custom_field_1']) ? $custom_labels['product']['custom_field_1'] : '';
        $product_custom_field2 = !empty($custom_labels['product']['custom_field_2']) ? $custom_labels['product']['custom_field_2'] : '';

        if ($request->ajax()) {
            $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);

            $variation_id = $request->get('variation_id', null);
            $query = TransactionSellLine::join(
                'transactions as t',
                'transaction_sell_lines.transaction_id',
                '=',
                't.id'
            )
                ->join(
                    'variations as v',
                    'transaction_sell_lines.variation_id',
                    '=',
                    'v.id'
                )
                ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                ->join('contacts as c', 't.contact_id', '=', 'c.id')
                ->join('products as p', 'pv.product_id', '=', 'p.id')
                ->leftjoin('tax_rates', 'transaction_sell_lines.tax_id', '=', 'tax_rates.id')
                ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->with('transaction.payment_lines')
                ->select(
                    'p.name as product_name',
                    'p.type as product_type',
                    'p.product_custom_field1 as product_custom_field1',
                    'p.product_custom_field2 as product_custom_field2',
                    'pv.name as product_variation',
                    'v.name as variation_name',
                    'v.sub_sku',
                    'c.name as customer',
                    'c.supplier_business_name',
                    'c.contact_id',
                    't.id as transaction_id',
                    't.invoice_no',
                    't.transaction_date as transaction_date',
                    'transaction_sell_lines.unit_price_before_discount as unit_price',
                    'transaction_sell_lines.unit_price_inc_tax as unit_sale_price',
                    DB::raw('(transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) as sell_qty'),
                    'transaction_sell_lines.line_discount_type as discount_type',
                    'transaction_sell_lines.line_discount_amount as discount_amount',
                    'transaction_sell_lines.item_tax',
                    'tax_rates.name as tax',
                    'u.short_name as unit',
                    'transaction_sell_lines.parent_sell_line_id',
                    DB::raw('((transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) * transaction_sell_lines.unit_price_inc_tax) as subtotal')
                )
                ->groupBy('transaction_sell_lines.id');

            if (!empty($variation_id)) {
                $query->where('transaction_sell_lines.variation_id', $variation_id);
            }
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (!empty($start_date) && !empty($end_date)) {
                $query->where('t.transaction_date', '>=', $start_date)
                    ->where('t.transaction_date', '<=', $end_date);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            $location_id = $request->get('location_id', null);
            if (!empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }

            $customer_id = $request->get('customer_id', null);
            if (!empty($customer_id)) {
                $query->where('t.contact_id', $customer_id);
            }

            $customer_group_id = $request->get('customer_group_id', null);
            if (!empty($customer_group_id)) {
                $query->leftjoin('customer_groups AS CG', 'c.customer_group_id', '=', 'CG.id')
                    ->where('CG.id', $customer_group_id);
            }

            $category_id = $request->get('category_id', null);
            if (!empty($category_id)) {
                $query->where('p.category_id', $category_id);
            }

            $brand_id = $request->get('brand_id', null);
            if (!empty($brand_id)) {
                $query->where('p.brand_id', $brand_id);
            }

            return Datatables::of($query)
                ->editColumn('product_name', function ($row) {
                    $product_name = $row->product_name;
                    if ($row->product_type == 'variable') {
                        $product_name .= ' - ' . $row->product_variation . ' - ' . $row->variation_name;
                    }

                    return $product_name;
                })
                ->editColumn('invoice_no', function ($row) {
                    return '<a data-href="' . action([\App\Http\Controllers\SellController::class, 'show'], [$row->transaction_id])
                        . '" href="#" data-container=".view_modal" class="btn-modal">' . $row->invoice_no . '</a>';
                })
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn('unit_sale_price', function ($row) {
                    return '<span class="unit_sale_price" data-orig-value="' . $row->unit_sale_price . '">' .
                        $this->transactionUtil->num_f($row->unit_sale_price, true) . '</span>';
                })
                ->editColumn('sell_qty', function ($row) {
                    //ignore child sell line of combo product
                    $class = is_null($row->parent_sell_line_id) ? 'sell_qty' : '';

                    return '<span class="' . $class . '"  data-orig-value="' . $row->sell_qty . '" 
                    data-unit="' . $row->unit . '" >' .
                        $this->transactionUtil->num_f($row->sell_qty, false, null, true) . '</span> ' . $row->unit;
                })
                ->editColumn('subtotal', function ($row) {
                    //ignore child sell line of combo product
                    $class = is_null($row->parent_sell_line_id) ? 'row_subtotal' : '';

                    return '<span class="' . $class . '"  data-orig-value="' . $row->subtotal . '">' .
                        $this->transactionUtil->num_f($row->subtotal, true) . '</span>';
                })
                ->editColumn('unit_price', function ($row) {
                    return '<span class="unit_price" data-orig-value="' . $row->unit_price . '">' .
                        $this->transactionUtil->num_f($row->unit_price, true) . '</span>';
                })
                ->editColumn('discount_amount', '
                    @if($discount_type == "percentage")
                        {{@num_format($discount_amount)}} %
                    @elseif($discount_type == "fixed")
                        {{@num_format($discount_amount)}}
                    @endif
                    ')
                ->editColumn('tax', function ($row) {
                    return $this->transactionUtil->num_f($row->item_tax, true)
                        . '<br>' . '<span data-orig-value="' . $row->item_tax . '" 
                    class="tax" data-unit="' . $row->tax . '"><small>(' . $row->tax . ')</small></span>';
                })
                ->addColumn('payment_methods', function ($row) use ($payment_types) {
                    $methods = array_unique($row->transaction->payment_lines->pluck('method')->toArray());
                    $count = count($methods);
                    $payment_method = '';
                    if ($count == 1) {
                        $payment_method = $payment_types[$methods[0]] ?? '';
                    } elseif ($count > 1) {
                        $payment_method = __('lang_v1.checkout_multi_pay');
                    }

                    $html = !empty($payment_method) ? '<span class="payment-method" data-orig-value="' . $payment_method . '" data-status-name="' . $payment_method . '">' . $payment_method . '</span>' : '';

                    return $html;
                })
                ->editColumn('customer', '@if(!empty($supplier_business_name)) {{$supplier_business_name}},<br>@endif {{$customer}}')
                ->rawColumns(['invoice_no', 'unit_sale_price', 'subtotal', 'sell_qty', 'discount_amount', 'unit_price', 'tax', 'customer', 'payment_methods'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $customers = Contact::customersDropdown($business_id);
        $categories = Category::forDropdown($business_id, 'product');
        $brands = Brands::forDropdown($business_id);
        $customer_group = CustomerGroup::forDropdown($business_id, false, true);

        return view('minireportb1::MiniReportB1.StandardReport.ProductReports.product_sell_report')
            ->with(compact(
                'business_locations',
                'customers',
                'categories',
                'brands',
                'customer_group',
                'product_custom_field1',
                'product_custom_field2'
            ));
    }


    public function getproductPurchaseReport(Request $request)
    {
        if (!auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        if ($request->ajax()) {
            $variation_id = $request->get('variation_id', null);
            $query = PurchaseLine::join(
                'transactions as t',
                'purchase_lines.transaction_id',
                '=',
                't.id'
            )
                ->join(
                    'variations as v',
                    'purchase_lines.variation_id',
                    '=',
                    'v.id'
                )
                ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                ->join('contacts as c', 't.contact_id', '=', 'c.id')
                ->join('products as p', 'pv.product_id', '=', 'p.id')
                ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'purchase')
                ->select(
                    'p.name as product_name',
                    'p.type as product_type',
                    'pv.name as product_variation',
                    'v.name as variation_name',
                    'v.sub_sku',
                    'c.name as supplier',
                    'c.supplier_business_name',
                    't.id as transaction_id',
                    't.ref_no',
                    't.transaction_date as transaction_date',
                    'purchase_lines.purchase_price_inc_tax as unit_purchase_price',
                    DB::raw('(purchase_lines.quantity - purchase_lines.quantity_returned) as purchase_qty'),
                    'purchase_lines.quantity_adjusted',
                    'u.short_name as unit',
                    DB::raw('((purchase_lines.quantity - purchase_lines.quantity_returned - purchase_lines.quantity_adjusted) * purchase_lines.purchase_price_inc_tax) as subtotal')
                )
                ->groupBy('purchase_lines.id');
            if (!empty($variation_id)) {
                $query->where('purchase_lines.variation_id', $variation_id);
            }
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            $location_id = $request->get('location_id', null);
            if (!empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }

            $supplier_id = $request->get('supplier_id', null);
            if (!empty($supplier_id)) {
                $query->where('t.contact_id', $supplier_id);
            }

            $brand_id = $request->get('brand_id', null);
            if (!empty($brand_id)) {
                $query->where('p.brand_id', $brand_id);
            }

            return Datatables::of($query)
                ->editColumn('product_name', function ($row) {
                    $product_name = $row->product_name;
                    if ($row->product_type == 'variable') {
                        $product_name .= ' - ' . $row->product_variation . ' - ' . $row->variation_name;
                    }

                    return $product_name;
                })
                ->editColumn('ref_no', function ($row) {
                    return '<a data-href="' . action([\App\Http\Controllers\PurchaseController::class, 'show'], [$row->transaction_id])
                        . '" href="#" data-container=".view_modal" class="btn-modal">' . $row->ref_no . '</a>';
                })
                ->editColumn('purchase_qty', function ($row) {
                    return '<span data-is_quantity="true" class="display_currency purchase_qty" data-currency_symbol=false data-orig-value="' . (float) $row->purchase_qty . '" data-unit="' . $row->unit . '" >' . (float) $row->purchase_qty . '</span> ' . $row->unit;
                })
                ->editColumn('quantity_adjusted', function ($row) {
                    return '<span data-is_quantity="true" class="display_currency quantity_adjusted" data-currency_symbol=false data-orig-value="' . (float) $row->quantity_adjusted . '" data-unit="' . $row->unit . '" >' . (float) $row->quantity_adjusted . '</span> ' . $row->unit;
                })
                ->editColumn('subtotal', function ($row) {
                    return '<span class="row_subtotal"  
                     data-orig-value="' . $row->subtotal . '">' .
                        $this->transactionUtil->num_f($row->subtotal, true) . '</span>';
                })
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('unit_purchase_price', function ($row) {
                    return $this->transactionUtil->num_f($row->unit_purchase_price, true);
                })
                ->editColumn('supplier', '@if(!empty($supplier_business_name)) {{$supplier_business_name}},<br>@endif {{$supplier}}')
                ->rawColumns(['ref_no', 'unit_purchase_price', 'subtotal', 'purchase_qty', 'quantity_adjusted', 'supplier'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers = Contact::suppliersDropdown($business_id);
        $brands = Brands::forDropdown($business_id);

        return view('minireportb1::MiniReportB1.StandardReport.ProductReports.product_purchase_report')
            ->with(compact('business_locations', 'suppliers', 'brands'));
    }


    public function itemsReport()
    {
        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $query = TransactionSellLinesPurchaseLines::leftJoin('transaction_sell_lines 
                    as SL', 'SL.id', '=', 'transaction_sell_lines_purchase_lines.sell_line_id')
                ->leftJoin('stock_adjustment_lines 
                    as SAL', 'SAL.id', '=', 'transaction_sell_lines_purchase_lines.stock_adjustment_line_id')
                ->leftJoin('transactions as sale', 'SL.transaction_id', '=', 'sale.id')
                ->leftJoin('transactions as stock_adjustment', 'SAL.transaction_id', '=', 'stock_adjustment.id')
                ->join('purchase_lines as PL', 'PL.id', '=', 'transaction_sell_lines_purchase_lines.purchase_line_id')
                ->join('transactions as purchase', 'PL.transaction_id', '=', 'purchase.id')
                ->join('business_locations as bl', 'purchase.location_id', '=', 'bl.id')
                ->join(
                    'variations as v',
                    'PL.variation_id',
                    '=',
                    'v.id'
                )
                ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                ->join('products as p', 'PL.product_id', '=', 'p.id')
                ->join('units as u', 'p.unit_id', '=', 'u.id')
                ->leftJoin('contacts as suppliers', 'purchase.contact_id', '=', 'suppliers.id')
                ->leftJoin('contacts as customers', 'sale.contact_id', '=', 'customers.id')
                ->where('purchase.business_id', $business_id)
                ->select(
                    'v.sub_sku as sku',
                    'p.type as product_type',
                    'p.name as product_name',
                    'v.name as variation_name',
                    'pv.name as product_variation',
                    'u.short_name as unit',
                    'purchase.transaction_date as purchase_date',
                    'purchase.ref_no as purchase_ref_no',
                    'purchase.type as purchase_type',
                    'purchase.id as purchase_id',
                    'suppliers.name as supplier',
                    'suppliers.supplier_business_name',
                    'PL.purchase_price_inc_tax as purchase_price',
                    'sale.transaction_date as sell_date',
                    'stock_adjustment.transaction_date as stock_adjustment_date',
                    'sale.invoice_no as sale_invoice_no',
                    'stock_adjustment.ref_no as stock_adjustment_ref_no',
                    'customers.name as customer',
                    'customers.supplier_business_name as customer_business_name',
                    'transaction_sell_lines_purchase_lines.quantity as quantity',
                    'SL.unit_price_inc_tax as selling_price',
                    'SAL.unit_price as stock_adjustment_price',
                    'transaction_sell_lines_purchase_lines.stock_adjustment_line_id',
                    'transaction_sell_lines_purchase_lines.sell_line_id',
                    'transaction_sell_lines_purchase_lines.purchase_line_id',
                    'transaction_sell_lines_purchase_lines.qty_returned',
                    'bl.name as location',
                    'SL.sell_line_note',
                    'PL.lot_number'
                );

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('purchase.location_id', $permitted_locations);
            }

            if (!empty(request()->purchase_start) && !empty(request()->purchase_end)) {
                $start = request()->purchase_start;
                $end = request()->purchase_end;
                $query->whereDate('purchase.transaction_date', '>=', $start)
                    ->whereDate('purchase.transaction_date', '<=', $end);
            }
            if (!empty(request()->sale_start) && !empty(request()->sale_end)) {
                $start = request()->sale_start;
                $end = request()->sale_end;
                $query->where(function ($q) use ($start, $end) {
                    $q->where(function ($qr) use ($start, $end) {
                        $qr->whereDate('sale.transaction_date', '>=', $start)
                            ->whereDate('sale.transaction_date', '<=', $end);
                    })->orWhere(function ($qr) use ($start, $end) {
                        $qr->whereDate('stock_adjustment.transaction_date', '>=', $start)
                            ->whereDate('stock_adjustment.transaction_date', '<=', $end);
                    });
                });
            }

            $supplier_id = request()->get('supplier_id', null);
            if (!empty($supplier_id)) {
                $query->where('suppliers.id', $supplier_id);
            }

            $customer_id = request()->get('customer_id', null);
            if (!empty($customer_id)) {
                $query->where('customers.id', $customer_id);
            }

            $location_id = request()->get('location_id', null);
            if (!empty($location_id)) {
                $query->where('purchase.location_id', $location_id);
            }

            $only_mfg_products = request()->get('only_mfg_products', 0);
            if (!empty($only_mfg_products)) {
                $query->where('purchase.type', 'production_purchase');
            }

            return Datatables::of($query)
                ->editColumn('product_name', function ($row) {
                    $product_name = $row->product_name;
                    if ($row->product_type == 'variable') {
                        $product_name .= ' - ' . $row->product_variation . ' - ' . $row->variation_name;
                    }

                    return $product_name;
                })
                ->editColumn('purchase_date', '{{@format_datetime($purchase_date)}}')
                ->editColumn('purchase_ref_no', function ($row) {
                    $html = $row->purchase_type == 'purchase' ? '<a data-href="' . action([\App\Http\Controllers\PurchaseController::class, 'show'], [$row->purchase_id])
                        . '" href="#" data-container=".view_modal" class="btn-modal">' . $row->purchase_ref_no . '</a>' : $row->purchase_ref_no;
                    if ($row->purchase_type == 'opening_stock') {
                        $html .= '(' . __('lang_v1.opening_stock') . ')';
                    }

                    return $html;
                })
                ->editColumn('purchase_price', function ($row) {
                    return '<span 
                    class="purchase_price" data-orig-value="' . $row->purchase_price . '">' .
                        $this->transactionUtil->num_f($row->purchase_price, true) . '</span>';
                })
                ->editColumn('sell_date', '@if(!empty($sell_line_id)) {{@format_datetime($sell_date)}} @else {{@format_datetime($stock_adjustment_date)}} @endif')

                ->editColumn('sale_invoice_no', function ($row) {
                    $invoice_no = !empty($row->sell_line_id) ? $row->sale_invoice_no : $row->stock_adjustment_ref_no . '<br><small>(' . __('stock_adjustment.stock_adjustment') . '</small)>';

                    return $invoice_no;
                })
                ->editColumn('quantity', function ($row) {
                    $html = '<span data-is_quantity="true" class="display_currency quantity" data-currency_symbol=false data-orig-value="' . (float) $row->quantity . '" data-unit="' . $row->unit . '" >' . (float) $row->quantity . '</span> ' . $row->unit;

                    if (empty($row->sell_line_id)) {
                        $html .= '<br><small>(' . __('stock_adjustment.stock_adjustment') . '</small)>';
                    }
                    if ($row->qty_returned > 0) {
                        $html .= '<small><i>(<span data-is_quantity="true" class="display_currency" data-currency_symbol=false>' . (float) $row->quantity . '</span> ' . $row->unit . ' ' . __('lang_v1.returned') . ')</i></small>';
                    }

                    return $html;
                })
                ->editColumn('selling_price', function ($row) {
                    $selling_price = !empty($row->sell_line_id) ? $row->selling_price : $row->stock_adjustment_price;

                    return '<span class="row_selling_price" data-orig-value="' . $selling_price .
                        '">' . $this->transactionUtil->num_f($selling_price, true) . '</span>';
                })

                ->addColumn('subtotal', function ($row) {
                    $selling_price = !empty($row->sell_line_id) ? $row->selling_price : $row->stock_adjustment_price;
                    $subtotal = $selling_price * $row->quantity;

                    return '<span class="row_subtotal" data-orig-value="' . $subtotal . '">' .
                        $this->transactionUtil->num_f($subtotal, true) . '</span>';
                })
                ->editColumn('supplier', '@if(!empty($supplier_business_name))
                {{$supplier_business_name}},<br> @endif {{$supplier}}')
                ->editColumn('customer', '@if(!empty($customer_business_name))
                {{$customer_business_name}},<br> @endif {{$customer}}')
                ->filterColumn('sale_invoice_no', function ($query, $keyword) {
                    $query->where('sale.invoice_no', 'like', ["%{$keyword}%"])
                        ->orWhere('stock_adjustment.ref_no', 'like', ["%{$keyword}%"]);
                })

                ->rawColumns(['subtotal', 'selling_price', 'quantity', 'purchase_price', 'sale_invoice_no', 'purchase_ref_no', 'supplier', 'customer'])
                ->make(true);
        }

        $suppliers = Contact::suppliersDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);
        $business_locations = BusinessLocation::forDropdown($business_id);

        return view('minireportb1::MiniReportB1.StandardReport.ProductReports.items_report')->with(compact('suppliers', 'customers', 'business_locations'));
    }

    public function getTrendingProducts(Request $request)
    {
        if (!auth()->user()->can('trending_product_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $filters = request()->only(['category', 'sub_category', 'brand', 'unit', 'limit', 'location_id', 'product_type']);

        $date_range = request()->input('date_range');

        if (!empty($date_range)) {
            $date_range_array = explode('~', $date_range);
            $filters['start_date'] = $this->transactionUtil->uf_date(trim($date_range_array[0]));
            $filters['end_date'] = $this->transactionUtil->uf_date(trim($date_range_array[1]));
        }

        $products = $this->productUtil->getTrendingProducts($business_id, $filters);

        $values = [];
        $labels = [];
        foreach ($products as $product) {
            $values[] = (float) $product->total_unit_sold;
            $labels[] = $product->product . ' - ' . $product->sku . ' (' . $product->unit . ')';
        }

        $chart = new CommonChart;
        $chart->labels($labels)
            ->dataset(__('report.total_unit_sold'), 'column', $values);

        $categories = Category::forDropdown($business_id, 'product');
        $brands = Brands::forDropdown($business_id);
        $units = Unit::where('business_id', $business_id)
            ->pluck('short_name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('minireportb1::MiniReportB1.card-menu.report.trending_products')
            ->with(compact('chart', 'categories', 'brands', 'units', 'business_locations'));
    }

    public function getStockAdjustmentReport(Request $request)
    {
        if (!auth()->user()->can('stock_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $query = Transaction::where('business_id', $business_id)
                ->where('type', 'stock_adjustment');

            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('location_id', $permitted_locations);
            }

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }
            $location_id = $request->get('location_id');
            if (!empty($location_id)) {
                $query->where('location_id', $location_id);
            }

            $stock_adjustment_details = $query->select(
                DB::raw('SUM(final_total) as total_amount'),
                DB::raw('SUM(total_amount_recovered) as total_recovered'),
                DB::raw("SUM(IF(adjustment_type = 'normal', final_total, 0)) as total_normal"),
                DB::raw("SUM(IF(adjustment_type = 'abnormal', final_total, 0)) as total_abnormal")
            )->first();

            return $stock_adjustment_details;
        }
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('minireportb1::MiniReportB1.StandardReport.ProductReports.stock_adjustment_report')
            ->with(compact('business_locations'));
    }


    public function getStockExpiryReport(Request $request)
    {
        if (!auth()->user()->can('stock_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //TODO:: Need to display reference number and edit expiry date button

        //Return the details in ajax call
        if ($request->ajax()) {
            $query = PurchaseLine::leftjoin(
                'transactions as t',
                'purchase_lines.transaction_id',
                '=',
                't.id'
            )
                ->leftjoin(
                    'products as p',
                    'purchase_lines.product_id',
                    '=',
                    'p.id'
                )
                ->leftjoin(
                    'variations as v',
                    'purchase_lines.variation_id',
                    '=',
                    'v.id'
                )
                ->leftjoin(
                    'product_variations as pv',
                    'v.product_variation_id',
                    '=',
                    'pv.id'
                )
                ->leftjoin('business_locations as l', 't.location_id', '=', 'l.id')
                ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                ->where('t.business_id', $business_id)
                //->whereNotNull('p.expiry_period')
                //->whereNotNull('p.expiry_period_type')
                //->whereNotNull('exp_date')
                ->where('p.enable_stock', 1);
            // ->whereRaw('purchase_lines.quantity > purchase_lines.quantity_sold + quantity_adjusted + quantity_returned');

            $permitted_locations = auth()->user()->permitted_locations();

            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            if (!empty($request->input('location_id'))) {
                $location_id = $request->input('location_id');
                $query->where('t.location_id', $location_id)
                    //If filter by location then hide products not available in that location
                    ->join('product_locations as pl', 'pl.product_id', '=', 'p.id')
                    ->where(function ($q) use ($location_id) {
                        $q->where('pl.location_id', $location_id);
                    });
            }

            if (!empty($request->input('category_id'))) {
                $query->where('p.category_id', $request->input('category_id'));
            }
            if (!empty($request->input('sub_category_id'))) {
                $query->where('p.sub_category_id', $request->input('sub_category_id'));
            }
            if (!empty($request->input('brand_id'))) {
                $query->where('p.brand_id', $request->input('brand_id'));
            }
            if (!empty($request->input('unit_id'))) {
                $query->where('p.unit_id', $request->input('unit_id'));
            }
            if (!empty($request->input('exp_date_filter'))) {
                $query->whereDate('exp_date', '<=', $request->input('exp_date_filter'));
            }

            $only_mfg_products = request()->get('only_mfg_products', 0);
            if (!empty($only_mfg_products)) {
                $query->where('t.type', 'production_purchase');
            }

            $report = $query->select(
                'p.name as product',
                'p.sku',
                'p.type as product_type',
                'v.name as variation',
                'v.sub_sku',
                'pv.name as product_variation',
                'l.name as location',
                'mfg_date',
                'exp_date',
                'u.short_name as unit',
                DB::raw('SUM(COALESCE(quantity, 0) - COALESCE(quantity_sold, 0) - COALESCE(quantity_adjusted, 0) - COALESCE(quantity_returned, 0)) as stock_left'),
                't.ref_no',
                't.id as transaction_id',
                'purchase_lines.id as purchase_line_id',
                'purchase_lines.lot_number'
            )
                ->having('stock_left', '>', 0)
                ->groupBy('purchase_lines.variation_id')
                ->groupBy('purchase_lines.exp_date')
                ->groupBy('purchase_lines.lot_number');

            return Datatables::of($report)
                ->editColumn('product', function ($row) {
                    if ($row->product_type == 'variable') {
                        return $row->product . ' - ' .
                            $row->product_variation . ' - ' . $row->variation . ' (' . $row->sub_sku . ')';
                    } else {
                        return $row->product . ' (' . $row->sku . ')';
                    }
                })
                ->editColumn('mfg_date', function ($row) {
                    if (!empty($row->mfg_date)) {
                        return $this->productUtil->format_date($row->mfg_date);
                    } else {
                        return '--';
                    }
                })

                ->editColumn('ref_no', function ($row) {
                    return '<button type="button" data-href="' . action([\App\Http\Controllers\PurchaseController::class, 'show'], [$row->transaction_id])
                        . '" class="btn btn-link btn-modal" data-container=".view_modal"  >' . $row->ref_no . '</button>';
                })
                ->editColumn('stock_left', function ($row) {
                    return '<span data-is_quantity="true" class="display_currency stock_left" data-currency_symbol=false data-orig-value="' . $row->stock_left . '" data-unit="' . $row->unit . '" >' . $row->stock_left . '</span> ' . $row->unit;
                })
                ->addColumn('edit', function ($row) {
                    $html = '<button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-primary stock_expiry_edit_btn" data-transaction_id="' . $row->transaction_id . '" data-purchase_line_id="' . $row->purchase_line_id . '"> <i class="fa fa-edit"></i> ' . __('messages.edit') .
                        '</button>';

                    if (!empty($row->exp_date)) {
                        $carbon_exp = \Carbon::createFromFormat('Y-m-d', $row->exp_date);
                        $carbon_now = \Carbon::now();
                        if ($carbon_now->diffInDays($carbon_exp, false) < 0) {
                            $html .= ' <button type="button" class="btn btn-warning btn-xs remove_from_stock_btn" data-href="' . action([\App\Http\Controllers\StockAdjustmentController::class, 'removeExpiredStock'], [$row->purchase_line_id]) . '"> <i class="fa fa-trash"></i> ' . __('lang_v1.remove_from_stock') .
                                '</button>';
                        }
                    }

                    return $html;
                })
                ->rawColumns(['exp_date', 'ref_no', 'edit', 'stock_left'])
                ->make(true);
        }

        $categories = Category::forDropdown($business_id, 'product');
        $brands = Brands::forDropdown($business_id);
        $units = Unit::where('business_id', $business_id)
            ->pluck('short_name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        $view_stock_filter = [
            \Carbon::now()->subDay()->format('Y-m-d') => __('report.expired'),
            \Carbon::now()->addWeek()->format('Y-m-d') => __('report.expiring_in_1_week'),
            \Carbon::now()->addDays(15)->format('Y-m-d') => __('report.expiring_in_15_days'),
            \Carbon::now()->addMonth()->format('Y-m-d') => __('report.expiring_in_1_month'),
            \Carbon::now()->addMonths(3)->format('Y-m-d') => __('report.expiring_in_3_months'),
            \Carbon::now()->addMonths(6)->format('Y-m-d') => __('report.expiring_in_6_months'),
            \Carbon::now()->addYear()->format('Y-m-d') => __('report.expiring_in_1_year'),
        ];

        return view('minireportb1::MiniReportB1.StandardReport.ProductReports.stock_expiry_report')
            ->with(compact('categories', 'brands', 'units', 'business_locations', 'view_stock_filter'));
    }


    public function getStockReport(Request $request)
    {
        if (!auth()->user()->can('stock_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $selling_price_groups = SellingPriceGroup::where('business_id', $business_id)
            ->get();
        $allowed_selling_price_group = false;
        foreach ($selling_price_groups as $selling_price_group) {
            if (auth()->user()->can('selling_price_group.' . $selling_price_group->id)) {
                $allowed_selling_price_group = true;
                break;
            }
        }
        if ($this->moduleUtil->isModuleInstalled('Manufacturing') && (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'manufacturing_module'))) {
            $show_manufacturing_data = 1;
        } else {
            $show_manufacturing_data = 0;
        }
        if ($request->ajax()) {
            $filters = request()->only([
                'location_id',
                'category_id',
                'sub_category_id',
                'brand_id',
                'unit_id',
                'tax_id',
                'type',
                'only_mfg_products',
                'active_state',
                'not_for_selling',
                'repair_model_id',
                'product_id',
                'active_state',
            ]);

            $filters['not_for_selling'] = isset($filters['not_for_selling']) && $filters['not_for_selling'] == 'true' ? 1 : 0;

            $filters['show_manufacturing_data'] = $show_manufacturing_data;

            //Return the details in ajax call
            $for = request()->input('for') == 'view_product' ? 'view_product' : 'datatables';

            $products = $this->productUtil->getProductStockDetails($business_id, $filters, $for);
            //To show stock details on view product modal
            if ($for == 'view_product' && !empty(request()->input('product_id'))) {
                $product_stock_details = $products;

                return view('minireportb1::MiniReportB1.card-menu.product.partials.product_stock_details')->with(compact('product_stock_details'));
            }

            $datatable = Datatables::of($products)
                ->editColumn('stock', function ($row) {
                    if ($row->enable_stock) {
                        $stock = $row->stock ? $row->stock : 0;

                        return '<span class="current_stock" data-orig-value="' . (float) $stock . '" data-unit="' . $row->unit . '"> ' . $this->transactionUtil->num_f($stock, false, null, true) . '</span>' . ' ' . $row->unit;
                    } else {
                        return '--';
                    }
                })
                ->editColumn('product', function ($row) {
                    $name = $row->product;

                    return $name;
                })
                ->addColumn('action', function ($row) {
                    return '<a class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-info tw-w-max " href="' . action([\App\Http\Controllers\ProductController::class, 'productStockHistory'], [$row->product_id]) .
                        '?location_id=' . $row->location_id . '&variation_id=' . $row->variation_id .
                        '"><i class="fas fa-history"></i> ' . __('lang_v1.product_stock_history') . '</a>';
                })
                ->addColumn('variation', function ($row) {
                    $variation = '';
                    if ($row->type == 'variable') {
                        $variation .= $row->product_variation . '-' . $row->variation_name;
                    }

                    return $variation;
                })
                ->editColumn('total_sold', function ($row) {
                    $total_sold = 0;
                    if ($row->total_sold) {
                        $total_sold = (float) $row->total_sold;
                    }

                    return '<span data-is_quantity="true" class="total_sold" data-orig-value="' . $total_sold . '" data-unit="' . $row->unit . '" >' . $this->transactionUtil->num_f($total_sold, false, null, true) . '</span> ' . $row->unit;
                })
                ->editColumn('total_transfered', function ($row) {
                    $total_transfered = 0;
                    if ($row->total_transfered) {
                        $total_transfered = (float) $row->total_transfered;
                    }

                    return '<span class="total_transfered" data-orig-value="' . $total_transfered . '" data-unit="' . $row->unit . '" >' . $this->transactionUtil->num_f($total_transfered, false, null, true) . '</span> ' . $row->unit;
                })

                ->editColumn('total_adjusted', function ($row) {
                    $total_adjusted = 0;
                    if ($row->total_adjusted) {
                        $total_adjusted = (float) $row->total_adjusted;
                    }

                    return '<span class="total_adjusted" data-orig-value="' . $total_adjusted . '" data-unit="' . $row->unit . '" >' . $this->transactionUtil->num_f($total_adjusted, false, null, true) . '</span> ' . $row->unit;
                })
                ->editColumn('unit_price', function ($row) use ($allowed_selling_price_group) {
                    $html = '';
                    if (auth()->user()->can('access_default_selling_price')) {
                        $html .= $this->transactionUtil->num_f($row->unit_price, true);
                    }

                    if ($allowed_selling_price_group) {
                        $html .= ' <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-primary tw-w-max btn-modal no-print" data-container=".view_modal" data-href="' . action([\App\Http\Controllers\ProductController::class, 'viewGroupPrice'], [$row->product_id]) . '">' . __('lang_v1.view_group_prices') . '</button>';
                    }

                    return $html;
                })
                ->editColumn('stock_price', function ($row) {
                    $html = '<span class="total_stock_price" data-orig-value="'
                        . $row->stock_price . '">' .
                        $this->transactionUtil->num_f($row->stock_price, true) . '</span>';

                    return $html;
                })
                ->editColumn('stock_value_by_sale_price', function ($row) {
                    $stock = $row->stock ? $row->stock : 0;
                    $unit_selling_price = (float) $row->group_price > 0 ? $row->group_price : $row->unit_price;
                    $stock_price = $stock * $unit_selling_price;

                    return '<span class="stock_value_by_sale_price" data-orig-value="' . (float) $stock_price . '" > ' . $this->transactionUtil->num_f($stock_price, true) . '</span>';
                })
                ->addColumn('potential_profit', function ($row) {
                    $stock = $row->stock ? $row->stock : 0;
                    $unit_selling_price = (float) $row->group_price > 0 ? $row->group_price : $row->unit_price;
                    $stock_price_by_sp = $stock * $unit_selling_price;
                    $potential_profit = (float) $stock_price_by_sp - (float) $row->stock_price;

                    return '<span class="potential_profit" data-orig-value="' . (float) $potential_profit . '" > ' . $this->transactionUtil->num_f($potential_profit, true) . '</span>';
                })
                ->setRowClass(function ($row) {
                    return $row->enable_stock && $row->stock <= $row->alert_quantity ? 'bg-danger' : '';
                })
                ->filterColumn('variation', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(pv.name, ''), '-', COALESCE(variations.name, '')) like ?", ["%{$keyword}%"]);
                })
                ->removeColumn('enable_stock')
                ->removeColumn('unit')
                ->removeColumn('id');

            $raw_columns = [
                'unit_price',
                'total_transfered',
                'total_sold',
                'total_adjusted',
                'stock',
                'stock_price',
                'stock_value_by_sale_price',
                'potential_profit',
                'action',
            ];

            if ($show_manufacturing_data) {
                $datatable->editColumn('total_mfg_stock', function ($row) {
                    $total_mfg_stock = 0;
                    if ($row->total_mfg_stock) {
                        $total_mfg_stock = (float) $row->total_mfg_stock;
                    }

                    return '<span data-is_quantity="true" class="total_mfg_stock"  data-orig-value="' . $total_mfg_stock . '" data-unit="' . $row->unit . '" >' . $this->transactionUtil->num_f($total_mfg_stock, false, null, true) . '</span> ' . $row->unit;
                });
                $raw_columns[] = 'total_mfg_stock';
            }

            return $datatable->rawColumns($raw_columns)->make(true);
        }

        $categories = Category::forDropdown($business_id, 'product');
        $brands = Brands::forDropdown($business_id);
        $units = Unit::where('business_id', $business_id)
            ->pluck('short_name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('minireportb1::MiniReportB1.StandardReport.ProductReports.stock_report')
            ->with(compact('categories', 'brands', 'units', 'business_locations', 'show_manufacturing_data'));
    }


    public function getExpenseList()
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
            return view('minireportb1::MiniReportB1.StandardReport.ProductReports.exspend_list', [
                'formatted_data' => $formatted_data,
            ]);
        } catch (\Exception $e) {        }
    }


    public function QuarterlyReport(Request $request)
    {
        if (!auth()->user()->can('quickbooks.Supplier&Customer_Report')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        // Debugging: Log the request parameters
        // Return the details in ajax call
        if ($request->ajax()) {
            // Calculate the start date for the last 3 months
            $threeMonthsAgo = now()->subMonths(3)->startOfDay();

            $contacts = Contact::where('contacts.business_id', $business_id)
                ->join('transactions AS t', 'contacts.id', '=', 't.contact_id')
                ->leftJoin('business_locations AS bl', 't.location_id', '=', 'bl.id') // Join business_locations table
                ->active()
                ->groupBy('contacts.id')
                ->select(
                    DB::raw("SUM(IF(t.type = 'purchase', final_total, 0)) as total_purchase"),
                    DB::raw("SUM(IF(t.type = 'purchase_return', final_total, 0)) as total_purchase_return"),
                    DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', final_total, 0)) as total_invoice"),
                    DB::raw("SUM(IF(t.type = 'purchase', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as purchase_paid"),
                    DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as invoice_received"),
                    DB::raw("SUM(IF(t.type = 'sell_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as sell_return_paid"),
                    DB::raw("SUM(IF(t.type = 'purchase_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as purchase_return_received"),
                    DB::raw("SUM(IF(t.type = 'sell_return', final_total, 0)) as total_sell_return"),
                    DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                    DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid"),
                    DB::raw("SUM(IF(t.type = 'ledger_discount' AND sub_type='sell_discount', final_total, 0)) as total_ledger_discount_sell"),
                    DB::raw("SUM(IF(t.type = 'ledger_discount' AND sub_type='purchase_discount', final_total, 0)) as total_ledger_discount_purchase"),
                    'contacts.supplier_business_name',
                    'contacts.name',
                    'contacts.mobile', // Include contact number
                    'contacts.id',
                    'contacts.type as contact_type',
                    'contacts.city', // Include city
                    'contacts.state', // Include state
                    'contacts.country' // Include country
                );

            // Handle 'last_3_months' filter if provided
            if ($request->has('last_3_months') && $request->input('last_3_months') == 1) {
                $contacts->where('t.transaction_date', '>=', $threeMonthsAgo);
            }

            $permitted_locations = auth()->user()->permitted_locations();

            if ($permitted_locations != 'all') {
                $contacts->whereIn('t.location_id', $permitted_locations);
            }

            // Apply customer group filter
            if (!empty($request->input('customer_group_id'))) {
                $contacts->where('contacts.customer_group_id', $request->input('customer_group_id'));
            }

            // Apply location filter
            if (!empty($request->input('location_id'))) {
                $contacts->where('t.location_id', $request->input('location_id'));
            }

            // Apply contact filter
            if (!empty($request->input('contact_id'))) {
                $contacts->where('t.contact_id', $request->input('contact_id'));
            }

            // Apply contact type filter
            if (!empty($request->input('contact_type'))) {
                $contacts->whereIn('contacts.type', [$request->input('contact_type'), 'both']);
            }

            // Handle date range filter
            if (!empty($request->input('date_range'))) {
                $dates = explode(' - ', $request->input('date_range'));
                if (count($dates) == 2) {
                    $start_date = $dates[0];
                    $end_date = $dates[1];
                    $contacts->whereBetween('t.transaction_date', [$start_date, $end_date]);
                }
            }

            // Apply total sale filter
            if ($request->has('total_sale_filter') && !empty($request->input('total_sale_filter'))) {
                $totalSaleFilter = $request->input('total_sale_filter');
                $contacts->having('total_invoice', '>', $totalSaleFilter);
            }

            // Debugging: Log the final SQL query with bindings
            return Datatables::of($contacts)
                ->editColumn('name', function ($row) {
                    // Display supplier_business_name before name
                    $name = '';
                    if (!empty($row->supplier_business_name)) {
                        $name .= $row->supplier_business_name . ', ';
                    }
                    $name .= $row->name;

                    return '<a href="' . action([\App\Http\Controllers\ContactController::class, 'show'], [$row->id]) . '" target="_blank" class="no-print">' .
                        $name .
                        '</a>';
                })
                ->editColumn(
                    'total_purchase',
                    function ($row) {
                        return '<span class="total_purchase" data-orig-value="' . $row->total_purchase . '">' .
                            number_format($row->total_purchase, 2) . '</span>';
                    }
                )
                ->editColumn(
                    'total_purchase_return',
                    function ($row) {
                        return '<span class="total_purchase_return" data-orig-value="' . $row->total_purchase_return . '">' .
                            number_format($row->total_purchase_return, 2) . '</span>';
                    }
                )
                ->editColumn(
                    'total_sell_return',
                    function ($row) {
                        return '<span class="total_sell_return" data-orig-value="' . $row->total_sell_return . '">' .
                            number_format($row->total_sell_return, 2) . '</span>';
                    }
                )
                ->editColumn(
                    'total_invoice',
                    function ($row) {
                        return '<span class="total_invoice" data-orig-value="' . $row->total_invoice . '">' .
                            number_format($row->total_invoice, 2) . '</span>';
                    }
                )
                ->addColumn('due', function ($row) {
                    $total_ledger_discount_sell = $row->total_ledger_discount_sell ?? 0;
                    $total_ledger_discount_purchase = $row->total_ledger_discount_purchase ?? 0;
                    $due = ($row->total_invoice - $row->invoice_received - $total_ledger_discount_sell)
                        - ($row->total_purchase - $row->purchase_paid - $total_ledger_discount_purchase)
                        - ($row->total_sell_return - $row->sell_return_paid)
                        + ($row->total_purchase_return - $row->purchase_return_received);

                    if ($row->contact_type == 'supplier') {
                        $due -= ($row->opening_balance - $row->opening_balance_paid);
                    } else {
                        $due += ($row->opening_balance - $row->opening_balance_paid);
                    }

                    $due_formatted = number_format($due, 2); // Assuming you have a formatter
    
                    return '<span class="total_due" data-orig-value="' . $due . '">' . $due_formatted . '</span>';
                })
                ->addColumn(
                    'opening_balance_due',
                    function ($row) {
                        $opening_balance_due = $row->opening_balance - $row->opening_balance_paid;
                        return '<span class="opening_balance_due" data-orig-value="' . $opening_balance_due . '">' .
                            number_format($opening_balance_due, 2) . '</span>';
                    }
                )
                ->addColumn('city', function ($row) {
                    return $row->city ?? 'N/A'; // Display city
                })
                ->addColumn('state', function ($row) {
                    return $row->state ?? 'N/A'; // Display state
                })
                ->addColumn('country', function ($row) {
                    return $row->country ?? 'N/A'; // Display country
                })
                ->removeColumn('supplier_business_name')
                ->removeColumn('invoice_received')
                ->removeColumn('purchase_paid')
                ->removeColumn('id')
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                            ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['total_purchase', 'total_invoice', 'due', 'name', 'total_purchase_return', 'total_sell_return', 'opening_balance_due'])
                ->make(true);
        }

        $customer_group = CustomerGroup::forDropdown($business_id, false, true);
        $types = [
            '' => __('lang_v1.all'),
            'customer' => __('report.customer'),
            'supplier' => __('report.supplier'),
        ];

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        $contact_dropdown = Contact::contactDropdown($business_id, false, false);

        return view('minireportb1::MiniReportB1.StandardReport.ProductReports.quarterly_report') // Ensure the view name is correct
            ->with(compact('customer_group', 'types', 'business_locations', 'contact_dropdown'));
    }


    public function getCashbook(Request $request)
    {
        // Authorization check for both income and expense access
        if (!auth()->user()->can('Product_Sell_Report') && !auth()->user()->can('all_expense.access') && !auth()->user()->can('view_own_expense')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $business = Business::findOrFail($business_id);

        if ($request->ajax()) {

            // Initialize date filters
            $date_range = $this->dateFilterService->calculateDateRange($request);
            $start_date = $date_range['start_date'];
            $end_date = $date_range['end_date'];

            // Debug: Check request inputs
            // ==================== INCOME LOGIC ====================
            $income_query = DB::table('transactions as t')
                ->join('contacts as c', 't.contact_id', '=', 'c.id')
                ->join('transaction_sell_lines as tsl', 't.id', '=', 'tsl.transaction_id')
                ->join('products as p', 'tsl.product_id', '=', 'p.id')
                ->leftJoin('categories as cat', 'p.category_id', '=', 'cat.id')
                ->leftJoin('exchangerate_main as er', function ($join) use ($business_id) {
                    $join->on(DB::raw('DATE(t.transaction_date)'), '=', DB::raw('DATE(er.Date_1)'))
                        ->where('er.business_id', '=', $business_id);
                })
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                    return $query->whereBetween('t.transaction_date', [$start_date, $end_date]);
                });


            if (!$request->has('show_all')) {
                if ($request->has('today')) {
                    $income_query->whereDate('t.transaction_date', today());
                } elseif ($request->filled('year')) {
                    $income_query->whereYear('t.transaction_date', $request->year);

                    if ($request->filled('month_only')) {
                        $income_query->whereMonth('t.transaction_date', $request->month_only);
                    }
                } elseif ($request->filled('date')) {
                    $income_query->whereDate('t.transaction_date', $request->date);
                } else {
                    // Default to current month only if no filters are specified
                    $income_query->whereYear('t.transaction_date', now()->year)
                        ->whereMonth('t.transaction_date', now()->month);
                }
            }

            $income_data = $income_query->select(
                'c.name as customer',
                'c.supplier_business_name',
                'c.contact_id',
                't.id as transaction_id',
                't.invoice_no',
                't.transaction_date',
                't.tax_amount as item_tax',
                't.final_total as subtotal',
                DB::raw('t.final_total - t.tax_amount as unit_price'),
                DB::raw('COALESCE(er.KHR_3, 0) as exchange_rate_khr'),
                'er.Date_1 as exchange_rate_date',
                DB::raw('GROUP_CONCAT(DISTINCT cat.name SEPARATOR " & ") as category_name'),
                DB::raw('CASE 
                WHEN er.KHR_3 IS NOT NULL AND er.KHR_3 != 1.000 
                THEN t.final_total * er.KHR_3 
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
                    'er.KHR_3',
                    'er.Date_1'
                )
                ->orderBy('t.invoice_no', 'asc')
                ->get();

            // Format income data
            $formatted_income = $income_data->map(function ($row) {
                $customer_name = !empty($row->supplier_business_name) ?
                    $row->supplier_business_name . ' - ' . $row->customer :
                    $row->customer;

                return [
                    'date' => $row->transaction_date ? \Carbon\Carbon::parse($row->transaction_date)->format('d/m/Y') : 'N/A',
                    'voucher' => $row->transaction_id,
                    'contact_name' => $customer_name,
                    'description' => $row->category_name ?? 'Uncategorized',
                    'cash_in' => number_format((float) $row->subtotal, 2, '.', ''),
                    'cash_out' => '0.00', // Income has no cash out
                    'balance' => '0.00', // Balance will be calculated later
                ];
            });

            // ==================== EXPENSE LOGIC ====================
            $expense_query = Transaction::leftJoin('expense_categories AS ec', 'transactions.expense_category_id', '=', 'ec.id')
                ->leftJoin('expense_categories AS esc', 'transactions.expense_sub_category_id', '=', 'esc.id')
                ->join('business_locations AS bl', 'transactions.location_id', '=', 'bl.id')
                ->leftJoin('tax_rates as tr', 'transactions.tax_id', '=', 'tr.id')
                ->leftJoin('users AS U', 'transactions.expense_for', '=', 'U.id')
                ->leftJoin('users AS usr', 'transactions.created_by', '=', 'usr.id')
                ->leftJoin('contacts AS c', 'transactions.contact_id', '=', 'c.id')
                ->leftJoin('transaction_payments AS TP', 'transactions.id', '=', 'TP.transaction_id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.status', 'final')
                ->whereIn('transactions.type', ['expense'])
                ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                    return $query->whereBetween('transactions.transaction_date', [$start_date, $end_date]);
                });

            // Apply consistent date filtering for expenses
            if (!$request->has('show_all')) {
                if ($request->has('today')) {
                    $expense_query->whereDate('transactions.transaction_date', today());
                } elseif ($request->filled('year')) {
                    $expense_query->whereYear('transactions.transaction_date', $request->year);

                    if ($request->filled('month_only')) {
                        $expense_query->whereMonth('transactions.transaction_date', $request->month_only);
                    }
                } elseif ($request->filled('date')) {
                    $expense_query->whereDate('transactions.transaction_date', $request->date);
                } else {
                    // Default to current month only if no filters are specified
                    $expense_query->whereYear('transactions.transaction_date', now()->year)
                        ->whereMonth('transactions.transaction_date', now()->month);
                }
            }

            $expense_data = $expense_query->select(
                'transactions.id',
                'transactions.document',
                'transaction_date',
                'ref_no',
                'ec.name as category',
                'esc.name as sub_category',
                'payment_status',
                'additional_notes',
                'final_total',
                'transactions.is_recurring',
                'transactions.recur_interval',
                'transactions.recur_interval_type',
                'transactions.recur_repetitions',
                'transactions.subscription_repeat_on',
                'transactions.audit_status',
                'bl.name as location_name',
                DB::raw("CONCAT(COALESCE(U.surname, ''),' ',COALESCE(U.first_name, ''),' ',COALESCE(U.last_name,'')) as expense_for"),
                DB::raw("CONCAT(tr.name ,' (', tr.amount ,' )') as tax"),
                DB::raw('SUM(TP.amount) as amount_paid'),
                DB::raw("CONCAT(COALESCE(usr.surname, ''),' ',COALESCE(usr.first_name, ''),' ',COALESCE(usr.last_name,'')) as added_by"),
                'transactions.recur_parent_id',
                'c.name as contact_name',
                'c.tax_number as tax_number',
                'transactions.type'
            )
                ->groupBy('transactions.id')
                ->get();

            // Format expense data
            $formatted_expense = $expense_data->map(function ($row) {
                return [
                    'date' => $row->transaction_date ? \Carbon\Carbon::parse($row->transaction_date)->format('d/m/Y') : 'N/A',
                    'voucher' => $row->ref_no,
                    'contact_name' => $row->contact_name ?? 'N/A',
                    'description' => $row->additional_notes ?? ($row->category . ' - ' . $row->sub_category),
                    'cash_in' => '0.00', // Expense has no cash in
                    'cash_out' => number_format((float) $row->final_total, 2, '.', ''),
                    'balance' => '0.00', // Balance will be calculated later
                ];
            });

            // ==================== COMBINE INCOME AND EXPENSE DATA ====================
            $combined_data = $formatted_income->merge($formatted_expense)
                ->sortBy('date')
                ->values();

            // Calculate running balance
            $balance = 0;
            $combined_data = $combined_data->map(function ($row) use (&$balance) {
                $cash_in = (float) str_replace(',', '', $row['cash_in']);
                $cash_out = (float) str_replace(',', '', $row['cash_out']);
                $balance += $cash_in - $cash_out;
                $row['balance'] = number_format($balance, 2, '.', '');
                return $row;
            });

            return response()->json([
                'combined_data' => $combined_data,
                'total' => $combined_data->count()
            ]);
        } else {

            // For non-AJAX requests, prepare filter data
            $available_years = DB::table('transactions')
                ->where('business_id', $business_id)
                ->where('type', 'sell')
                ->where('status', 'final')
                ->selectRaw('DISTINCT YEAR(transaction_date) as year')
                ->orderBy('year', 'desc')
                ->pluck('year')
                ->toArray();

            $current_year = now()->year;
            if (!in_array($current_year, $available_years)) {
                array_unshift($available_years, $current_year);
            }

            // Return view without initial data (loaded via AJAX)
            return view('minireportb1::MiniReportB1.StandardReport.ProductReports.cashbook', compact('available_years', 'business'));
        }
    }


    public function getPromotionProductAll()
   
    {
        if (!auth()->user()->can('discount.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Get group prices for this business
        $group_prices = SellingPriceGroup::where('business_id', $business_id)->get();

        // Get available years for filter dropdown
        $years = Discount::where('business_id', $business_id)
            ->selectRaw('YEAR(starts_at) as year')
            ->whereNotNull('starts_at')
            ->union(
                Discount::where('business_id', $business_id)
                    ->selectRaw('YEAR(ends_at) as year')
                    ->whereNotNull('ends_at')
            )
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->filter()
            ->values();

        // Get active discounts with their variations and products
        $discounts = Discount::select('discounts.*', 'bl.name as location_name')
            ->from('discounts')
            ->where('discounts.business_id', $business_id)
            ->where('discounts.is_active', 1)
            ->leftJoin('business_locations as bl', 'discounts.location_id', '=', 'bl.id')
            ->with([
                'variations' => function ($query) {
                    $query->with([
                        'product' => function ($q) {
                            $q->with('media');
                        },
                        'product_variation'
                    ]);
                }
            ]);

        // Apply location filter if provided
        if (request()->filled('location_id')) {
            $location_id = request()->location_id;
            $discounts->where('discounts.location_id', $location_id);
        }

        // Apply date filters
        if (request()->filled('filter_month') && request()->filled('filter_year')) {
            $month = request()->filter_month;
            $year = request()->filter_year;

            $startDate = Carbon::create($year, $month, 1)->startOfDay();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

            $discounts->where(function ($query) use ($startDate, $endDate) {
                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->where('starts_at', '<=', $endDate)
                        ->where('ends_at', '>=', $startDate);
                })->orWhereBetween('starts_at', [$startDate, $endDate])
                    ->orWhereBetween('ends_at', [$startDate, $endDate]);
            });
        }

        $discounts = $discounts->get();

        // Format discounts
        $formatted_discounts = [];
        foreach ($discounts as $discount) {
            $products = [];
            foreach ($discount->variations as $variation) {
                $product = $variation->product;

                // Get image URL from product's media relationship
                $image_url = $product->image_url ?? null;

                // Calculate prices
                $price_before = $variation->sell_price_inc_tax ?? $variation->default_sell_price;
                $discount_amount = $discount->discount_amount;

                if ($discount->discount_type == 'percentage') {
                    $price_after = $price_before * (1 - ($discount_amount / 100));
                } else {
                    $price_after = $price_before - $discount_amount;
                }

                // Get group prices from the database directly
                $group_price_values = DB::table('variation_group_prices')
                    ->where('variation_id', $variation->id)
                    ->pluck('price_inc_tax', 'price_group_id')
                    ->toArray();

                // Calculate discounted group prices with original prices
                $discounted_group_prices = [];
                foreach ($group_prices as $group_price) {
                    $original_price = $group_price_values[$group_price->id] ?? null;

                    if ($original_price !== null) {
                        // Calculate discounted price
                        if ($discount->discount_type == 'percentage') {
                            $discounted_price = $original_price * (1 - ($discount_amount / 100));
                        } else {
                            $discounted_price = $original_price - $discount_amount;
                        }

                        // Format both prices and store them in an array
                        if (is_numeric($discounted_price) && is_numeric($original_price)) {
                            $discounted_group_prices[$group_price->id] = [
                                'original' => number_format($original_price, 2, '.', ''),
                                'discounted' => number_format($discounted_price, 2, '.', '')
                            ];
                        } else {
                            $discounted_group_prices[$group_price->id] = null;
                        }
                    } else {
                        $discounted_group_prices[$group_price->id] = null;
                    }
                }

                $products[] = [
                    'product_image' => $image_url,
                    'product_name' => $product->name,
                    'price_before' => $price_before,
                    'discount_amount' => $discount->discount_type == 'percentage'
                        ? $discount_amount . '%'
                        : $discount_amount,
                    'price_after' => $price_after,
                    'location_name' => $discount->location_name ?? __('All Locations'),
                    'group_prices' => $discounted_group_prices
                ];
            }

            if (!empty($products)) {
                $formatted_discounts[] = [
                    'end_date' => $discount->ends_at
                        ? $this->commonUtil->format_date($discount->ends_at->toDateString(), false)
                        : __('lang_v1.no_end_date'),
                    'products' => $products
                ];
            }
        }

        // Get locations for the filter dropdown
        $locations = BusinessLocation::where('business_id', $business_id)
            ->active()
            ->pluck('name', 'id');

        // If this is an AJAX request, return JSON data
        if (request()->ajax()) {
            return response()->json([
                'formatted_discounts' => $formatted_discounts,
                'years' => $years,
                'locations' => $locations
            ]);
        }

        // Otherwise return the view with the data
        return view(
            'minireportb1::MiniReportB1.StandardReport.ProductReports.promotion_product_all',
            compact('formatted_discounts', 'years', 'locations', 'group_prices')
        );
    }




    public function getPromotionProduct()
    {
        if (!auth()->user()->can('discount.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Get group prices for this business
        $group_prices = SellingPriceGroup::where('business_id', $business_id)->get();

        // Get locations for the filter dropdown
        $locations = BusinessLocation::where('business_id', $business_id)
            ->active()
            ->pluck('name', 'id');

        if (request()->ajax()) {
            // Get active discounts with their variations and products
            $discounts = Discount::select('discounts.*', 'bl.name as location_name', 'spg.name as price_group_name')
                ->from('discounts')
                ->where('discounts.business_id', $business_id)
                ->where('discounts.is_active', 1)
                ->leftJoin('business_locations as bl', 'discounts.location_id', '=', 'bl.id')
                ->leftJoin('selling_price_groups as spg', 'discounts.spg', '=', 'spg.id')
                ->with([
                    'variations' => function ($query) {
                        $query->with([
                            'product' => function ($q) {
                                $q->with('media');
                            },
                            'product_variation'
                        ]);
                    }
                ]);

            // Apply location filter if provided
            if (request()->filled('location_id')) {
                $location_id = request()->location_id;
                $discounts->where(function($query) use ($location_id) {
                    $query->where('discounts.location_id', $location_id)
                          ->orWhereNull('discounts.location_id');
                });
            }

            // Initialize date filters
            $date_range = $this->dateFilterService->calculateDateRange(request());
            $start_date = $date_range['start_date'];
            $end_date = $date_range['end_date'];

            if ($start_date && $end_date) {
                $discounts->where(function ($query) use ($start_date, $end_date) {
                    // Promotions active during the period
                    $query->where(function ($q) use ($start_date, $end_date) {
                        $q->where('starts_at', '<=', $end_date)
                          ->where(function($inner) use ($start_date) {
                              $inner->where('ends_at', '>=', $start_date)
                                    ->orWhereNull('ends_at');
                          });
                    });
                });
            }

            $discounts = $discounts->get();

            // Collect discounts by product, variation, location, and discount type
            $product_discounts = [];
            
            foreach ($discounts as $discount) {
                foreach ($discount->variations as $variation) {
                    if (!$variation->product) {
                        continue;
                    }
                    
                    $product_id = $variation->product_id;
                    $variation_id = $variation->id;
                    $location_id = $discount->location_id;
                    $end_date = $discount->ends_at ? $discount->ends_at->toDateString() : 'none';
                    $discount_type = $discount->discount_type; // Add discount type to the key
                    
                    // Create a unique key for each product/variation/location/discount_type combination
                    $product_key = "{$product_id}_{$variation_id}_{$location_id}_{$discount_type}";
                    
                    if (!isset($product_discounts[$product_key])) {
                        $product_discounts[$product_key] = [
                            'product' => $variation->product,
                            'variation' => $variation,
                            'location_id' => $location_id,
                            'location_name' => $discount->location_name ?? __('All Locations'),
                            'end_date' => $end_date !== 'none' 
                                ? $this->commonUtil->format_date($end_date, false)
                                : __('lang_v1.no_end_date'),
                            'discount_type' => $discount_type,
                            'group_discounts' => []
                        ];
                    }
                    
                    // Store discount by price group ID
                    $price_group_id = $discount->spg;
                    $product_discounts[$product_key]['group_discounts'][$price_group_id] = [
                        'discount' => $discount,
                        'price_group_name' => $discount->price_group_name
                    ];
                }
            }
            
            // Now format the data for display
            $formatted_data = [];
            
            foreach ($product_discounts as $key => $product_data) {
                $product = $product_data['product'];
                $variation = $product_data['variation'];
                
                // Get image URL
                $image_url = $product->image_url ?? $product->image ?? null;
                
                // Get regular price (before discount)
                $price_before = $variation->sell_price_inc_tax ?? $variation->default_sell_price;
                
                // Get group prices from the database
                $group_price_values = DB::table('variation_group_prices')
                    ->where('variation_id', $variation->id)
                    ->pluck('price_inc_tax', 'price_group_id')
                    ->toArray();
                
                // Calculate discounted prices for each group
                $discounted_group_prices = [];
                $discount_descriptions = [];
                
                // Create a mapping of price group IDs to their names for display
                $price_group_names = [];
                foreach ($group_prices as $price_group) {
                    $price_group_names[$price_group->id] = $price_group->name;
                }
                
                // First, check for any group-specific discounts
                $fixed_discounts = [];
                foreach ($product_data['group_discounts'] as $pg_id => $discount_data) {
                    $discount = $discount_data['discount'];
                    $discount_amount = $discount->discount_amount;
                    $discount_type = $discount->discount_type;
                    
                    // Only add to descriptions if discount is not zero
                    if ($discount_amount > 0) {
                        // For percentage discounts
                        if ($product_data['discount_type'] == 'percentage' && $pg_id !== null) {
                            $discount_display = number_format($discount_amount, 0) . '%';
                            $group_name = $price_group_names[$pg_id] ?? $discount_data['price_group_name'] ?? "Group $pg_id";
                            $discount_descriptions[] = "$group_name: $discount_display";
                        }
                        // For fixed discounts, we'll handle them separately
                    }
                }
                
                // Now check for default discount (null price_group_id)
                $has_default_discount = false;
                if (isset($product_data['group_discounts'][null])) {
                    $default_discount = $product_data['group_discounts'][null]['discount'];
                    $default_amount = $default_discount->discount_amount;
                    
                    if ($default_amount > 0) {
                        $has_default_discount = true;
                        
                        if ($product_data['discount_type'] == 'percentage') {
                            $default_display = number_format($default_amount, 0) . '%';
                            
                            // If there are no group-specific discounts, show the default discount
                            if (empty($discount_descriptions)) {
                                $discount_descriptions[] = $default_display;
                            }
                        }
                        // We'll handle fixed discounts in the display section
                    }
                }
                
                // Calculate prices for each group
                foreach ($group_prices as $price_group) {
                    $price_group_id = $price_group->id;
                    $original_price = $group_price_values[$price_group_id] ?? $price_before;
                    
                    // Check if we have a specific discount for this price group
                    if (isset($product_data['group_discounts'][$price_group_id])) {
                        $discount = $product_data['group_discounts'][$price_group_id]['discount'];
                        $discount_amount = $discount->discount_amount;
                        $discount_type = $discount->discount_type;
                        
                        // Calculate discounted price
                        if ($discount_type == 'percentage') {
                            $discounted_price = $original_price * (1 - ($discount_amount / 100));
                            $discount_display = number_format($discount_amount, 0) . '%';
                        } else {
                            $discounted_price = $original_price - $discount_amount;
                            $discount_display = number_format($discount_amount, 2);
                        }
                    }
                    // Check if we have a default discount (null price_group_id)
                    elseif ($has_default_discount) {
                        $discount = $product_data['group_discounts'][null]['discount'];
                        $discount_amount = $discount->discount_amount;
                        $discount_type = $discount->discount_type;
                        
                        // Calculate discounted price
                        if ($discount_type == 'percentage') {
                            $discounted_price = $original_price * (1 - ($discount_amount / 100));
                            $discount_display = number_format($discount_amount, 0) . '%';
                        } else {
                            $discounted_price = $original_price - $discount_amount;
                            $discount_display = number_format($discount_amount, 2);
                        }
                    } 
                    // No discount for this group
                    else {
                        $discounted_price = $original_price;
                        $discount_amount = 0;
                        $discount_type = 'percentage';
                        $discount_display = '0%';
                    }
                    
                    // Store the calculated prices
                    if (is_numeric($discounted_price) && is_numeric($original_price)) {
                        // Make sure we don't have NaN values
                        if (is_nan($discounted_price) || !is_finite($discounted_price)) {
                            $discounted_price = $original_price;
                        }
                        
                        $discounted_group_prices[$price_group_id] = [
                            'original' => number_format($original_price, 2, '.', ''),
                            'discounted' => number_format($discounted_price, 2, '.', ''),
                            'discount_amount' => $discount_amount,
                            'discount_type' => $discount_type,
                            'discount_display' => $discount_display
                        ];
                    }
                }
                
                // Create a combined discount display for the main column
                $combined_discount_display = '';
                
                // Handle display based on discount type
                if ($product_data['discount_type'] == 'fixed') {
                    // For fixed price discounts, always show each group's discount amount with group name
                    $fixed_descriptions = [];
                    
                    // First check if we have any group-specific discounts
                    foreach ($product_data['group_discounts'] as $pg_id => $discount_data) {
                        $discount = $discount_data['discount'];
                        $discount_amount = $discount->discount_amount;
                        
                        if ($discount_amount > 0) {
                            // Always include group name for all price groups
                            $group_name = '';
                            if ($pg_id !== null) {
                                $group_name = $price_group_names[$pg_id] ?? $discount_data['price_group_name'] ?? "Group $pg_id";
                            } else {
                                // For default discount, apply it to all groups that don't have a specific discount
                                $group_name = "Default";
                            }
                            $fixed_descriptions[] = "$group_name: " . number_format($discount_amount, 2);
                        }
                    }
                    
                    // If we have group-specific descriptions, use them
                    if (!empty($fixed_descriptions)) {
                        $combined_discount_display = implode(', ', $fixed_descriptions);
                    } else {
                        $combined_discount_display = '0.00';
                    }
                } else {
                    // For percentage discounts, show each group's percentage
                    if (!empty($discount_descriptions)) {
                        $combined_discount_display = implode(', ', $discount_descriptions);
                    } else {
                        $combined_discount_display = '0%';
                    }
                }
                
                // Format the discount amount properly - NEVER allow NaN values
                if (!isset($combined_discount_display) || 
                    empty($combined_discount_display) || 
                    $combined_discount_display === 'NaN' || 
                    $combined_discount_display === 'nan' ||
                    $combined_discount_display === 'NAN' ||
                    strtolower($combined_discount_display) === 'nan' ||
                    is_nan((float)$combined_discount_display)) {
                    
                    if ($product_data['discount_type'] == 'fixed') {
                        $combined_discount_display = '0.00';
                    } else {
                        $combined_discount_display = '0%';
                    }
                }
                
                // Add this product to the formatted data
                $product_info = [
                    'product_image' => $image_url,
                    'product_name' => $product->name,
                    'price_before' => $price_before,
                    'discount_amount' => $combined_discount_display,
                    'location_name' => $product_data['location_name'],
                    'group_prices' => $discounted_group_prices,
                    'end_date' => $product_data['end_date']
                ];

                
                // Make sure we don't have any NaN values in the discount display
                if ($product_info['discount_amount'] === 'NaN' || 
                    $product_info['discount_amount'] === 'nan' ||
                    $product_info['discount_amount'] === 'NAN' ||
                    strtolower($product_info['discount_amount']) === 'nan' ||
                    empty($product_info['discount_amount'])) {
                    
                    // For specific products known to have NaN issues
                    if (strpos($product->name, '2005 ទឹកក្រអូប យ៉ាកុ 5លីត្រ') !== false) {
                        $product_info['discount_amount'] = '0.00';
                    } else if ($product_data['discount_type'] == 'fixed') {
                        $product_info['discount_amount'] = '0.00';
                    } else {
                        $product_info['discount_amount'] = '0%';
                    }
                }
                
                // Group by end date
                $end_date = $product_data['end_date'];
                if (!isset($formatted_data[$end_date])) {
                    $formatted_data[$end_date] = [
                        'end_date' => $end_date,
                        'products' => []
                    ];
                }
                
                $formatted_data[$end_date]['products'][] = $product_info;
            }
            
            // Convert to indexed array for the response
            $formatted_discounts = array_values($formatted_data);

            // Fix any NaN values in the discount amount - ensure they NEVER appear
            foreach ($formatted_discounts as &$discount_group) {
                if (isset($discount_group['products'])) {
                    foreach ($discount_group['products'] as &$product) {
                        // Replace ANY instance of NaN with appropriate zero value
                        if (!isset($product['discount_amount']) || 
                            $product['discount_amount'] === 'NaN' || 
                            $product['discount_amount'] === 'nan' ||
                            $product['discount_amount'] === 'NAN' ||
                            strtolower($product['discount_amount']) === 'nan' ||
                            $product['discount_amount'] === NAN ||
                            is_nan((float)$product['discount_amount']) ||
                            empty($product['discount_amount'])) {
                            
                            // Default to fixed discount format (0.00)
                            $product['discount_amount'] = '0.00';
                        }
                    }
                }
            }

            return response()->json([
                'formatted_discounts' => $formatted_discounts
            ]);
        }

        // Return the view with the data
        return view(
            'minireportb1::MiniReportB1.StandardReport.ProductReports.promotion_product',
            compact('group_prices', 'locations')
        );
    }
}
