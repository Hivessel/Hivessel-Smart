<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

class GradeController extends Controller
{
    public function index(){
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

    public function all(Request $request){
        try {
            $data = Grade::query();
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
