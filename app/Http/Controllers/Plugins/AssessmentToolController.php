<?php

namespace App\Http\Controllers\Plugins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;
class AssessmentToolController extends Controller
{
    // public function index(string $id = null){
    //     $chat = null;

    //     if($id){
    //         $chat = Chat::findOrFail($id) ?? null;
    //     }

    //     return Inertia::render('Client/Plugins/AssessmentTool',[
    //          'chat' => $chat
    //     ]);
    // }

    public function index(Request $request){
        $chat = null;
        if($request->filled('id')){
            $id = $request->input('id');
            $chat = Chat::findOrFail($id) ?? null;
        }
        return Inertia::render('Client/Plugins/AssessmentTool',[
             'chat' => $chat,
             'messages' => Chat::latest()->where('user_id', Auth::id())->get()
        ]);
    }
}
