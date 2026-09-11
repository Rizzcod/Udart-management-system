<x-app-layout title="Messages">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">Messages</h4>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item active text-muted">Messages</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('messages.create') }}" class="btn btn-primary d-flex align-items-center gap-2" style="border-radius:9px;font-size:.875rem;background:#1c3faa;border-color:#1c3faa;">
            <i class="bi bi-pencil-square"></i> New Message
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('messages.index') }}" class="mb-3">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="input-group" style="max-width:280px;">
                <span class="input-group-text bg-white border-end-0" style="border-radius:9px 0 0 9px;">
                    <i class="bi bi-search text-muted" style="font-size:.85rem;"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control border-start-0 ps-0"
                       placeholder="Search conversations…"
                       style="border-radius:0 9px 9px 0;font-size:.875rem;">
            </div>

            @foreach([''=>'All Categories','general'=>'General','protocol'=>'Protocol','urgent'=>'Urgent','maintenance'=>'Maintenance'] as $val => $label)
            <a href="{{ route('messages.index', array_merge(request()->except('category','page'), $val ? ['category'=>$val] : [])) }}"
               class="btn btn-sm {{ request('category')===$val ? 'btn-primary' : 'btn-outline-secondary' }}"
               style="border-radius:8px;font-size:.8rem;{{ request('category')===$val ? 'background:#1c3faa;border-color:#1c3faa;' : '' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>
    </form>

    <div class="card" style="border-radius:14px;">
        <div class="px-4 py-3 d-flex align-items-center justify-content-between" style="border-bottom:1px solid #f0f3fb;">
            <span class="fw-bold" style="font-size:.95rem;color:#111827;">Conversations</span>
            @php $totalUnread = $threads->sum('unread_count'); @endphp
            @if($totalUnread)
            <span class="badge" style="background:#fee2e2;color:#991b1b;font-size:.78rem;padding:.35em .7em;border-radius:6px;">
                {{ $totalUnread }} unread
            </span>
            @endif
        </div>

        @forelse($threads as $thread)
        @php
            $badge   = $thread->categoryBadge();
            $latest  = $thread->latestMessage;
            $unread  = $thread->unread_count;
            $others  = $thread->participants->where('id', '!=', auth()->id())->take(3);
        @endphp
        <a href="{{ route('messages.show', $thread) }}"
           class="d-flex align-items-start gap-3 px-4 py-3 thread-row text-decoration-none {{ $unread ? 'thread-unread' : '' }}"
           style="border-bottom:1px solid #f9fafb;">

            {{-- Category icon --}}
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:42px;height:42px;background:{{ $badge['bg'] }};margin-top:2px;">
                @php
                    $icon = match($thread->category) {
                        'protocol'    => 'shield-check',
                        'urgent'      => 'exclamation-octagon',
                        'maintenance' => 'tools',
                        default       => 'chat-dots',
                    };
                @endphp
                <i class="bi bi-{{ $icon }}" style="color:{{ $badge['color'] }};font-size:1rem;"></i>
            </div>

            <div class="flex-grow-1 min-width-0">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="px-2 py-0 rounded-2 fw-semibold" style="font-size:.7rem;background:{{ $badge['bg'] }};color:{{ $badge['color'] }};">
                        {{ $badge['label'] }}
                    </span>
                    <span class="fw-semibold text-truncate {{ $unread ? 'text-dark' : 'text-muted' }}" style="font-size:.9rem;">
                        {{ $thread->subject }}
                    </span>
                    @if($unread)
                    <span class="badge rounded-pill ms-auto flex-shrink-0" style="background:#1c3faa;font-size:.65rem;">{{ $unread }}</span>
                    @endif
                </div>

                @if($latest)
                <div class="text-truncate" style="font-size:.8rem;color:#6b7280;max-width:520px;">
                    <span class="fw-medium" style="color:#374151;">{{ $latest->sender->id === auth()->id() ? 'You' : $latest->sender->name }}:</span>
                    {{ $latest->body }}
                </div>
                @endif

                <div class="d-flex align-items-center gap-2 mt-1">
                    {{-- Participant avatars --}}
                    <div class="d-flex" style="gap:-4px;">
                        @foreach($others as $p)
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                             title="{{ $p->name }}"
                             style="width:20px;height:20px;background:#dde3f0;color:#1c3faa;font-size:.55rem;border:2px solid #fff;margin-right:-4px;">
                            {{ $p->initials() }}
                        </div>
                        @endforeach
                        @if($thread->participants->count() > 4)
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:20px;height:20px;background:#dde3f0;color:#6b7280;font-size:.55rem;border:2px solid #fff;margin-right:-4px;">
                            +{{ $thread->participants->count() - 4 }}
                        </div>
                        @endif
                    </div>
                    <span style="font-size:.72rem;color:#9ca3af;">
                        {{ $thread->messages()->count() }} {{ Str::plural('message', $thread->messages()->count()) }}
                        &middot; {{ $thread->updated_at->diffForHumans() }}
                    </span>
                </div>
            </div>
        </a>
        @empty
        <div class="text-center py-5 text-muted">
            <i class="bi bi-chat-square-dots d-block" style="font-size:2.5rem;opacity:.2;"></i>
            <div class="mt-2 fw-medium">No conversations yet.</div>
            <div style="font-size:.82rem;">Start a new message to communicate with your team.</div>
            <a href="{{ route('messages.create') }}" class="btn btn-sm btn-primary mt-3" style="border-radius:8px;background:#1c3faa;border-color:#1c3faa;">
                <i class="bi bi-pencil-square me-1"></i> New Message
            </a>
        </div>
        @endforelse

        @if($threads->hasPages())
        <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
            <div class="text-muted" style="font-size:.82rem;">
                Showing {{ $threads->firstItem() }}–{{ $threads->lastItem() }} of {{ $threads->total() }}
            </div>
            {{ $threads->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

@push('styles')
<style>
.thread-row { transition: background .12s; color: inherit; }
.thread-row:hover { background: #f8faff !important; }
.thread-row:last-child { border-bottom: none !important; }
.thread-unread { background: #f0f6ff; }
.thread-unread:hover { background: #e8f1ff !important; }
</style>
@endpush
</x-app-layout>
