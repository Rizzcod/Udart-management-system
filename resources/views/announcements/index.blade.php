<x-app-layout title="Announcements">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;"><i class="bi bi-megaphone me-2"></i>Announcements</h4>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item active text-muted">Announcements</li>
                </ol>
            </nav>
        </div>
        @if($canPost)
        <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#postModal"
                style="border-radius:9px;background:#1c3faa;border-color:#1c3faa;font-size:.875rem;">
            <i class="bi bi-plus-lg"></i> Post Announcement
        </button>
        @endif
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius:10px;">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card" style="border-radius:14px;border:1px solid #e5e7eb;">
        <div class="px-4 py-3 border-bottom d-flex align-items-center gap-2" style="border-color:#f0f3fb !important;">
            <i class="bi bi-megaphone-fill" style="color:#1c3faa;"></i>
            <span class="fw-bold" style="font-size:.95rem;color:#111827;">All Announcements</span>
        </div>

        @forelse($announcements as $ann)
        @php $badge = $ann->typeBadge(); @endphp
        <div class="ann-row px-4 py-3" style="border-bottom:1px solid #f9fafb;">
            <div class="d-flex align-items-start gap-3">
                {{-- Type icon --}}
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:40px;height:40px;background:{{ $badge['bg'] }};margin-top:2px;">
                    <i class="bi bi-{{ $badge['icon'] }}" style="color:{{ $badge['color'] }};font-size:1rem;"></i>
                </div>

                <div class="flex-grow-1 min-width-0">
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                        @if($ann->is_pinned)
                        <span class="badge" style="background:#fef9c3;color:#92400e;font-size:.68rem;border-radius:5px;padding:.25em .55em;">
                            <i class="bi bi-pin-fill me-1"></i>Pinned
                        </span>
                        @endif
                        <span class="badge" style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};font-size:.68rem;border-radius:5px;padding:.25em .55em;">
                            {{ $badge['label'] }}
                        </span>
                        @if($ann->target_role)
                        <span class="badge" style="background:#f3f4f6;color:#374151;font-size:.68rem;border-radius:5px;padding:.25em .55em;">
                            <i class="bi bi-people me-1"></i>{{ $ann->target_role }}
                        </span>
                        @else
                        <span class="badge" style="background:#f0fdf4;color:#15803d;font-size:.68rem;border-radius:5px;padding:.25em .55em;">
                            <i class="bi bi-globe me-1"></i>All Staff
                        </span>
                        @endif
                    </div>

                    <div class="fw-semibold mb-1" style="font-size:.9rem;color:#111827;">{{ $ann->title }}</div>
                    <div style="font-size:.82rem;color:#4b5563;line-height:1.6;white-space:pre-wrap;">{{ $ann->body }}</div>

                    <div class="mt-2 d-flex align-items-center gap-3" style="font-size:.72rem;color:#9ca3af;">
                        <span><i class="bi bi-person me-1"></i>{{ $ann->author->name ?? '—' }}</span>
                        <span><i class="bi bi-clock me-1"></i>{{ $ann->created_at->diffForHumans() }}</span>
                        @if($ann->expires_at)
                        <span><i class="bi bi-calendar-x me-1"></i>Expires {{ $ann->expires_at->format('d M Y') }}</span>
                        @endif
                    </div>
                </div>

                @if($canPost)
                <div class="flex-shrink-0 ms-2">
                    <form method="POST" action="{{ route('announcements.destroy', $ann) }}"
                          onsubmit="return confirm('Delete this announcement?')">
                        @csrf @method('DELETE')
                        <button type="submit" title="Delete"
                                class="btn btn-sm d-flex align-items-center justify-content-center"
                                style="width:32px;height:32px;border-radius:8px;border:1px solid #fca5a5;background:#fef2f2;color:#dc2626;">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center py-5 text-muted">
            <i class="bi bi-megaphone d-block" style="font-size:2.5rem;opacity:.2;"></i>
            <div class="mt-2">No announcements yet.</div>
        </div>
        @endforelse

        @if($announcements->hasPages())
        <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
            <div class="text-muted" style="font-size:.82rem;">
                Showing {{ $announcements->firstItem() }}–{{ $announcements->lastItem() }} of {{ $announcements->total() }}
            </div>
            {{ $announcements->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

    {{-- Post Announcement Modal (Admin/Supervisor only) --}}
    @if($canPost)
    <div class="modal fade" id="postModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="border-radius:16px;border:none;">
                <form method="POST" action="{{ route('announcements.store') }}">
                    @csrf
                    <div class="modal-header" style="border-bottom:1px solid #f0f3fb;padding:1.25rem 1.5rem;">
                        <h5 class="modal-title fw-bold" style="color:#111827;">
                            <i class="bi bi-megaphone-fill me-2" style="color:#1c3faa;"></i>Post Announcement
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" style="padding:1.5rem;">

                        @if($errors->any())
                        <div class="alert alert-danger mb-3" style="border-radius:10px;">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $e)<li style="font-size:.85rem;">{{ $e }}</li>@endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:.85rem;">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" value="{{ old('title') }}"
                                   class="form-control" placeholder="Announcement title…" style="border-radius:9px;" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:.85rem;">Message <span class="text-danger">*</span></label>
                            <textarea name="body" rows="5" class="form-control" placeholder="Write your announcement…"
                                      style="border-radius:9px;resize:vertical;" required>{{ old('body') }}</textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-sm-4">
                                <label class="form-label fw-semibold" style="font-size:.85rem;">Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-select" style="border-radius:9px;" required>
                                    <option value="info"    {{ old('type','info') === 'info'    ? 'selected' : '' }}>Info</option>
                                    <option value="warning" {{ old('type') === 'warning' ? 'selected' : '' }}>Warning</option>
                                    <option value="urgent"  {{ old('type') === 'urgent'  ? 'selected' : '' }}>Urgent</option>
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label fw-semibold" style="font-size:.85rem;">Target Role</label>
                                <select name="target_role" class="form-select" style="border-radius:9px;">
                                    <option value="">All Staff</option>
                                    @foreach($roles as $r)
                                    <option value="{{ $r }}" {{ old('target_role') === $r ? 'selected' : '' }}>{{ $r }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label fw-semibold" style="font-size:.85rem;">Expires At</label>
                                <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}"
                                       class="form-control" style="border-radius:9px;">
                            </div>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_pinned" id="isPinned" value="1"
                                   {{ old('is_pinned') ? 'checked' : '' }}>
                            <label class="form-check-label" for="isPinned" style="font-size:.875rem;">
                                <i class="bi bi-pin-fill me-1" style="color:#92400e;"></i>Pin this announcement (appears at the top)
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top:1px solid #f0f3fb;padding:1rem 1.5rem;">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:9px;">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="border-radius:9px;background:#1c3faa;border-color:#1c3faa;">
                            <i class="bi bi-send me-1"></i>Post Announcement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

@push('styles')
<style>
.ann-row { transition: background .12s; }
.ann-row:hover { background: #f8faff; }
.ann-row:last-child { border-bottom: none !important; }
</style>
@endpush

@if($errors->any())
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var modal = new bootstrap.Modal(document.getElementById('postModal'));
    modal.show();
});
</script>
@endpush
@endif

</x-app-layout>
