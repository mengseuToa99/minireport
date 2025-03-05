<?php

namespace Modules\MiniReportB1\Http\Controllers;


use App\Brands;
use App\Product;
use App\Variation;
use App\Transaction;
use App\BusinessLocation;
use App\Category;
use App\Discount;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\VariationLocationDetails;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ProductController;
use App\PurchaseLine;
use App\SellingPriceGroup;
use App\StockAdjustmentLine;
use App\TransactionSellLine;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\MiniReportB1\Http\Services\DateFilterService;



class ProductHController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $productUtil;

    protected $moduleUtil;

    protected $productByGroupPrice;
    protected $commonUtil;
    protected $dateFilterService;


    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, ModuleUtil $moduleUtil, ProductController $productByGroupPrice, Util $commonUtil , DateFilterService $dateFilterService)
    {
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->productByGroupPrice = $productByGroupPrice;
        $this->commonUtil = $commonUtil;
        $this->dateFilterService = $dateFilterService;
    }



    public function getMonthlyStockReport($business_id, $location_id, $month, $year)
    {
        $startDate = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $previousMonthEnd = $startDate->copy()->subMonth()->endOfMonth();
    
        // Fetch products with variations and selling prices
        $products = Product::where('business_id', $business_id)
            ->with(['variations', 'variations.product_variation', 'category'])
            ->get();
    
        $reportData = [];
    
        foreach ($products as $product) {
            $productData = [
                'product_name' => $product->name,
                'sku' => $product->sku,
                'category_id' => $product->category->id ?? null,
                'category_name' => $product->category->name ?? __('lang_v1.uncategorized'),
                'selling_price' => 0,
                'opening_stock' => 0,
                'total_purchases' => 0,
                'total_sales' => 0,
                'total_adjustments' => 0,
                'total_transfers_in' => 0,
                'total_transfers_out' => 0,
                'total_production_purchases' => 0, // Add this
                'total_production_sales' => 0, // Add this
                'final_stock' => 0,
                'product_link' => ''
            ];
    
            if (auth()->user()->can('product.view')) {
                $productData['product_link'] = action([\App\Http\Controllers\ProductController::class, 'productStockHistory'], [$product->id]);
            }
    
            foreach ($product->variations as $variation) {
                // Get selling price for the variation
                $sellingPrice = $variation->sell_price_inc_tax ?? $variation->default_sell_price;
                $productData['selling_price'] = $sellingPrice;
    
                // Get opening stock
                $openingStock = $this->getVariationStockUpToDate(
                    $business_id,
                    $variation->id,
                    $location_id,
                    $previousMonthEnd
                );
                
                $productData['opening_stock'] += $openingStock;
    
                // Get current month transactions
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
                $productData['total_production_purchases'] += $transactions['total_production_purchases']; // Add this
                $productData['total_production_sales'] += $transactions['total_production_sales']; // Add this
            }
    
            // Calculate final stock
            $productData['final_stock'] = $productData['opening_stock']
                + $productData['total_purchases']
                + $productData['total_transfers_in']
                + $productData['total_production_purchases'] // Add this
                - $productData['total_sales']
                - $productData['total_adjustments']
                - $productData['total_transfers_out']
                - $productData['total_production_sales']; // Add this
    
            // Group by category
            $categoryKey = $productData['category_id'] ?? 'uncategorized';
            if (!isset($reportData[$categoryKey])) {
                $reportData[$categoryKey] = [
                    'category_id' => $productData['category_id'],
                    'category_name' => $productData['category_name'],
                    'products' => [],
                ];
            }
            $reportData[$categoryKey]['products'][] = $productData;
        }
    
        return array_values($reportData);
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

        return view('minireportb1::MiniReportB1.card-menu.products.monthly_product')
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
            ->with(['variations' => function ($query) {
                $query->with(['product' => function ($q) {
                    $q->with('media');
                }, 'product_variation']);
            }]);

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

        return view(
            'minireportb1::MiniReportB1.card-menu.products.promotion_product_all',
            compact('formatted_discounts', 'years', 'locations', 'group_prices')
        );
    }

    public function getPromotionProduct(Request $request)
    {
        if (!auth()->user()->can('discount.access')) {
            abort(403, 'Unauthorized action.');
        }
    
        $business_id = request()->session()->get('user.business_id');
    
        // Get group prices for this business
        $group_prices = SellingPriceGroup::where('business_id', $business_id)->get();
    
        // Get date range from the request
        $date_range = $this->dateFilterService->calculateDateRange($request);
        $start_date = $date_range['start_date'];
        $end_date = $date_range['end_date'];
    
        // Get active discounts with their variations and products
        $discounts = Discount::select('discounts.*', 'bl.name as location_name')
            ->from('discounts')
            ->where('discounts.business_id', $business_id)
            ->where('discounts.is_active', 1)
            ->leftJoin('business_locations as bl', 'discounts.location_id', '=', 'bl.id')
            ->with(['variations' => function ($query) {
                $query->with(['product' => function ($q) {
                    $q->with('media');
                }, 'product_variation']);
            }]);
    
        // Apply location filter if provided
        if (request()->filled('location_id')) {
            $location_id = request()->location_id;
            $discounts->where('discounts.location_id', $location_id);
        }
    
        // Apply date filters using ->when()
        $discounts->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
            return $query->where(function ($q) use ($start_date, $end_date) {
                $q->whereBetween('starts_at', [$start_date, $end_date])
                    ->orWhereBetween('ends_at', [$start_date, $end_date])
                    ->orWhere(function ($q2) use ($start_date, $end_date) {
                        $q2->where('starts_at', '<=', $end_date)
                            ->where('ends_at', '>=', $start_date);
                    });
            });
        });
    
        // Remove the old date filter block since it's replaced by ->when()
        /*
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
        */
    
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
    
                // Apply discount to each group price
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
    
                        // Ensure $discounted_price is a numeric value before formatting
                        if (is_numeric($discounted_price)) {
                            $discounted_group_prices[$group_price->id] = number_format($discounted_price, 2, '.', '');
                        } else {
                            $discounted_group_prices[$group_price->id] = null; // Handle non-numeric values gracefully
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
    
        return view(
            'minireportb1::MiniReportB1.card-menu.products.promotion_product',
            compact('formatted_discounts', 'locations', 'group_prices')
        );
    }

    public function ProductByGroupPrice()
    {
        // Fetch all group prices for the dropdown
        $group_prices = SellingPriceGroup::where('business_id', auth()->user()->business_id)->get();

        // Fetch the selected group price (if any)
        $selected_group_price = null;
        if (request('group_price')) {
            $selected_group_price = SellingPriceGroup::find(request('group_price'));
        }

        // Fetch products with their group price values
        $products = Product::with(['category', 'variations'])
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('variations', 'products.id', '=', 'variations.product_id')
            ->leftJoin('variation_group_prices', function ($join) {
                $join->on('variations.id', '=', 'variation_group_prices.variation_id')
                    ->where('variation_group_prices.price_group_id', request('group_price'));
            })
            ->where('products.business_id', auth()->user()->business_id)
            ->select(
                'products.id',
                'products.name as product_name', // Ensure product name is selected
                'categories.name as category_name',
                'variations.sell_price_inc_tax as selling_price',
                'products.sku',
                'variation_group_prices.price_inc_tax as group_price_value',
                'categories.id as category_id'
            )
            ->get();

        // Fetch all categories for the filter (using forDropdown)
        $categories = Category::forDropdown(auth()->user()->business_id, 'product');

        // Pass data to the view
        return view('minireportb1::MiniReportB1.card-menu.products.productByGroupPrice', compact('products', 'group_prices', 'selected_group_price', 'categories'));
    }

    public function ProductByGroupPriceAll()
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
        $query = Product::with(['media', 'category', 'variations'])
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->join('units', 'products.unit_id', '=', 'units.id')
            ->leftJoin('categories as c1', 'products.category_id', '=', 'c1.id') // Use leftJoin for categories
            ->leftJoin('categories as c2', 'products.sub_category_id', '=', 'c2.id') // Use leftJoin for subcategories
            ->leftJoin('tax_rates', 'products.tax', '=', 'tax_rates.id')
            ->join('variations', 'variations.product_id', '=', 'products.id') // Join variations table
            ->leftJoin('variation_group_prices', function ($join) {
                $join->on('variation_group_prices.variation_id', '=', 'variations.id');
            })
            ->whereNull('variations.deleted_at')
            ->where('products.business_id', $business_id)
            ->where('products.type', '!=', 'modifier');

        // Fetch products with their details
        $products = $query->select(
            'products.id',
            'products.name as product_name', // Ensure this is selected as "product_name"
            'products.type',
            'c1.name as category',
            'c2.name as sub_category',
            'products.category_id', // Ensure category_id is selected
            'units.actual_name as unit',
            'brands.name as brand',
            'tax_rates.name as tax',
            'products.sku',
            'products.image',
            'products.enable_stock',
            'products.is_inactive',
            'products.not_for_selling',
            DB::raw('MAX(variations.dpp_inc_tax) as max_purchase_price'), // Max purchase price
            DB::raw('MIN(variations.dpp_inc_tax) as min_purchase_price'), // Min purchase price
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
                'units.actual_name',
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
        foreach ($products as $product) {
            if (!isset($grouped_products[$product->id])) {
                $grouped_products[$product->id] = [
                    'id' => $product->id, // Ensure the 'id' key is set
                    'product_name' => $product->product_name,
                    'category_name' => $product->category,
                    'sub_category_name' => $product->sub_category,
                    'category_id' => $product->category_id ?? null, // Ensure category_id is always set
                    'sku' => $product->sku,
                    'unit' => $product->unit,
                    'brand' => $product->brand,
                    'tax' => $product->tax,
                    'image' => $product->image,
                    'enable_stock' => $product->enable_stock,
                    'is_inactive' => $product->is_inactive,
                    'not_for_selling' => $product->not_for_selling,
                    'min_purchase_price' => $product->min_purchase_price ?? 0, // Min purchase price
                    'max_purchase_price' => $product->max_purchase_price ?? 0, // Max purchase price
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
            $product_name = $product['product_name']; // Ensure this key exists

            if (!isset($products_by_category[$category_id])) {
                $products_by_category[$category_id] = [];
            }

            // Add product to its category
            $products_by_category[$category_id][] = $product;
        }

        // Pass data to the view
        return view('minireportb1::MiniReportB1.card-menu.products.productByGroupPriceAll', compact('group_prices', 'categories', 'grouped_products', 'products_by_category'));
    }

    public function StockHistory($id)
    {

        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $start_date = request()->input('start_date');
            $end_date = request()->input('end_date');
            $location_id = request()->input('location_id');

            // Get stock details
            $stock_details = $this->getVariationStockDetails($business_id, $id, $location_id, $start_date, $end_date);


            // Get stock history with date range filtering
            $stock_history = $this->getVariationStockHistory($business_id, $id, $location_id, $start_date, $end_date);

            return view('quickbooks::products.view_test')
                ->with(compact('stock_details', 'stock_history'));
        }

        $product = Product::where('business_id', $business_id)
            ->with(['variations', 'variations.product_variation'])
            ->findOrFail($id);

        $business_locations = BusinessLocation::forDropdown($business_id);

        return view('quickbooks::products.view_test')
            ->with(compact('product', 'business_locations'));
    }


    public function getVariationStockDetails($business_id, $variation_id, $location_id, $start_date = null, $end_date = null)
    {
        // Fetch purchase details with optional date filters
        $purchase_details = Variation::join('products as p', 'p.id', '=', 'variations.product_id')
            ->join('units', 'p.unit_id', '=', 'units.id')
            ->leftjoin('units as u', 'p.secondary_unit_id', '=', 'u.id')
            ->leftjoin('product_variations as pv', 'variations.product_variation_id', '=', 'pv.id')
            ->leftjoin('purchase_lines as pl', 'pl.variation_id', '=', 'variations.id')
            ->leftjoin('transactions as t', 'pl.transaction_id', '=', 't.id')
            ->where('t.location_id', $location_id)
            ->where('p.business_id', $business_id)
            ->where('variations.id', $variation_id)
            ->when($start_date, function ($query, $start_date) {
                $query->whereDate('t.transaction_date', '>=', $start_date);
            })
            ->when($end_date, function ($query, $end_date) {
                $query->whereDate('t.transaction_date', '<=', $end_date);
            })
            ->select(
                DB::raw("SUM(IF(t.type='purchase' AND t.status='received', pl.quantity, 0)) as total_purchase"),
                DB::raw("SUM(IF(t.type='purchase' OR t.type='purchase_return', pl.quantity_returned, 0)) as total_purchase_return"),
                DB::raw('SUM(pl.quantity_adjusted) as total_adjusted'),
                DB::raw("SUM(IF(t.type='opening_stock', pl.quantity, 0)) as total_opening_stock"),
                DB::raw("SUM(IF(t.type='purchase_transfer', pl.quantity, 0)) as total_purchase_transfer"),
                'variations.sub_sku as sub_sku',
                'p.name as product',
                'p.type',
                'p.sku',
                'p.id as product_id',
                'units.short_name as unit',
                'u.short_name as second_unit',
                'pv.name as product_variation',
                'variations.name as variation_name',
                'variations.id as variation_id'
            )
            ->first();

        // Fetch sell details with optional date filters
        $sell_details = Variation::join('products as p', 'p.id', '=', 'variations.product_id')
            ->leftjoin('transaction_sell_lines as sl', 'sl.variation_id', '=', 'variations.id')
            ->join('transactions as t', 'sl.transaction_id', '=', 't.id')
            ->where('t.location_id', $location_id)
            ->where('t.status', 'final')
            ->where('p.business_id', $business_id)
            ->where('variations.id', $variation_id)
            ->when($start_date, function ($query, $start_date) {
                $query->whereDate('t.transaction_date', '>=', $start_date);
            })
            ->when($end_date, function ($query, $end_date) {
                $query->whereDate('t.transaction_date', '<=', $end_date);
            })
            ->select(
                DB::raw("SUM(IF(t.type='sell', sl.quantity, 0)) as total_sold"),
                DB::raw("SUM(IF(t.type='sell', sl.quantity_returned, 0)) as total_sell_return"),
                DB::raw("SUM(IF(t.type='sell_transfer', sl.quantity, 0)) as total_sell_transfer")
            )
            ->first();

        // Fetch current stock from VariationLocationDetails (unchanged)
        $current_stock = VariationLocationDetails::where('variation_id', $variation_id)
            ->where('location_id', $location_id)
            ->first();

        // Build the product name
        if ($purchase_details->type == 'variable') {
            $product_name = $purchase_details->product . ' - ' . $purchase_details->product_variation . ' - ' . $purchase_details->variation_name . ' (' . $purchase_details->sub_sku . ')';
        } else {
            $product_name = $purchase_details->product . ' (' . $purchase_details->sku . ')';
        }

        // Return the output in the original format
        $output = [
            'variation' => $product_name,
            'unit' => $purchase_details->unit,
            'second_unit' => $purchase_details->second_unit,
            'total_purchase' => $purchase_details->total_purchase,
            'total_purchase_return' => $purchase_details->total_purchase_return,
            'total_adjusted' => $purchase_details->total_adjusted,
            'total_opening_stock' => $purchase_details->total_opening_stock,
            'total_purchase_transfer' => $purchase_details->total_purchase_transfer,
            'total_sold' => $sell_details->total_sold,
            'total_sell_return' => $sell_details->total_sell_return,
            'total_sell_transfer' => $sell_details->total_sell_transfer,
            'current_stock' => $current_stock->qty_available ?? 0,
        ];

        return $output;
    }


    public function getVariationStockHistory($business_id, $variation_id, $location_id, $start_date = null, $end_date = null)
    {
        $stock_history = Transaction::leftjoin('transaction_sell_lines as sl', 'sl.transaction_id', '=', 'transactions.id')
            ->leftjoin('purchase_lines as pl', 'pl.transaction_id', '=', 'transactions.id')
            ->leftjoin('stock_adjustment_lines as al', 'al.transaction_id', '=', 'transactions.id')
            ->leftjoin('transactions as return', 'transactions.return_parent_id', '=', 'return.id')
            ->leftjoin('purchase_lines as rpl', 'rpl.transaction_id', '=', 'return.id')
            ->leftjoin('transaction_sell_lines as rsl', 'rsl.transaction_id', '=', 'return.id')
            ->leftjoin('contacts as c', 'transactions.contact_id', '=', 'c.id')
            ->where('transactions.location_id', $location_id)
            ->where(function ($q) use ($variation_id) {
                $q->where('sl.variation_id', $variation_id)
                    ->orWhere('pl.variation_id', $variation_id)
                    ->orWhere('al.variation_id', $variation_id)
                    ->orWhere('rpl.variation_id', $variation_id)
                    ->orWhere('rsl.variation_id', $variation_id);
            })
            ->whereIn('transactions.type', [
                'sell',
                'purchase',
                'stock_adjustment',
                'opening_stock',
                'sell_transfer',
                'purchase_transfer',
                'production_purchase',
                'purchase_return',
                'sell_return',
                'production_sell',
            ])
            // Apply the date filter conditions
            ->when($start_date, function ($query, $start_date) {
                $query->whereDate('transactions.transaction_date', '>=', $start_date);
            })
            ->when($end_date, function ($query, $end_date) {
                $query->whereDate('transactions.transaction_date', '<=', $end_date);
            })
            ->select(
                'transactions.id as transaction_id',
                'transactions.type as transaction_type',
                'sl.quantity as sell_line_quantity',
                'pl.quantity as purchase_line_quantity',
                'rsl.quantity_returned as sell_return',
                'rpl.quantity_returned as purchase_return',
                'al.quantity as stock_adjusted',
                'pl.quantity_returned as combined_purchase_return',
                'transactions.return_parent_id',
                'transactions.transaction_date',
                'transactions.status',
                'transactions.invoice_no',
                'transactions.ref_no',
                'transactions.additional_notes',
                'c.name as contact_name',
                'c.supplier_business_name',
                'pl.secondary_unit_quantity as purchase_secondary_unit_quantity',
                'sl.secondary_unit_quantity as sell_secondary_unit_quantity'
            )
            ->orderBy('transactions.transaction_date', 'asc')
            ->get();

        $stock_history_array = [];
        $stock = 0;
        $stock_in_second_unit = 0;

        foreach ($stock_history as $stock_line) {
            $temp_array = [
                'date' => $stock_line->transaction_date,
                'transaction_id' => $stock_line->transaction_id,
                'contact_name' => $stock_line->contact_name,
                'supplier_business_name' => $stock_line->supplier_business_name,
            ];

            if ($stock_line->transaction_type == 'sell') {
                if ($stock_line->status != 'final') {
                    continue;
                }
                $quantity_change = -1 * $stock_line->sell_line_quantity;
                $stock += $quantity_change;
                $stock_in_second_unit -= $stock_line->sell_secondary_unit_quantity;
                $stock_history_array[] = array_merge($temp_array, [
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'sell',
                    'type_label' => __('sale.sale'),
                    'ref_no' => $stock_line->invoice_no,
                    'sell_secondary_unit_quantity' => !empty($stock_line->sell_secondary_unit_quantity) ? $this->roundQuantity($stock_line->sell_secondary_unit_quantity) : 0,
                    'stock_in_second_unit' => $this->roundQuantity($stock_in_second_unit),
                ]);
            } elseif ($stock_line->transaction_type == 'purchase') {
                if ($stock_line->status != 'received') {
                    continue;
                }
                $quantity_change = $stock_line->purchase_line_quantity;
                $stock += $quantity_change;
                $stock_in_second_unit += $stock_line->purchase_secondary_unit_quantity;
                $stock_history_array[] = array_merge($temp_array, [
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'purchase',
                    'type_label' => __('lang_v1.purchase'),
                    'ref_no' => $stock_line->ref_no,
                    'purchase_secondary_unit_quantity' => !empty($stock_line->purchase_secondary_unit_quantity) ? $this->roundQuantity($stock_line->purchase_secondary_unit_quantity) : 0,
                    'stock_in_second_unit' => $this->roundQuantity($stock_in_second_unit),
                ]);
            } elseif ($stock_line->transaction_type == 'stock_adjustment') {
                $quantity_change = -1 * $stock_line->stock_adjusted;
                $stock += $quantity_change;
                $stock_history_array[] = array_merge($temp_array, [
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'stock_adjustment',
                    'type_label' => __('stock_adjustment.stock_adjustment'),
                    'ref_no' => $stock_line->ref_no,
                    'stock_in_second_unit' => $this->roundQuantity($stock_in_second_unit),
                ]);
            } elseif ($stock_line->transaction_type == 'opening_stock') {
                $quantity_change = $stock_line->purchase_line_quantity;
                $stock += $quantity_change;
                $stock_in_second_unit += $stock_line->purchase_secondary_unit_quantity;
                $stock_history_array[] = array_merge($temp_array, [
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'opening_stock',
                    'type_label' => __('report.opening_stock'),
                    'ref_no' => $stock_line->ref_no ?? '',
                    'additional_notes' => $stock_line->additional_notes,
                    'purchase_secondary_unit_quantity' => !empty($stock_line->purchase_secondary_unit_quantity) ? $this->roundQuantity($stock_line->purchase_secondary_unit_quantity) : 0,
                    'stock_in_second_unit' => $this->roundQuantity($stock_in_second_unit),
                ]);
            } elseif ($stock_line->transaction_type == 'sell_transfer') {
                if ($stock_line->status != 'final') {
                    continue;
                }
                $quantity_change = -1 * $stock_line->sell_line_quantity;
                $stock += $quantity_change;
                $stock_history_array[] = array_merge($temp_array, [
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'sell_transfer',
                    'type_label' => __('lang_v1.stock_transfers') . ' (' . __('lang_v1.out') . ')',
                    'ref_no' => $stock_line->ref_no,
                    'stock_in_second_unit' => $this->roundQuantity($stock_in_second_unit),
                ]);
            } elseif ($stock_line->transaction_type == 'purchase_transfer') {
                if ($stock_line->status != 'received') {
                    continue;
                }

                $quantity_change = $stock_line->purchase_line_quantity;
                $stock += $quantity_change;
                $stock_history_array[] = array_merge($temp_array, [
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'purchase_transfer',
                    'type_label' => __('lang_v1.stock_transfers') . ' (' . __('lang_v1.in') . ')',
                    'ref_no' => $stock_line->ref_no,
                    'stock_in_second_unit' => $this->roundQuantity($stock_in_second_unit),
                ]);
            } elseif ($stock_line->transaction_type == 'production_sell') {
                if ($stock_line->status != 'final') {
                    continue;
                }
                $quantity_change = -1 * $stock_line->sell_line_quantity;
                $stock += $quantity_change;
                $stock_history_array[] = array_merge($temp_array, [
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'sell',
                    'type_label' => __('manufacturing::lang.ingredient'),
                    'ref_no' => '',
                    'stock_in_second_unit' => $this->roundQuantity($stock_in_second_unit),
                ]);
            } elseif ($stock_line->transaction_type == 'production_purchase') {
                $quantity_change = $stock_line->purchase_line_quantity;
                $stock += $quantity_change;
                $stock_history_array[] = array_merge($temp_array, [
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'production_purchase',
                    'type_label' => __('manufacturing::lang.manufactured'),
                    'ref_no' => $stock_line->ref_no,
                    'stock_in_second_unit' => $this->roundQuantity($stock_in_second_unit),
                ]);
            } elseif ($stock_line->transaction_type == 'purchase_return') {
                $quantity_change = -1 * ($stock_line->combined_purchase_return + $stock_line->purchase_return);
                $stock += $quantity_change;
                $stock_history_array[] = array_merge($temp_array, [
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'purchase_return',
                    'type_label' => __('lang_v1.purchase_return'),
                    'ref_no' => $stock_line->ref_no,
                    'stock_in_second_unit' => $this->roundQuantity($stock_in_second_unit),
                ]);
            } elseif ($stock_line->transaction_type == 'sell_return') {
                $quantity_change = $stock_line->sell_return;
                $stock += $quantity_change;
                $stock_history_array[] = array_merge($temp_array, [
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'purchase_transfer',
                    'type_label' => __('lang_v1.sell_return'),
                    'ref_no' => $stock_line->invoice_no,
                    'stock_in_second_unit' => $this->roundQuantity($stock_in_second_unit),
                ]);
            }
        }

        return array_reverse($stock_history_array);
    }

    public function roundQuantity($quantity)
    {
        $quantity_precision = session('business.quantity_precision', 2);

        return round($quantity, $quantity_precision);
    }
}
