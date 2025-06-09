<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function store(Request $request, string $id = null){
       
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

        // Send the conversation history and user input to OpenAI to get a response
        $response = OpenAI::chat()->create([
            // 'model' => 'gpt-3.5-turbo',
            'model' => 'gpt-4.1-2025-04-14',
            'messages' => $messages
        ]);

        // Add the assistant's reply to the messages array
        $messages[] = [
            'role' => 'assistant',
            'content' => $response->choices[0]->message->content
        ];

        // Save or update the chat in the database with the new conversation context
        $chat = Chat::updateOrCreate([
            'id' => $request->id,
            'plugin' => $request->plugin,
            'user_id' => Auth::user()->id,
        ],[
            'context' => $messages
        ]);

         // Redirect the user to the chat display page
        return Inertia::location(route('client.plugins.assessment-tool', [
            'tab' => 'history',
            'id' => $chat->id,
        ]));
    }
}
