<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quarter;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

class QuarterController extends Controller
{
    public function index(){
        return Inertia::render('Admin/AssessmentTool/Quarters/Index');
    }

    public function store(Request $request){
        // Validate the request data
        $validated = $request->validate([
            'quarter' => 'required|string|max:255|unique:quarters,quarter',
        ]);

        Quarter::create([
            'quarter' => $validated['quarter'],
        ]);

        return response('', 200);
    }

    public function all(Request $request){
        try {
            $data = Quarter::query();
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
