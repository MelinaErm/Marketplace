<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Models\User;
use App\Events\NewMessage;

class MessageController extends Controller
{
    /**
     * Display the authenticated user's messages.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
{
    //retrieve messages for the authenticated user
    $messages = Message::where('receiver_id', auth()->id())->get();

    //retrieve all users
    $allUsers = User::all();

    //sort users by the most recent message exchange
    $users = $allUsers->sortByDesc(function ($user) {
        $latestMessage = Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();
        return $latestMessage ? $latestMessage->created_at : null;
    });

    //calculate the unread messages count for each user
    $unreadMessagesCounts = [];
    foreach ($users as $user) {
        if ($user->id !== auth()->id()) {
            $unreadMessagesCount = Message::where('sender_id', $user->id)
                ->where('receiver_id', auth()->id())
                ->where('is_read', false)
                ->count();
            $unreadMessagesCounts[$user->id] = $unreadMessagesCount;
        }
    }

    return view('messages.index', compact('messages', 'users', 'unreadMessagesCounts'));
}



    /**
     * Store a new message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id', 
            'message_content' => 'required|string|max:255',
        ]);
    
        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->input('receiver_id'),
            'message_content' => $request->message_content,
            'is_read' => false,
        ]);
    
        broadcast(new NewMessage($message))->toOthers();
    
        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

//add the triggerNewMessageEvent method to handle triggering the broadcast event

public function triggerNewMessageEvent(Request $request)
{
    //validate the incoming request data
    $request->validate([
        'receiver_id' => 'required|exists:users,id',
        'message_content' => 'required|string|max:255',
    ]);

    //create a new message instance (not stored in the database)
    $message = new Message([
        'sender_id' => auth()->id(),
        'receiver_id' => $request->input('receiver_id'),
        'message_content' => $request->message_content,
        'is_read' => false,
    ]);

    //broadcast the new message event
    broadcast(new NewMessage($message))->toOthers();

    //return a JSON response indicating success
    return response()->json(['success' => true]);
}

    /**
     * Get messages for a specific receiver.
     *
     * @param  int  $receiverId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMessagesForReceiver($receiverId)
    {
        //retrieve messages where the provided receiverId is either the sender or the receiver
        $messages = Message::where('receiver_id', $receiverId)
            ->orWhere('sender_id', $receiverId)
            ->get();

        //iterate through each message and add sender's name
        foreach ($messages as $message) {
            $sender = User::find($message->sender_id); // Get the sender's details
            $message->sender_name = $sender->name; // Add sender's name to the message object
        }

        return response()->json($messages);
    }

    /**
     * Mark a message as read.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($id)
    {
        $message = Message::findOrFail($id);
        if ($message->receiver_id == auth()->id()) {
            $message->is_read = 1;
            $message->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 403);
    }

    /**
     * Count unread messages from a specific sender.
     *
     * @param  int  $sender_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function count($id)
    {
        //get the id of the authenitated user 
        $receiver_id = Auth::id();

        //count the unread messages for the receiver
        $unreadCount = Message::where('sender_id', $id)
            ->where('receiver_id', $receiver_id)
            ->where('is_read', 0)
            ->count();

        return response()->json(['count' => $unreadCount]);
    }

    /**
     * Delete a message.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMessage($id)
    {
        $message = Message::where('id', $id)
            ->where('sender_id', auth()->id())
            ->delete();

        return response()->json(['success' => true]);
    }
}
