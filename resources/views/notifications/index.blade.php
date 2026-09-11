<x-app-layout title="Notifications">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">Notifications</h4>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item active text-muted">Notifications</li>
                </ol>
            </nav>
        </div>
        <form method="POST" action="{{ route('notifications.read-all') }}">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-outline-secondary d-flex align-items-center gap-2" style="border-radius:9px;font-size:.875rem;">
                <i class="bi bi-check2-all"></i> Mark All Read
            </button>
        </form>
    </div>

    @php
        $iconMap = [
            'alert'   => ['icon' => 'exclamation-circle-fill', 'bg' => '#fee2e2', 'color' => '#dc2626'],
            'warning' => ['icon' => 'exclamation-triangle-fill', 'bg' => '#ffedd5', 'color' => '#ea580c'],
            'info'    => ['icon' => 'info-circle-fill', 'bg' => '#dbeafe', 'color' => '#1c3faa'],
        ];
    @endphp

    <div class="card" style="border-radius:14px;">
        <div class="px-4 py-3 d-flex align-items-center justify-content-between" style="border-bottom:1px solid #f0f3fb;">
            <span class="fw-bold" style="font-size:.95rem;color:#111827;">All Notifications</span>
            @php $unreadCount = $notifications->filter(fn($n) => !$n->is_read)->count(); @endphp
            @if($unreadCount)
            <span class="badge" style="background:#fee2e2;color:#991b1b;font-size:.78rem;padding:.35em .7em;border-radius:6px;">{{ $unreadCount }} unread</span>
            @endif
        </div>

        @forelse($notifications as $notif)
        @php $ic = $iconMap[$notif->type] ?? $iconMap['info']; @endphp
        <div class="d-flex align-items-start gap-3 px-4 py-3 notif-row {{ $notif->is_read ? '' : 'notif-unread' }}" style="border-bottom:1px solid #f9fafb;">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:38px;height:38px;background:{{ $ic['bg'] }};margin-top:2px;">
                <i class="bi bi-{{ $ic['icon'] }}" style="color:{{ $ic['color'] }};font-size:.95rem;"></i>
            </div>
            <div class="flex-grow-1 min-width-0">
                <div class="fw-semibold" style="font-size:.875rem;color:#111827;">
                    {{ $notif->title }}
                    @if(!$notif->is_read)
                    <span class="d-inline-block rounded-circle ms-1" style="width:7px;height:7px;background:#1c3faa;vertical-align:middle;"></span>
                    @endif
                </div>
                <div class="mt-1" style="font-size:.82rem;color:#6b7280;line-height:1.5;">{{ $notif->message }}</div>
                <div class="mt-1" style="font-size:.72rem;color:#9ca3af;">{{ $notif->created_at->diffForHumans() }}</div>
            </div>
            <div class="flex-shrink-0 ms-2">
                @if(!$notif->is_read)
                <form method="POST" action="{{ route('notifications.read', $notif) }}">
                    @csrf @method('PATCH')
                    <button type="submit" title="Mark as read" class="btn btn-sm btn-outline-secondary p-0 d-flex align-items-center justify-content-center" style="width:32px;height:32px;border-radius:8px;">
                        <i class="bi bi-check2"></i>
                    </button>
                </form>
                @else
                <span class="d-flex align-items-center justify-content-center" style="width:32px;height:32px;color:#16a34a;font-size:1.1rem;">
                    <i class="bi bi-check2-all"></i>
                </span>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center py-5 text-muted">
            <i class="bi bi-bell-slash d-block" style="font-size:2.5rem;opacity:.2;"></i>
            <div class="mt-2">No notifications yet.</div>
        </div>
        @endforelse

        @if($notifications->hasPages())
        <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
            <div class="text-muted" style="font-size:.82rem;">Showing {{ $notifications->firstItem() }}–{{ $notifications->lastItem() }} of {{ $notifications->total() }}</div>
            {{ $notifications->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

@push('styles')
<style>
.notif-row { transition:background .12s; }
.notif-row:hover { background:#f8faff !important; }
.notif-row:last-child { border-bottom:none !important; }
.notif-unread { background:#f0f6ff; }
.notif-unread:hover { background:#e8f1ff !important; }
</style>
@endpush
</x-app-layout>
