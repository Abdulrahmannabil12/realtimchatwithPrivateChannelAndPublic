<?php

namespace App\Http\Controllers;

use App\Events\MessageDeliverd;
use App\Events\PrivateMessage;
use App\Models\Conversation;
use App\Models\Message;
use App\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $messages = Message::all();
        return view('messages.index', compact('messages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function private_message(User $user)
    {
        $receiver_id = $user->id;
        $sender_id = auth()->user()->id;

        $messages = Message::
        where(function ($query) use ($sender_id, $receiver_id) {

            $query->where('sender_id', $sender_id)->where('receiver_id', $receiver_id);

        })->orWhere(function ($query) use ($sender_id, $receiver_id) {

            $query->where('sender_id', $receiver_id)->where('receiver_id', $sender_id);
        })
            ->orderBy('created_at')
            ->latest()
            ->get();
        /*check for existing channel*/
        if (auth()->user()->conversation) {
            foreach (auth()->user()->conversation as $co) {
                if ($user->conversation) {
                    foreach ($user->conversation as $co2) {
                        if ($co->id == $co2->id) {
                            $conversation = $co2;
                            return view('messages.private-message',
                                compact('user', 'messages', 'conversation'));
                        }
                    }
                }
            }
        }
        $conversation = Conversation::create(['created_at' => now()]);
        $conversation->users()->attach([$user->id, auth()->user()->id]);


        return view('messages.private-message', compact('user', 'messages', 'conversation'));
    }

    public function create(Request $request, $receiver_id)
    {


        $sender = auth()->user();
        if (!$sender) {
            return response()->json([
                "success" => false,
                "message" => "Sender not registered",
                "data" => []
            ]);
        } else {
            $receiver = User::find($receiver_id);
            if (!$receiver) {
                return response()->json([
                    "success" => false,
                    "message" => "receiver not registered",
                    "data" => []
                ]);
            } else {
                $message = Message::create([
                    'sender_id' => auth()->user()->id,
                    'receiver_id' => $receiver_id,
                    'message_type' => 'text',
                    'body' => $request->body,
                    'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                    'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
                ]);
                $userIds = [auth()->user()->id, $receiver_id];
                /* send message to channel*/
                $conversation = Conversation::whereHas('users', function ($q) use ($userIds) {
                    $q->where('user_id', (array)$userIds);
                })->get();
                broadcast(new PrivateMessage($message, $conversation[0]))->toOthers();


                return response()->json([
                    "success" => true,
                    "message" => "Message created successfully.",
                    "data" => $message
                ]);
            }


        }

    }


    public function store(Request $request)
    {
        try {


            $message = Message::create([
                'sender_id' => auth()->user()->id,
                'receiver_id' => 0,
                'message_type' => 'text',
                'body' => $request->body,
                'created_at' => now(),
            ]);
            broadcast(new MessageDeliverd($message))->toOthers();
            return response()->json([
                'success' => true,
                'data' => $message
            ]);
        } catch (\Exception $e) {
            return response($e->getMessage());
        }
    }

    public function DeleteMessage($id)
    {
        try {
            $message = Message::find($id);
            if (!$message) {
                return response()->json([
                    "success" => true,
                    "message" => "Message Not found.",
                    "data" => $message
                ]);
            }

            if ($message->created_at->diffInMinutes() < 60) {

                return response()->json([
                    "success" => false,
                    "message" => "Cannot delete message Before an hour.",
                    "data" => $message
                ]);
            }
            if ($message->message_type === 'video') {

                $video = Str::after($message->message, 'assets/');
                $video = public_path('assets/' . $video);
                unlink($video); //delete from folder
            }
            if ($message->message_type === 'image') {

                $image = Str::after($message->message, 'assets/');
                $image = public_path('assets/' . $image);
                unlink($image); //delete from folder
            }
            $message->delete();
            return response()->json([
                "success" => false,
                "message" => "Message deleted successfully.",
                "data" => $message
            ]);
        } catch (\Exception $ex) {

            return response()->json([
                "success" => false,
                "message" => "Message deleted failed.",
                "data" => $message
            ]);

        }
    }


}
