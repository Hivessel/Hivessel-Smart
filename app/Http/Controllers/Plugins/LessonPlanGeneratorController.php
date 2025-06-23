<?php

namespace App\Http\Controllers\Plugins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;
class LessonPlanGeneratorController extends Controller
{
    public function index(Request $request){
        $chat = null;
        if($request->filled('id')){
            $chat = Chat::where('id', $request->id)
                ->where('user_id', Auth::user()->id)
                ->firstOrFail() ?? null;
        }
        return Inertia::render('Client/Plugins/LessonPlanGenerator',[
             'chat' => $chat,
             'messages' => Chat::latest()->where('user_id', Auth::id())->where('plugin', 'Lesson Planner')->get()
        ]);
    }
}

