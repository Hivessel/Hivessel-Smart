<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProficiencyLevel;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class ProficiencyLevelController extends Controller
{
    public function index(){
        return Inertia::render('Admin/AssessmentTool/ProficiencyLevels/Index');
    }

    public function store(Request $request){
        // Validate the request data
        $validated = $request->validate([
            'level' => 'required|string|max:255|unique:proficiency_levels,level',
        ],[
            'level.required' => 'The proficiency level is required.',
            'level.unique' => 'The proficiency level has already been taken.'
        ]);

        // Create a new grade level (this is just a placeholder, implement your logic)
        ProficiencyLevel::create([
            'level' => $validated['level'],
        ]);

        return response('', 200);
    }

    public function all(Request $request){
        try {
            $data = ProficiencyLevel::query();
            if($request->filled('active')){
                $active = $request->active;
                $data->where(function($query) use ($active){
                    $query->where('active', $active);
                });
            }
            return $data->get();

        } catch (Throwable $error) {
            info($error->getMessage());
            return response()->json([
                'error' => $error->getMessage(),
            ], 500);
        }
    }

    public function proficiencyLevels(Request $request){
        if ($request->ajax()) {
            try {
                $levels = ProficiencyLevel::select(['id', 'level', 'active'])->get();
    
                return DataTables::of($levels)
                    ->addColumn('active', function ($level) {
                        return '<div class="form-check form-switch">
                                    <input class="form-check-input custom-checkbox toggle-active-tag" type="checkbox" 
                                        data-id="'.$level->id.'"
                                    data-level="'.$level->level.'"
                                    data-active-tag="'.$level->active.'" '.($level->active ? 'checked' : '').'>
                                </div>';
                    })
                    ->addColumn('action', function ($level) {
                        return '<button class="btn btn-md btn-secondary edit-proficiency_level-btn"
                                    data-id="'.$level->id.'"
                                    data-level="'.$level->level.'"
                                    data-active-tag="'.$level->active.'">
                                    <i class="fas fa-edit pr-2"></i>Edit</button>
                                    <button class="btn btn-md btn-danger delete-proficiency-level-btn"
                                    data-id="'.$level->id.'"><i class="fas fa-trash pr-2"></i>Delete</button>';
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
            'id' => 'required|exists:proficiency_levels,id',
            'level' => 'required|string|max:255|unique:proficiency_levels,level,'.$request->id,
            'active' => 'required|boolean',
        ]);

        try {
            $grade = ProficiencyLevel::findOrFail($validated['id']);
            $grade->update([
                'level' => $validated['level'],
                'active' => $validated['active'],
            ]);
            return response('', 200);
        } catch (Throwable $error) {
            info($error->getMessage());
            return response()->json([
                'error' => $error->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request){
        try {
            $level = ProficiencyLevel::findOrFail($request->id);
            $level->delete();
            return response('', 200);
        } catch (Throwable $error) {
            info($error->getMessage());
            return response()->json([
                'error' => $error->getMessage(),
            ], 500);
        }
    }
}
