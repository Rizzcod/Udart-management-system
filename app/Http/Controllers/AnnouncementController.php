<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();

        $announcements = Announcement::active()
            ->visibleTo($user)
            ->with('author')
            ->orderByDesc('is_pinned')
            ->latest()
            ->paginate(20);

        // Mark all currently visible announcements as read
        $ids = $announcements->pluck('id')->all();
        if (!empty($ids)) {
            $existing = DB::table('announcement_reads')
                ->where('user_id', $user->id)
                ->whereIn('announcement_id', $ids)
                ->pluck('announcement_id')
                ->all();

            $toInsert = array_diff($ids, $existing);
            if (!empty($toInsert)) {
                $rows = array_map(fn($aid) => [
                    'user_id'         => $user->id,
                    'announcement_id' => $aid,
                    'read_at'         => now(),
                ], $toInsert);
                DB::table('announcement_reads')->insert($rows);
            }
        }

        $canPost = $user->hasAnyRole(['Admin', 'Supervisor']);

        $roles = ['Admin', 'Supervisor', 'Technician', 'Storekeeper', 'Driver'];

        return view('announcements.index', compact('announcements', 'canPost', 'roles'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasAnyRole(['Admin', 'Supervisor']), 403);

        $data = $request->validate([
            'title'       => 'required|string|max:200',
            'body'        => 'required|string|max:3000',
            'type'        => 'required|in:info,warning,urgent',
            'target_role' => 'nullable|string',
            'is_pinned'   => 'boolean',
            'expires_at'  => 'nullable|date|after:now',
        ]);

        Announcement::create([
            'title'       => $data['title'],
            'body'        => $data['body'],
            'type'        => $data['type'],
            'posted_by'   => auth()->id(),
            'target_role' => $data['target_role'] ?: null,
            'is_pinned'   => $request->boolean('is_pinned'),
            'expires_at'  => $data['expires_at'] ?? null,
        ]);

        return redirect()->route('announcements.index')->with('success', 'Announcement posted successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        abort_unless(auth()->user()->hasAnyRole(['Admin', 'Supervisor']), 403);

        $announcement->delete();

        return redirect()->route('announcements.index')->with('success', 'Announcement removed.');
    }
}
