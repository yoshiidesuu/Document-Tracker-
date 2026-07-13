<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function index(Request $request): \Illuminate\View\View
    {
        abort_unless($request->user()->hasPermission('messages') || $request->user()->hasPermission('messages.access'), 403);

        return view('system.messages.index');
    }

    public function conversations(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $conversations = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->select('sender_id', 'receiver_id')
            ->distinct()
            ->get()
            ->map(fn ($m) => $m->sender_id === $userId ? $m->receiver_id : $m->sender_id)
            ->unique()
            ->values();

        $users = User::whereIn('id', $conversations)
            ->get(['id', 'firstname', 'middlename', 'lastname', 'profile_picture']);

        $result = $users->map(function ($user) use ($userId) {
            $lastMessage = Message::where(function ($q) use ($userId, $user) {
                $q->where('sender_id', $userId)->where('receiver_id', $user->id);
            })->orWhere(function ($q) use ($userId, $user) {
                $q->where('sender_id', $user->id)->where('receiver_id', $userId);
            })->latest()->first();

            $unreadCount = Message::where('sender_id', $user->id)
                ->where('receiver_id', $userId)
                ->whereNull('read_at')
                ->count();

            return [
                'id' => $user->id,
                'name' => $user->full_name,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'profile_picture' => $user->profile_picture_url,
                'initials' => $user->initials,
                'last_message' => $lastMessage ? [
                    'message' => $lastMessage->message,
                    'created_at' => $lastMessage->created_at->diffForHumans(),
                    'is_mine' => $lastMessage->sender_id === $userId,
                ] : null,
                'unread_count' => $unreadCount,
            ];
        })->sortByDesc(function ($conv) {
            return $conv['last_message'] ? $conv['last_message']['created_at'] : '';
        })->values();

        $totalUnread = Message::where('receiver_id', $userId)->whereNull('read_at')->count();

        return response()->json([
            'conversations' => $result,
            'total_unread' => $totalUnread,
        ]);
    }

    public function messages(Request $request, User $user): JsonResponse
    {
        $userId = $request->user()->id;

        $messages = Message::betweenUsers($userId, $user->id)
            ->with('sender:id,firstname,lastname,profile_picture')
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        Message::where('sender_id', $user->id)
            ->where('receiver_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json($messages);
    }

    public function send(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $message = Message::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $user->id,
            'message' => $request->message,
        ]);

        $message->load('sender:id,firstname,lastname,profile_picture');

        return response()->json($message, 201);
    }

    public function poll(Request $request, User $user): JsonResponse
    {
        $userId = $request->user()->id;
        $afterId = $request->integer('after', 0);

        $messages = Message::where('sender_id', $user->id)
            ->where('receiver_id', $userId)
            ->where('id', '>', $afterId)
            ->with('sender:id,firstname,lastname,profile_picture')
            ->orderBy('created_at', 'asc')
            ->get();

        if ($messages->isNotEmpty()) {
            Message::whereIn('id', $messages->pluck('id'))
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return response()->json($messages);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $count = Message::where('receiver_id', $request->user()->id)
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }

    public function searchUsers(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:1|max:100']);

        $q = $request->q;
        $userId = $request->user()->id;

        $users = User::where('id', '!=', $userId)
            ->where(function ($query) use ($q) {
                $query->where('firstname', 'like', "%{$q}%")
                    ->orWhere('lastname', 'like', "%{$q}%")
                    ->orWhere('middlename', 'like', "%{$q}%")
                    ->orWhere('id_number', 'like', "%{$q}%")
                    ->orWhere(DB::raw("CONCAT(firstname, ' ', lastname)"), 'like', "%{$q}%")
                    ->orWhere(DB::raw("CONCAT(firstname, ' ', middlename, ' ', lastname)"), 'like', "%{$q}%");
            })
            ->where(function ($query) {
                $query->where('status', 'active')->orWhereNull('status');
            })
            ->limit(20)
            ->get(['id', 'firstname', 'middlename', 'lastname', 'profile_picture']);

        $result = $users->map(fn ($user) => [
            'id' => $user->id,
            'name' => $user->full_name,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'profile_picture' => $user->profile_picture_url,
            'initials' => $user->initials,
        ]);

        return response()->json($result);
    }
}
