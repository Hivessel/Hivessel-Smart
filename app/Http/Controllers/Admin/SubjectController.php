<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;
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

    // public function subjects(Request $request){
    //     if ($request->ajax()) {
    //         try {
    //             $subjects = Subject::select(['id', 'subject', 'active'])->get();
    
    //             return DataTables::of($subjects)
    //                 ->addColumn('active', function ($subject) {
    //                     return '<div class="form-check form-switch">
    //                                 <input class="form-check-input custom-checkbox toggle-active-tag" type="checkbox" 
    //                                     data-id="'.$subject->id.'"
    //                                 data-subject="'.$subject->subject.'"
    //                                 data-active-tag="'.$subject->active.'" '.($subject->active ? 'checked' : '').'>
    //                             </div>';
    //                 })
    //                 ->addColumn('action', function ($subject) {
    //                     return '<button class="btn btn-md btn-secondary edit-grade-btn"
    //                                 data-id="'.$subject->id.'"
    //                                 data-level="'.$subject->level.'"
    //                                 data-active-tag="'.$subject->active.'">
    //                                 <i class="fas fa-edit pr-2"></i>Edit</button>
    //                                 <button class="btn btn-md btn-danger delete-grade-btn"
    //                                 data-id="'.$subject->id.'"><i class="fas fa-trash pr-2"></i>Delete</button>';
    //                 })
    //                 ->rawColumns(['active', 'action']) // ✅ Ensure HTML rendering
    //                 ->make(true);
    //         } catch (\Exception $e) {
    //             return response()->json(['error' => $e->getMessage()], 500);
    //         }
    //     }
    
    //     return response()->json(['error' => 'Invalid request'], 400);
    // }

    public function subjects(Request $request){
        if ($request->ajax()) {
            try {
                
                $subject = Subject::with('grade')->get();
                return DataTables::of($subject)
                    ->addColumn('grade', function ($subject) {
                        return $subject->grade->level;
                    })
                    
                    ->addColumn('action', function ($subject) {
                        return '<button class="btn btn-md btn-secondary edit-subject-btn"
                                    data-id="'.$subject->id.'"
                                    data-grade="'.$subject->grade->level.'"
                                    data-subject="'.$subject->subject.'"
                                    data-active-tag="'.$subject->active.'">
                                    <i class="fas fa-edit pr-2"></i>Edit</button>
                                    <button class="btn btn-md btn-danger delete-subject-btn"
                                    data-id="'.$subject->id.'">
                                    <i class="fas fa-trash pr-2"></i>Delete</button>
                                    ';
                    })
                    ->rawColumns(['action']) // Ensure HTML rendering
                    ->make(true);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }

    public function destroy(Request $request){
        try {
            $subject = Subject::findOrFail($request->id);
            $subject->delete();
            return response('', 200);
        } catch (Throwable $error) {
            info($error->getMessage());
            return response()->json([
                'error' => $error->getMessage(),
            ], 500);
        }
    }


}
