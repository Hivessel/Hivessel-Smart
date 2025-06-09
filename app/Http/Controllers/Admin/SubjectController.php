<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;
use Illuminate\Validation\ValidationException;
class SubjectController extends Controller
{
    public function index(){
        return Inertia::render('Admin/AssessmentTool/Subjects/Index');
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'grade_id' => 'required|integer|exists:grades,id',
            'subject' => 'required|string|max:255',
        ], [
            'grade_id.required' => 'The Grade field is required.'
        ]);

        $existingSubject = Subject::where('grade_id', $validated['grade_id'])
            ->where('subject', $validated['subject'])
            ->first();
            
        if ($existingSubject) {
            // Throw a ValidationException with a custom error message
            throw ValidationException::withMessages([
                'subject' => ['This subject already exists for the selected grade.'],
            ]);
        }

        Subject::create($validated);

        return response('', 200);

    }

    public function all(Request $request){
        try {
            $data = Subject::query();

            if($request->filled('grade_id')){
                $grade_id = $request->grade_id;
                $data->where(function($query) use ($grade_id){
                    $query->where('grade_id', $grade_id);
                });
            }

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
