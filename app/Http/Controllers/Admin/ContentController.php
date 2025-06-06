<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;
use Illuminate\Validation\ValidationException;
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
            if($request->filled('active')){
                $active = $request->active;
                $data->where(function($query) use ($active){
                    $query->where('active', $active);
                });
            }
            return $data->get();

        } catch (Throwable $error) {
            return response()->json([
                'error' => $error->getMessage(),
            ], 500);
        }
    }
}
