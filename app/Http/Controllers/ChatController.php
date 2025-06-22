<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Throwable;

class ChatController extends Controller
{
    public function store(Request $request, string $id = null){
        // set_time_limit(0);
        // ini_set('max_execution_time', '99999999999');
        // ini_set('memory_limit', '-1');
        
        try{
            
             // Initialize the messages array to store conversation history
            $messages = [];
            // If a chat ID is provided, retrieve the existing chat and its context
            if($request->id){
                $chat = Chat::findOrFail($request->id);
                $messages = $chat->context;
            }

            // Append the user's message (prompt) to the messages array
            $messages[] = [
                'role' => 'user',
                'content' => $request->input('prompt'),
            ];

            if($request->plugin === 'Assessment Tool'){
                $model = config('openai.assessment_tool_model') ?? 'gpt-4.1-nano';
            }else if($request->plugin === 'Lesson Planner'){
                $model = config('openai.lesson_planner_model') ?? 'gpt-4.1-nano';
            }else{
                $model = 'gpt-4.1-nano';
            }

            // Send the conversation history and user input to OpenAI to get a response
            $response = OpenAI::chat()->create([
                'model' => $model, 
                'messages' => $messages,
            ]);

            // dd($response);

            // Add the assistant's reply to the messages array
            $messages[] = [
                'role' => 'assistant',
                'content' => $response->choices[0]->message->content
            ];

            // Save or update the chat in the database with the new conversation context
            // if($request->options){
            //     $chat = Chat::updateOrCreate([
            //         'id' => $request->id,
            //         'plugin' => $request->plugin,
            //         'user_id' => Auth::user()->id,
            //     ],[
            //         'context' => $messages,
            //         'options' => [
            //             'grade' => $request->options['grade'],
            //             'subject' => $request->options['subject'],
            //             'quarter' => $request->options['quarter'],
            //         ]
            //     ]);
            // }else{
            //     $chat = Chat::updateOrCreate([
            //         'id' => $request->id,
            //         'plugin' => $request->plugin,
            //         'user_id' => Auth::user()->id,
            //     ],[
            //         'context' => $messages,
            //         'options' => [
            //             'grade' => $request->options['grade'],
            //             'subject' => $request->options['subject'],
            //             'quarter' => $request->options['quarter'],
            //         ]
            //     ]);
            // }

            $chat = Chat::updateOrCreate([
                'id' => $request->id,
                'plugin' => $request->plugin,
                'user_id' => Auth::user()->id,
            ],[
                'context' => $messages,
                'options' => [
                    'grade' => $request->options['grade'],
                    'subject' => $request->options['subject'],
                    'quarter' => $request->options['quarter'],
                ]
            ]);

            if($request->plugin === 'Assessment Tool'){
                return Inertia::location(route('client.plugins.assessment-tool', [
                    'tab' => 'history',
                    'id' => $chat->id,
                ]));
            }else if($request->plugin === 'Lesson Planner'){
                return Inertia::location(route('client.plugins.lesson-plan-generator', [
                    'tab' => 'history',
                    'id' => $chat->id,
                ]));
            }
        }catch(Throwable $e){
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ]);
        }
    }
}
