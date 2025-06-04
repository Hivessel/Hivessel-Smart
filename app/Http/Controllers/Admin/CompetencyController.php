<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competency;
use Illuminate\Http\Request;
use Throwable;

class CompetencyController extends Controller
{
    // public function all(Request $request){
    //     try {
    //         $data = Competency::query();

    //         if($request->filled('active')){
    //             $active = $request->active;
    //             $data->where(function($query) use ($active){
    //                 $query->where('active', $active);
    //             });
    //         }

    //         if($request->filled('content_id')){
    //             $content_id = $request->content_id;
    //             $data->where(function($query) use ($content_id){
    //                 $query->where('content_id', $content_id);
    //             });
    //         }
    //         return $data->get();

    //     } catch (Throwable $error) {
    //         return response()->json([
    //             'error' => $error->getMessage(),
    //         ], 500);
    //     }
    // }

    public function all(Request $request)
    {
        try {
            $data = Competency::query();

            if ($request->filled('active')) {
                $active = $request->input('active');
                $data->where(function ($query) use ($active) {
                    $query->where('active', $active);
                });
            }

            if ($request->filled('content_id')) {
                $content_id = $request->input('content_id');

                // Try to decode if it's a JSON-style array
                if (is_string($content_id) && str_starts_with($content_id, '[')) {
                    $content_id = json_decode($content_id, true);
                }

                $data->where(function ($query) use ($content_id) {
                    if (is_array($content_id)) {
                        $query->whereIn('content_id', $content_id);
                    } else {
                        $query->where('content_id', $content_id);
                    }
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
