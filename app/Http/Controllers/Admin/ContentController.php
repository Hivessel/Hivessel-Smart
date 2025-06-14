<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class ContentController extends Controller
{
    public function index(){
        return Inertia::render('Admin/AssessmentTool/Contents/Index');
    }

    public function store(Request $request){
        // // Validate the request data
        // $validated = $request->validate([
        //     'grade_id' => 'required|integer|exists:grades,id',
        //     'subject_id' => 'required|integer|exists:subjects,id',
        //     'quarter_id' => 'required|integer|exists:quarters,id',
        //     'content' => 'required|string|max:255',
        //     'competencies' => 'required|array|min:1'
        // ],[
        //     'grade_id.required' => 'The grade field is required.',
        //     'subject_id.required' => 'The subject field is required.',
        //     'quarter_id.required' => 'The quarter field is required.',
        //     'competencies.required' => 'Add one or more competencies to continue.'
        // ]);

        // $existingContent = Content::where('grade_id', $validated['grade_id'])
        //     ->where('subject_id', $validated['grade_id'])
        //     ->where('quarter_id', $validated['quarter_id'])
        //     ->where('content', $validated['content'])
        //     ->first();
            
        // if ($existingContent) {
        //     // Throw a ValidationException with a custom error message
        //     throw ValidationException::withMessages([
        //         'subject' => ['This content already exists for the selected combinations.'],
        //     ]);
        // }

        // // Create a new grade level (this is just a placeholder, implement your logic)
        // $content = Content::create([
        //     'grade_id' => $validated['grade_id'],
        //     'subject_id' => $validated['subject_id'],
        //     'quarter_id' => $validated['quarter_id'],
        //     'content' => $validated['content'],
        // ]);


        // foreach($validated['competencies'] as $item){
        //     $content->competencies()->create([
        //         'competency' => $item['competency'],
        //         'attachments' => $item['attachment'],
        //     ]);
        // }

        // return response('', 200);

        // Validate the request data
        $validated = $request->validate([
            'grade_id' => 'required|integer|exists:grades,id',
            'subject_id' => 'required|integer|exists:subjects,id',
            'quarter_id' => 'required|integer|exists:quarters,id',
            'content' => 'required|string|max:255',
            'competencies' => 'required|array|min:1'
        ],[
            'grade_id.required' => 'The grade field is required.',
            'subject_id.required' => 'The subject field is required.',
            'quarter_id.required' => 'The quarter field is required.',
            'competencies.required' => 'Add one or more competencies to continue.'
        ]);

        $existingContent = Content::where('grade_id', $validated['grade_id'])
            ->where('subject_id', $validated['subject_id'])
            ->where('quarter_id', $validated['quarter_id'])
            ->where('content', $validated['content'])
            ->first();
            
        if ($existingContent) {
            // Throw a ValidationException with a custom error message
            throw ValidationException::withMessages([
                'content' => ['This content already exists for the selected combinations'],
            ]);
        }

        $content = Content::create($validated);


        foreach($validated['competencies'] as $item){
            $content->competencies()->create([
                'competency' => $item['competency'],
                'attachments' => $item['attachment']
            ]);
        }
        
        return response('', 200);
    }

    public function all(Request $request){
        try {
            $data = Content::query();

            if($request->filled('grade_id')){
                $grade_id = $request->grade_id;
                $data->where(function($query) use ($grade_id){
                    $query->where('grade_id', $grade_id);
                });
            }

            if($request->filled('subject_id')){
                $subject_id = $request->subject_id;
                $data->where(function($query) use ($subject_id){
                    $query->where('subject_id', $subject_id);
                });
            }

            if($request->filled('quarter_id')){
                $quarter_id = $request->quarter_id;
                $data->where(function($query) use ($quarter_id){
                    $query->where('quarter_id', $quarter_id);
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

    public function contents(Request $request){
        if ($request->ajax()) {
            try {
                
                $contents = Content::with('grade')->get();
                return DataTables::of($contents)
                    ->addColumn('grade', function ($content) {
                        return $content->grade->level;
                    })
                    ->addColumn('subject', function ($content) {
                        return $content->subject->subject;
                    })
                    ->addColumn('quarter', function ($content) {
                        return $content->quarter->quarter;
                    })
                    
                    ->addColumn('action', function ($content) {
                        return '<button class="btn btn-md btn-secondary edit-content-btn"
                                    data-id="'.$content->id.'"
                                    data-grade_id="'.$content->grade->id.'"
                                    data-grade="'.$content->grade->level.'"
                                    data-subject_id="'.$content->subject->id.'"
                                    data-subject="'.$content->subject->subject.'"
                                    data-quarter_id="'.$content->quarter->id.'"
                                    data-quarter="'.$content->quarter->quarter.'"
                                    data-content="'.$content->content.'"
                                    data-competencies="'.htmlspecialchars($content->competencies, ENT_QUOTES, 'UTF-8').'"
                                    data-active-tag="'.$content->active.'">
                                    <i class="fas fa-edit pr-2"></i>Edit</button>
                                    <button class="btn btn-md btn-danger delete-content-btn"
                                    data-id="'.$content->id.'">
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

    public function update(Request $request){
        // Validate the request data
        $validated = $request->validate([
            'id' => 'required|exists:contents,id',
            'grade_id' => 'required|integer|exists:grades,id',
            'subject_id' => 'required|integer|exists:subjects,id',
            'quarter_id' => 'required|integer|exists:quarters,id',
            'content' => 'required|string|max:255',
            'competencies' => 'required|array|min:1',
            'active' => 'required|boolean',
        ],[
            'competencies.required' => 'Add one or more competencies to continue.'
        ]);

        $existingContent = Content::where('grade_id', $validated['grade_id'])
            ->where('subject_id', $validated['subject_id'])
            ->where('quarter_id', $validated['quarter_id'])
            ->where('content', $validated['content'])
            ->first();
            
        if ($existingContent && $existingContent->id !== $validated['id']) {
            // Throw a ValidationException with a custom error message
            throw ValidationException::withMessages([
                'content' => ['This content already exists for the selected combinations.'],
            ]);
        }

        try {
            $content = Content::findOrFail($validated['id']);
            $content->update([
                'content' => $validated['content'],
                'active' => $validated['active'],
            ]);

            // Delete old competencies first
            $content->competencies()->delete();

            // Re-create competencies with updated values
            foreach ($validated['competencies'] as $data) {
                $content->competencies()->create([
                    'competency' => $data['competency'],
                ]);
            }

            return response('', 200);
        } catch (Throwable $error) {
            return response()->json([
                'error' => $error->getMessage(),
            ], 500);
        }


    }

    public function destroy(Request $request){
        try {
            $content = Content::findOrFail($request->id);
            $content->delete();
            return response('', 200);
        } catch (Throwable $error) {
            info($error->getMessage());
            return response()->json([
                'error' => $error->getMessage(),
            ], 500);
        }
    }
}
