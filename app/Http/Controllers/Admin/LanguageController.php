<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

class LanguageController extends Controller
{
    public function index(){
        return Inertia::render('Admin/AssessmentTool/Languages/Index');
    }

    public function store(Request $request){
        // Validate the request data
        $validated = $request->validate([
            'language' => 'required|string|max:255|unique:languages,language',
        ]);

        // Create a new grade level (this is just a placeholder, implement your logic)
        Language::create([
            'language' => $validated['language'],
        ]);

        return response('', 200);
    }

    public function all(Request $request){
        try {
            $data = Language::query();
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
