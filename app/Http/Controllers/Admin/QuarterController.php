<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quarter;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class QuarterController extends Controller
{
    public function index(){
        return Inertia::render('Admin/AssessmentTool/Quarters/Index');
    }

    public function store(Request $request){
        // Validate the request data
        $validated = $request->validate([
            'quarter' => 'required|string|max:255|unique:quarters,quarter',
        ]);

        Quarter::create([
            'quarter' => $validated['quarter'],
        ]);

        return response('', 200);
    }

    public function all(Request $request){
        try {
            $data = Quarter::query();

            if ($request->has('active')) {
                $active = (int) $request->active;
                $data->where('active', $active);
            }

            return response()->json($data->get());

        } catch (Throwable $error) {
            return response()->json([
                'error' => $error->getMessage(),
            ]);
        }
    }

    public function quarters(Request $request){
        if ($request->ajax()) {
            try {
                $quarters = Quarter::select(['id', 'quarter', 'active'])->get();
    
                return DataTables::of($quarters)
                    ->addColumn('active', function ($quarter) {
                        return '<div class="form-check form-switch">
                                    <input class="form-check-input custom-checkbox toggle-active-tag" type="checkbox" 
                                        data-id="'.$quarter->id.'"
                                    data-level="'.$quarter->level.'"
                                    data-active-tag="'.$quarter->active.'" '.($quarter->active ? 'checked' : '').'>
                                </div>';
                    })
                    ->addColumn('action', function ($quarter) {
                        return '<button class="btn btn-md btn-secondary edit-quarter-btn"
                                    data-id="'.$quarter->id.'"
                                    data-quarter="'.$quarter->quarter.'"
                                    data-active-tag="'.$quarter->active.'">
                                    <i class="fas fa-edit pr-2"></i>Edit</button>
                                    <button class="btn btn-md btn-danger delete-quarter-btn"
                                    data-id="'.$quarter->id.'"><i class="fas fa-trash pr-2"></i>Delete</button>';
                    })
                    ->rawColumns(['active', 'action']) // ✅ Ensure HTML rendering
                    ->make(true);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
    
        return response()->json(['error' => 'Invalid request'], 400);
    }

    public function update(Request $request){
        // Validate the request data
        $validated = $request->validate([
            'id' => 'required|exists:grades,id',
            'quarter' => 'required|string|max:255|unique:quarters,quarter,'.$request->id,
            'active' => 'required|boolean',
        ]);

        try {
            $quarter = Quarter::findOrFail($validated['id']);
            $quarter->update([
                'quarter' => $validated['quarter'],
                'active' => $validated['active'],
            ]);
            return response('', 200);
        } catch (Throwable $error) {
            return response()->json([
                'error' => $error->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request){
        try {
            $quarter = Quarter::findOrFail($request->id);
            $quarter->delete();
            return response('', 200);
        } catch (Throwable $error) {
            return response()->json([
                'error' => $error->getMessage(),
            ], 500);
        }
    }
}
