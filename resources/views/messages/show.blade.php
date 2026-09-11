<x-app-layout title="Messages">
    {{-- Header --}}
    <div class="d-flex align-items-start justify-content-between mb-4 gap-3">
        <div class="flex-grow-1 min-width-0">
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('messages.index') }}" class="btn btn-sm btn-light p-1 d-flex align-items-center" style="border-radius:7px;">
                    <i class="bi bi-arrow-left" style="font-size:.9rem;"></i>
                </a>
                @php $badge = $thread->categoryBadge(); @endphp
                <span class="px-2 py-1 rounded-2 fw-semibold" style="font-size:.72rem;background:{{ $badge['bg'] }};color:{{ $badge['color'] }};">
                    {{ $badge['label'] }}
                </span>
                <h5 class="fw-bold mb-0 text-truncate" style="color:#111827;max-width:500px;">{{ $thread->subject }}</h5>
            </div>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('messages.index') }}" style="color:#1c3faa;">Messages</a></li>
                    <li class="breadcrumb-item active text-muted">{{ Str::limit($thread->subject, 40) }}</li>
                </ol>
            </nav>
        </div>

        @if($thread->created_by === auth()->id() || auth()->user()->hasRole('Admin'))
        <form method="POST" action="{{ route('messages.destroy', $thread) }}"
              onsubmit="return confirm('Delete this entire conversation? This cannot be undone.')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1" style="border-radius:8px;font-size:.8rem;">
                <i class="bi bi-trash3"></i> Delete
            </button>
        </form>
        @endif
    </div>

    <div class="row g-3">
        {{-- Messages column --}}
        <div class="col-lg-8">
            <div class="card d-flex flex-column" style="border-radius:14px;min-height:500px;">
                <div class="px-4 py-3" style="border-bottom:1px solid #f0f3fb;">
                    <span class="fw-bold" style="font-size:.9rem;color:#111827;">
                        <i class="bi bi-chat-dots me-2" style="color:#1c3faa;"></i>
                        {{ $thread->messages->count() }} {{ Str::plural('Message', $thread->messages->count()) }}
                    </span>
                </div>

                {{-- Message list --}}
                <div class="flex-grow-1 p-3 message-list" id="messageList">
                    @foreach($thread->messages as $msg)
                    @php $isMine = $msg->sender_id === auth()->id(); @endphp
                    <div class="d-flex gap-3 mb-3 {{ $isMine ? 'flex-row-reverse' : '' }}">
                        {{-- Avatar --}}
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                             style="width:36px;height:36px;{{ $isMine ? 'background:#1c3faa;color:#fff;' : 'background:#dde3f0;color:#1c3faa;' }}font-size:.75rem;align-self:flex-start;margin-top:2px;">
                            {{ $msg->sender->initials() }}
                        </div>

                        {{-- Bubble --}}
                        <div class="{{ $isMine ? 'text-end' : '' }}" style="max-width:75%;">
                            <div class="d-flex align-items-baseline gap-2 mb-1 {{ $isMine ? 'justify-content-end' : '' }}">
                                <span class="fw-semibold" style="font-size:.8rem;color:#374151;">
                                    {{ $isMine ? 'You' : $msg->sender->name }}
                                </span>
                                <span style="font-size:.7rem;color:#9ca3af;">{{ $msg->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="rounded-3 px-3 py-2"
                                 style="{{ $isMine
                                     ? 'background:#1c3faa;color:#fff;border-radius:14px 4px 14px 14px !important;'
                                     : 'background:#f3f4f6;color:#111827;border-radius:4px 14px 14px 14px !important;' }}
                                 font-size:.875rem;line-height:1.55;white-space:pre-wrap;word-break:break-word;">{{ $msg->body }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Reply form --}}
                <div class="p-3 pt-0" style="border-top:1px solid #f0f3fb;margin-top:auto;">
                    <form method="POST" action="{{ route('messages.reply', $thread) }}" class="pt-3">
                        @csrf
                        <div class="d-flex gap-2 align-items-end">
                            <div class="flex-grow-1">
                                <textarea name="body" rows="3" id="replyBody"
                                          class="form-control @error('body') is-invalid @enderror"
                                          placeholder="Write a reply…"
                                          style="border-radius:9px;font-size:.875rem;resize:none;"
                                          maxlength="5000" required>{{ old('body') }}</textarea>
                                @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="border-radius:9px;background:#1c3faa;border-color:#1c3faa;width:42px;height:42px;padding:0;">
                                <i class="bi bi-send" style="font-size:1rem;"></i>
                            </button>
                        </div>
                        <div class="text-end mt-1">
                            <span id="replyCount" style="font-size:.7rem;color:#9ca3af;">0 / 5000</span>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Details sidebar --}}
        <div class="col-lg-4">
            {{-- Thread info --}}
            <div class="card mb-3" style="border-radius:14px;">
                <div class="px-4 py-3" style="border-bottom:1px solid #f0f3fb;">
                    <span class="fw-bold" style="font-size:.88rem;color:#111827;">Thread Details</span>
                </div>
                <div class="p-4">
                    <div class="mb-3">
                        <div style="font-size:.72rem;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;font-weight:700;" class="mb-1">Category</div>
                        <span class="px-2 py-1 rounded-2 fw-semibold" style="font-size:.78rem;background:{{ $badge['bg'] }};color:{{ $badge['color'] }};">
                            {{ $badge['label'] }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <div style="font-size:.72rem;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;font-weight:700;" class="mb-1">Started By</div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                 style="width:28px;height:28px;background:#dde3f0;color:#1c3faa;font-size:.65rem;flex-shrink:0;">
                                {{ $thread->creator->initials() }}
                            </div>
                            <span style="font-size:.83rem;color:#374151;">
                                {{ $thread->creator->id === auth()->id() ? 'You' : $thread->creator->name }}
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div style="font-size:.72rem;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;font-weight:700;" class="mb-1">Created</div>
                        <div style="font-size:.83rem;color:#374151;">{{ $thread->created_at->format('d M Y, H:i') }}</div>
                    </div>
                    <div>
                        <div style="font-size:.72rem;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;font-weight:700;" class="mb-1">Last Activity</div>
                        <div style="font-size:.83rem;color:#374151;">{{ $thread->updated_at->diffForHumans() }}</div>
                    </div>
                </div>
            </div>

            {{-- Participants --}}
            <div class="card" style="border-radius:14px;">
                <div class="px-4 py-3" style="border-bottom:1px solid #f0f3fb;">
                    <span class="fw-bold" style="font-size:.88rem;color:#111827;">
                        Participants ({{ $thread->participants->count() }})
                    </span>
                </div>
                <div class="p-3">
                    @foreach($thread->participants as $p)
                    <div class="d-flex align-items-center gap-2 py-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                             style="width:34px;height:34px;{{ $p->id === auth()->id() ? 'background:#1c3faa;color:#fff;' : 'background:#dde3f0;color:#1c3faa;' }}font-size:.72rem;">
                            {{ $p->initials() }}
                        </div>
                        <div class="flex-grow-1 min-width-0">
                            <div class="fw-semibold text-truncate" style="font-size:.83rem;color:#111827;">
                                {{ $p->id === auth()->id() ? 'You' : $p->name }}
                            </div>
                            <div style="font-size:.72rem;color:#6b7280;">{{ $p->getRoleNames()->first() }}</div>
                        </div>
                        @if($thread->created_by === $p->id)
                        <span class="badge" style="background:#eef2ff;color:#1c3faa;font-size:.62rem;">Author</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

@push('styles')
<style>
.message-list { overflow-y: auto; max-height: 520px; }
.message-list::-webkit-scrollbar { width: 4px; }
.message-list::-webkit-scrollbar-thumb { background: #dde3f0; border-radius: 4px; }
</style>
@endpush

@push('scripts')
<script>
// Scroll to bottom of messages on load
const list = document.getElementById('messageList');
if (list) list.scrollTop = list.scrollHeight;

// Character counter for reply
const reply = document.getElementById('replyBody');
const count = document.getElementById('replyCount');
if (reply) {
    reply.addEventListener('input', () => count.textContent = reply.value.length + ' / 5000');
    // Allow Ctrl+Enter to submit
    reply.addEventListener('keydown', e => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') reply.closest('form').submit();
    });
}
</script>
@endpush
</x-app-layout>
