<?php

namespace Modules\MiniReportB1\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Contact;
use App\Product;
use Yajra\DataTables\Facades\DataTables;
use Modules\MiniReportB1\Entities\MiniReportB1;
use Modules\MiniReportB1\Entities\MiniReportB1Category;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Illuminate\Support\Facades\Auth;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\Schema;
use App\Utils\TransactionUtil;

class MiniReportB1Controller extends Controller
{
    protected $moduleUtil;
    protected $transactionUtil;

    public function __construct(
        ModuleUtil $moduleUtil,
        TransactionUtil $transactionUtil
    ) {
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
    }

    public function modulefield()
    {
        $tableName = 'minireportb1_main';

        try {
            // Query the information schema to get column details
            $columns = DB::select(DB::raw("SHOW COLUMNS FROM $tableName"));

            // Prepare the response as an associative array to check for duplicates
            $columnInfo = [];
            foreach ($columns as $column) {
                $columnInfo[$column->Field] = [
                    'name' => $column->Field,
                    'type' => $column->Type,
                ];
            }

            // Add dynamic columns
            $additionalColumns = json_decode('[{"name":"Date_1","type":"date"},{"name":"Title_1","type":"string"}]', true);

            if (is_array($additionalColumns)) {
                foreach ($additionalColumns as $additionalColumn) {
                    $columnName = $additionalColumn['name'];

                    // Always replace the existing static column with the dynamic column
                    $columnInfo[$columnName] = $additionalColumn;
                }
            }

            // Convert back to an indexed array
            $columnInfo = array_values($columnInfo);

            return response()->json($columnInfo);
        } catch (\Exception $e) {
            // Return a JSON response with the error message
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'minireportb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((! auth()->user()->can('module.minireportb1'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $MiniReportB1 = MiniReportB1::where('minireportb1_main.business_id', $business_id)
            ->leftJoin('minireportb1_category as minireportb1category', 'minireportb1_main.category_id', '=', 'minireportb1category.id')
            ->where('minireportb1_main.business_id', $business_id)
            ->select('minireportb1_main.*', 'minireportb1category.name as category_name');

        if (!empty(request()->start_date) && !empty(request()->end_date)) {
            $start = request()->start_date;
            $end = request()->end_date;
            $MiniReportB1->whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $end);
        }




        $result = $MiniReportB1->get();

        return response()->json($result);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'minireportb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((! auth()->user()->can('module.minireportb1'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $minireportb1_categories = MiniReportB1Category::forDropdown($business_id);
        $users = User::forDropdown($business_id);
        $customers = Contact::where('business_id', $business_id)
            ->where('type', 'customer')
            ->pluck('mobile', 'id');
        $suppliers = Contact::where('business_id', $business_id)
            ->where('type', 'supplier')
            ->pluck('mobile', 'id');
        $products = Product::where('business_id', $business_id)
            ->pluck('name', 'id');

        return response()->json([
            'categories' => $minireportb1_categories,
            'users' => $users,
            'customers' => $customers,
            'suppliers' => $suppliers,
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|integer',






            'Date_1' => 'nullable',


            'Title_1' => 'nullable',

        ]);

        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $minireportb1 = new MiniReportB1();
            $minireportb1->title = $request->title;
            $minireportb1->description = $request->description;
            $minireportb1->business_id = $business_id;
            $minireportb1->category_id = $request->category_id;
            $minireportb1->created_by = auth()->user()->id;






            $minireportb1->Date_1 = $request->Date_1;


            $minireportb1->Title_1 = $request->Title_1;


            $minireportb1->save();

            return response()->json(['success' => true, 'msg' => __('minireportb1::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function edit($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'minireportb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((! auth()->user()->can('module.minireportb1'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $minireportb1 = MiniReportB1::find($id);
        $minireportb1 = MiniReportB1Category::forDropdown($business_id);
        $users = User::forDropdown($business_id);

        return response()->json([
            'categories' => $minireportb1_categories,
            'users' => $users,
            'minireportb1' => $minireportb1,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|integer',






            'Date_1' => 'nullable',


            'Title_1' => 'nullable',

        ]);

        try {
            $minireportb1 = MiniReportB1::find($id);
            $minireportb1->title = $request->title;
            $minireportb1->description = $request->description;
            $minireportb1->category_id = $request->category_id;
            $minireportb1->created_by = auth()->user()->id;






            $minireportb1->Date_1 = $request->Date_1;


            $minireportb1->Title_1 = $request->Title_1;


            $minireportb1->save();

            return response()->json(['success' => true, 'msg' => __('minireportb1::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }



    public function destroy($id)
    {
        try {
            MiniReportB1::destroy($id);
            return response()->json(['success' => true, 'msg' => __('minireportb1::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'minireportb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((! auth()->user()->can('module.minireportb1'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $categories = MiniReportB1Category::where('business_id', $business_id)->get();

        return response()->json([
            'categories' => $categories,
        ]);
    }

    public function storeCategory(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $minireportb1 = new MiniReportB1Category();
            $minireportb1->name = $request->name;
            $minireportb1->description = $request->description;
            $minireportb1->business_id = $business_id;
            $minireportb1->save();

            return response()->json(['success' => true, 'msg' => __('minireportb1::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $module = ModuleCreator::where('module_name', 'minireportb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((! auth()->user()->can('module.minireportb1'))  && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $category = MiniReportB1Category::find($id);

        return response()->json([
            'category' => $category,
        ]);
    }

    public function updateCategory(Request $request, $id)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $category = MiniReportB1Category::find($id);
            $category->name = $request->name;
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('minireportb1::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function destroyCategory($id)
    {
        try {
            MiniReportB1Category::destroy($id);
            return response()->json(['success' => true, 'msg' => __('minireportb1::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}
