<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageThread;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        $threads = MessageThread::whereHas('participants', fn($q) => $q->where('user_id', $userId))
            ->with(['creator', 'latestMessage.sender', 'participants'])
            ->when($request->category, fn($q, $c) => $q->where('category', $c))
            ->when($request->search, fn($q, $s) => $q->where('subject', 'like', "%{$s}%"))
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        // Attach unread count per thread (max 20 threads per page — acceptable N+1)
        $threads->each(function ($thread) use ($userId) {
            $participant = $thread->participants->firstWhere('id', $userId);
            $lastRead    = $participant?->pivot->last_read_at;

            $query = $thread->messages()->where('sender_id', '!=', $userId);
            if ($lastRead) {
                $query->where('created_at', '>', $lastRead);
            }
            $thread->unread_count = $query->count();
        });

        return view('messages.index', compact('threads'));
    }

    public function create()
    {
        $users = User::where('id', '!=', auth()->id())->orderBy('name')->get();

        return view('messages.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject'        => 'required|string|max:200',
            'category'       => 'required|in:general,protocol,urgent,maintenance',
            'participants'   => 'required|array|min:1',
            'participants.*' => 'exists:users,id',
            'body'           => 'required|string|max:5000',
        ]);

        $thread = MessageThread::create([
            'subject'    => $data['subject'],
            'category'   => $data['category'],
            'created_by' => auth()->id(),
        ]);

        $allParticipants = array_unique(array_merge([auth()->id()], $data['participants']));
        $thread->participants()->attach($allParticipants);

        Message::create([
            'thread_id' => $thread->id,
            'sender_id' => auth()->id(),
            'body'      => $data['body'],
        ]);

        // Mark thread read for creator immediately
        $thread->participants()->updateExistingPivot(auth()->id(), ['last_read_at' => now()]);

        return redirect()->route('messages.show', $thread)->with('success', 'Message sent successfully.');
    }

    public function show(MessageThread $thread)
    {
        $userId = auth()->id();

        abort_unless($thread->participants()->where('user_id', $userId)->exists(), 403);

        $thread->load(['creator', 'messages.sender', 'participants']);

        // Mark as read
        $thread->participants()->updateExistingPivot($userId, ['last_read_at' => now()]);

        return view('messages.show', compact('thread'));
    }

    public function reply(Request $request, MessageThread $thread)
    {
        $userId = auth()->id();

        abort_unless($thread->participants()->where('user_id', $userId)->exists(), 403);

        $data = $request->validate(['body' => 'required|string|max:5000']);

        Message::create([
            'thread_id' => $thread->id,
            'sender_id' => $userId,
            'body'      => $data['body'],
        ]);

        $thread->touch();

        // Mark as read for replier
        $thread->participants()->updateExistingPivot($userId, ['last_read_at' => now()]);

        return redirect()->route('messages.show', $thread)->with('success', 'Reply sent.');
    }

    public function destroy(MessageThread $thread)
    {
        $userId = auth()->id();

        abort_unless($thread->created_by === $userId || auth()->user()->hasRole('Admin'), 403);

        $thread->delete();

        return redirect()->route('messages.index')->with('success', 'Conversation deleted.');
    }
}
