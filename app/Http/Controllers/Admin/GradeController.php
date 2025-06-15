<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

use Yajra\DataTables\Facades\DataTables;

class GradeController extends Controller
{
    public function index(Request $request){
    
        return Inertia::render('Admin/AssessmentTool/Grades/Index');
    }

    public function store(Request $request){
        // Validate the request data
        $validated = $request->validate([
            'grade' => 'required|string|max:255|unique:grades,level',
        ]);

        // Create a new grade level (this is just a placeholder, implement your logic)
        Grade::create([
            'level' => $validated['grade'],
        ]);

        return response('', 200);
    }

    public function all(Request $request)
    {
        try {
            $data = Grade::query();

            if ($request->filled('active')) {
                $active = (int) $request->active;
                $data->where('active', $active);
            }

            return response()->json($data->get());
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function grades(Request $request){
        if ($request->ajax()) {
            try {
                $grades = Grade::select(['id', 'level', 'active'])->get();
    
                return DataTables::of($grades)
                    ->addColumn('active', function ($grade) {
                        return '<div class="form-check form-switch">
                                    <input class="form-check-input custom-checkbox toggle-active-tag" type="checkbox" 
                                        data-id="'.$grade->id.'"
                                    data-level="'.$grade->level.'"
                                    data-active-tag="'.$grade->active.'" '.($grade->active ? 'checked' : '').'>
                                </div>';
                    })
                    ->addColumn('action', function ($grade) {
                        return '<button class="btn btn-md btn-secondary edit-grade-btn"
                                    data-id="'.$grade->id.'"
                                    data-level="'.$grade->level.'"
                                    data-active-tag="'.$grade->active.'">
                                    <i class="fas fa-edit pr-2"></i>Edit</button>
                                    <button class="btn btn-md btn-danger delete-grade-btn"
                                    data-id="'.$grade->id.'"><i class="fas fa-trash pr-2"></i>Delete</button>';
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
            'level' => 'required|string|max:255|unique:grades,level,'.$request->id,
            'active' => 'required|boolean',
        ]);

        try {
            $grade = Grade::findOrFail($validated['id']);
            $grade->update([
                'level' => $validated['level'],
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
            $grade = Grade::findOrFail($request->id);
            $grade->delete();
            return response('', 200);
        } catch (Throwable $error) {
            return response()->json([
                'error' => $error->getMessage(),
            ], 500);
        }
    }
}
