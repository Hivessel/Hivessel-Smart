<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProficiencyLevel;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

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
}
