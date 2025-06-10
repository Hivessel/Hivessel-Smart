<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

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
            info($error->getMessage());
            return response()->json([
                'error' => $error->getMessage(),
            ], 500);
        }
    }

    public function languages(Request $request){
        if ($request->ajax()) {
            try {
                $languages = Language::select(['id', 'language', 'active'])->get();
    
                return DataTables::of($languages)
                    ->addColumn('active', function ($language) {
                        return '<div class="form-check form-switch">
                                    <input class="form-check-input custom-checkbox toggle-active-tag" type="checkbox" 
                                        data-id="'.$language->id.'"
                                    data-level="'.$language->level.'"
                                    data-active-tag="'.$language->active.'" '.($language->active ? 'checked' : '').'>
                                </div>';
                    })
                    ->addColumn('action', function ($language) {
                        return '<button class="btn btn-md btn-secondary edit-grade-btn"
                                    data-id="'.$language->id.'"
                                    data-level="'.$language->level.'"
                                    data-active-tag="'.$language->active.'">
                                    <i class="fas fa-edit pr-2"></i>Edit</button>
                                    <button class="btn btn-md btn-danger delete-language-btn"
                                    data-id="'.$language->id.'"><i class="fas fa-trash pr-2"></i>Delete</button>';
                    })
                    ->rawColumns(['active', 'action']) // ✅ Ensure HTML rendering
                    ->make(true);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
    
        return response()->json(['error' => 'Invalid request'], 400);
    }

    public function destroy(Request $request){
        try {
            $language = Language::findOrFail($request->id);
            $language->delete();
            return response('', 200);
        } catch (Throwable $error) {
            info($error->getMessage());
            return response()->json([
                'error' => $error->getMessage(),
            ], 500);
        }
    }
}
