<?php

namespace Modules\MiniReportB1\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\MiniReportB1\Entities\MiniReportB1Folder;
use Modules\MiniReportB1\Entities\MiniReportB1File;

class FolderController extends Controller
{

    public function getPrintLayout(Request $request)
    {
        $tableContent = $request->input('tableContent', '');
        return view('minireportb1::MiniReportB1.components.printbutton', ['tableContent' => $tableContent]);
    }

    public function createFolder(Request $request)
    {
        try {
            // Validate request
            if (empty($request->folder_name)) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Section name is required'
                ]);
            }

            // Check if name exists in folders
            $existingFolder = MiniReportB1Folder::where('business_id', session('business.id'))
                ->where('folder_name', $request->folder_name)
                ->exists();

            // Check if name exists in files
            $existingFile = MiniReportB1File::where('business_id', session('business.id'))
                ->where('file_name', $request->folder_name)
                ->exists();

            if ($existingFolder || $existingFile) {
                return response()->json([
                    'success' => false,
                    'msg' => 'A folder or file with this name already exists'
                ]);
            }

            // Get the max order value
            $maxOrder = MiniReportB1Folder::where('business_id', session('business.id'))
                ->max('order') ?? -1;

            $folder = new MiniReportB1Folder();
            $folder->business_id = session('business.id');
            $folder->folder_name = $request->folder_name;
            $folder->type = 'report_section';
            $folder->order = $maxOrder + 1;  // Set the order to be after the last folder
            $folder->save();

            return response()->json([
                'success' => true,
                'msg' => 'Section created successfully',
                'data' => [
                    'id' => $folder->id,
                    'name' => $folder->folder_name,
                    'type' => $folder->type
                ]
            ]);
        } catch (\Exception $e) {            return response()->json([
                'success' => false,
                'msg' => 'Error creating section'
            ]);
        }
    }

    public function createFile(Request $request)
    {
        try {
            // Validate request
            if (empty($request->file_name)) {
                return response()->json([
                    'success' => false,
                    'msg' => 'File name is required'
                ]);
            }

            if (empty($request->parent_id)) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Section is required'
                ]);
            }

            // Check if name exists in folders
            $existingFolder = MiniReportB1Folder::where('business_id', session('business.id'))
                ->where('folder_name', $request->file_name)
                ->exists();

            // Check if name exists in files
            $existingFile = MiniReportB1File::where('business_id', session('business.id'))
                ->where('file_name', $request->file_name)
                ->exists();

            if ($existingFolder || $existingFile) {
                return response()->json([
                    'success' => false,
                    'msg' => 'A folder or file with this name already exists'
                ]);
            }

            // Verify parent folder exists
            $parentFolder = MiniReportB1Folder::where('business_id', session('business.id'))
                ->where('id', $request->parent_id)
                ->first();

            if (!$parentFolder) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Selected section does not exist'
                ]);
            }

            $file = new MiniReportB1File();
            $file->business_id = session('business.id');
            $file->file_name = $request->file_name;
            $file->parent_id = $request->parent_id;
            $file->save();

            return response()->json([
                'success' => true,
                'msg' => 'File created successfully',
                'data' => [
                    'id' => $file->id,
                    'name' => $file->file_name,
                    'parent_id' => $file->parent_id
                ]
            ]);
        } catch (\Exception $e) {            return response()->json([
                'success' => false,
                'msg' => 'Error creating file'
            ]);
        }
    }

    public function getFolderContents(Request $request)
    {
        $folderId = $request->folder_id ?? 0;
        
        $folders = MiniReportB1Folder::where('business_id', session('business.id'))
            ->where('parent_id', $folderId)
            ->get();
            
        $files = MiniReportB1File::where('business_id', session('business.id'))
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'folders' => $folders,
                'files' => $files
            ]
        ]);
    }

    public function deleteFolder(Request $request)
    {
        $folder = MiniReportB1Folder::find($request->folder_id);
        if ($folder && $folder->business_id == session('business.id')) {
            // Delete all child folders recursively
            MiniReportB1Folder::where('parent_id', $folder->id)->delete();
            
            // Delete all files in this folder
            MiniReportB1File::where('parent_id', $folder->id)->delete();
            
            // Delete the folder itself
            $folder->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }

    public function deleteFile(Request $request)
    {
        $file = MiniReportB1File::find($request->file_id);
        if ($file && $file->business_id == session('business.id')) {
            $file->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }

    public function getFoldersList()
    {
        $business_id = session('business.id');
        // Only get report section folders
        $folders = MiniReportB1Folder::where('business_id', $business_id)
            ->where('type', 'report_section')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $folders
        ]);
    }

    public function renameFolder(Request $request)
    {
        try {
            $folder = MiniReportB1Folder::find($request->folder_id);
            if ($folder && $folder->business_id == session('business.id')) {
                $folder->folder_name = $request->folder_name;
                $folder->save();
                return response()->json(['success' => true]);
            }
            return response()->json(['success' => false]);
        } catch (\Exception $e) {            return response()->json(['success' => false]);
        }
    }

    public function updateFolderOrder(Request $request)
    {
        try {
            $folderOrder = $request->folder_order;
            foreach ($folderOrder as $index => $folderId) {
                MiniReportB1Folder::where('id', $folderId)
                    ->where('business_id', session('business.id'))
                    ->update(['order' => $index]);
            }
            return response()->json(['success' => true]);
        } catch (\Exception $e) {            return response()->json(['success' => false]);
        }
    }

    public function renameFile(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'file_id' => 'required|integer',
                'file_name' => 'required|string|max:255',
            ]);
    
            // Check if new name exists in folders
            $existingFolder = MiniReportB1Folder::where('business_id', session('business.id'))
                ->where('folder_name', $request->file_name)
                ->exists();
    
            // Check if new name exists in other files
            $existingFile = MiniReportB1File::where('business_id', session('business.id'))
                ->where('file_name', $request->file_name)
                ->where('id', '!=', $request->file_id)
                ->exists();
    
            if ($existingFolder || $existingFile) {
                return response()->json([
                    'success' => false,
                    'msg' => 'A folder or file with this name already exists',
                    'error_code' => 'NAME_CONFLICT'
                ], 409); // 409 Conflict
            }
    
            // Find the file to rename
            $file = MiniReportB1File::find($request->file_id);
            if (!$file) {
                return response()->json([
                    'success' => false,
                    'msg' => 'File not found',
                    'error_code' => 'FILE_NOT_FOUND'
                ], 404); // 404 Not Found
            }
    
            // Ensure the file belongs to the current business
            if ($file->business_id != session('business.id')) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Unauthorized to rename this file',
                    'error_code' => 'UNAUTHORIZED'
                ], 403); // 403 Forbidden
            }
    
            // Rename the file
            $file->file_name = $request->file_name;
            $file->save();
    
            return response()->json([
                'success' => true,
                'msg' => 'File renamed successfully'
            ]);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'success' => false,
                'msg' => 'Validation error',
                'errors' => $e->errors(),
                'error_code' => 'VALIDATION_ERROR'
            ], 422); // 422 Unprocessable Entity
    
        } catch (\Exception $e) {
            // Handle any other exceptions
            return response()->json([
                'success' => false,
                'msg' => 'An unexpected error occurred',
                'error' => $e->getMessage(),
                'error_code' => 'INTERNAL_ERROR'
            ], 500); // 500 Internal Server Error
        }
    }

    public function moveFile(Request $request)
    {
        try {
            $file = MiniReportB1File::find($request->file_id);
            if ($file && $file->business_id == session('business.id')) {
                $file->parent_id = $request->parent_id;
                $file->save();
                return response()->json(['success' => true]);
            }
            return response()->json(['success' => false]);
        } catch (\Exception $e) {            return response()->json(['success' => false]);
        }
    }
} 