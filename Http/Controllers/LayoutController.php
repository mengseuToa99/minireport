<?php

namespace Modules\MiniReportB1\Http\Controllers;

use App\Business;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Modules\MiniReportB1\Entities\MiniReportB1Layout;


class LayoutController extends Controller
{

    public function editLayout(Request $request, $layout_name)
    {
        try {
            // Fetch all layout components by layout_name
            $layoutComponents = DB::table('minireportb1_layout')
                ->where('layout_name', $layout_name)
                ->orderBy('position', 'asc') // Order by position
                ->get();
    
            if ($layoutComponents->isEmpty()) {
                return redirect()->back()->with('error', 'Layout not found.');
            }
    
            // Return the edit view with the layout components
            return view('minireportb1::MiniReportB1.edit_layout', [
                'layout_name' => $layout_name, // Pass the layout_name to the view
                'components' => $layoutComponents, // Pass all components to the view
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while fetching the layout.');
        }
    }

    public function deleteLayout($layout_name)
    {
        try {
            // Find the layout by layout_name and delete it
            $layout = DB::table('minireportb1_layout')->where('layout_name', $layout_name)->first();
            if (!$layout) {
                return response()->json([
                    'success' => false,
                    'error' => 'Layout not found.'
                ], 404);
            }
    
            // Delete all records with the same layout_name
            DB::table('minireportb1_layout')->where('layout_name', $layout_name)->delete();
    
            return response()->json([
                'success' => true,
                'message' => 'Layout deleted successfully.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting layout: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getLayoutComponents($layoutName)
    {
        try {
            // Retrieve all elements for the specified layout name
            $components = DB::table('minireportb1_layout')
                ->select('id', 'layout_name', 'type', 'content', 'x', 'y', 'position', 'created_at', 'updated_at')
                ->where('layout_name', $layoutName)
                ->orderBy('position', 'asc') // Optional: Order by position
                ->get()
                ->map(function ($component) {
                    return [
                        'id' => $component->id,
                        'layout_name' => $component->layout_name,
                        'type' => $component->type,
                        'content' => json_decode($component->content, true), // Decode JSON content
                        'x' => $component->x,
                        'y' => $component->y,
                        'position' => $component->position,
                        'created_at' => $component->created_at,
                        'updated_at' => $component->updated_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'components' => $components
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching layout components: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getComponent(Request $request)
    {
        // Get the current date formatted as desired
        $currentDate = Carbon::now()->toFormattedDateString();
        $business_id = $request->session()->get('user.business_id');
        $business = Business::findOrFail($business_id);
        $currentDay = Carbon::now()->format('F j, Y');

        
        //logo date signature place holder
        $component = [
            'business_name' => $business->name,
            'logo' => $business->logo,
            'date' => $currentDay,
            'signature' => 'signature.png',
            'place_holder' => 'place_holder.png'
        ];
    

        // Return the header view with the data
        return view('minireportb1::MiniReportB1.components.print', compact('component'));

    
    }

    public function getAllLayouts()
    {
        try {
            // Retrieve distinct layout names along with created_at and updated_at
            $layouts = DB::table('minireportb1_layout')
                ->select('layout_name', 'created_at', 'updated_at')
                ->distinct()
                ->whereNotNull('layout_name') // Exclude rows with empty layout_name
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($layout) {
                    return [
                        'layout_name' => $layout->layout_name,
                        'created_at' => $layout->created_at,
                        'updated_at' => $layout->updated_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'layouts' => $layouts
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching layouts: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    function createLayout(Request $request)
    {

        $business_id = $request->session()->get('user.business_id');
        $business = Business::findOrFail($business_id);
        $currentDay = date('l');

        return view(
            'minireportb1::MiniReportB1.create_layout',
            [
                'business_name' => $business->name,
                'business_logo' => $business->logo,
                'current_day' => $currentDay,
            ]
        );
    }

    public function show($fileId)
    {
        // Fetch components for the given file
        $components = MiniReportB1Layout::where('file_id', $fileId)
            ->orderBy('position')
            ->get();

        return view('minireport::components.show', compact('components', 'fileId'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'layout_name' => 'required|string|max:255',
                'components' => 'required|array',
                'components.*.element_id' => 'required|string',
                'components.*.type' => 'required|string',
                'components.*.content' => 'required|array',
                'components.*.content.html' => 'required|string',
                'components.*.x' => 'required|numeric',
                'components.*.y' => 'required|numeric',
            ]);

            DB::beginTransaction();

            foreach ($request->components as $index => $component) {
                MiniReportB1Layout::create([
                    'layout_name' => $request->layout_name, // Use layout_name from request
                    'type' => $component['type'],
                    'content' => $component['content'],
                    'x' => $component['x'],
                    'y' => $component['y'],
                    'position' => $index
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Layout saved successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Layout save error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getLayout($fileId)
    {
        try {
            $components = MiniReportB1Layout::where('file_id', $fileId)
                ->orderBy('position')
                ->get();

            return response()->json([
                'success' => true,
                'components' => $components
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updatePosition(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:minireportb1_components,id',
            'x' => 'nullable|integer',
            'y' => 'nullable|integer',
        ]);

        $component = MiniReportB1Layout::find($request->input('id'));
        if ($component) {
            $component->update([
                'x' => $request->input('x', $component->x),
                'y' => $request->input('y', $component->y),
            ]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }
}
