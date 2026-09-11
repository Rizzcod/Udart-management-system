<x-app-layout title="New Message">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">New Message</h4>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('messages.index') }}" style="color:#1c3faa;">Messages</a></li>
                    <li class="breadcrumb-item active text-muted">New Message</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card" style="border-radius:14px;">
                <div class="px-4 py-3" style="border-bottom:1px solid #f0f3fb;">
                    <span class="fw-bold" style="font-size:.95rem;color:#111827;">
                        <i class="bi bi-pencil-square me-2" style="color:#1c3faa;"></i>Compose Message
                    </span>
                </div>

                <form method="POST" action="{{ route('messages.store') }}" class="p-4">
                    @csrf

                    {{-- Subject --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.875rem;color:#374151;">Subject</label>
                        <input type="text" name="subject" value="{{ old('subject') }}"
                               class="form-control @error('subject') is-invalid @enderror"
                               placeholder="e.g. Reminder: Pre-departure inspection protocol"
                               style="border-radius:9px;font-size:.875rem;" maxlength="200" required>
                        @error('subject')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Category --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.875rem;color:#374151;">Category</label>
                        <div class="d-flex flex-wrap gap-2">
                            @php
                                $categories = [
                                    'general'     => ['label' => 'General',     'icon' => 'chat-dots',        'bg' => '#f0fdf4', 'color' => '#16a34a'],
                                    'protocol'    => ['label' => 'Protocol',    'icon' => 'shield-check',     'bg' => '#dbeafe', 'color' => '#1d4ed8'],
                                    'urgent'      => ['label' => 'Urgent',      'icon' => 'exclamation-octagon', 'bg' => '#fee2e2', 'color' => '#dc2626'],
                                    'maintenance' => ['label' => 'Maintenance', 'icon' => 'tools',            'bg' => '#fef3c7', 'color' => '#b45309'],
                                ];
                            @endphp
                            @foreach($categories as $val => $cat)
                            <label class="cat-option" for="cat_{{ $val }}" style="cursor:pointer;">
                                <input type="radio" name="category" id="cat_{{ $val }}" value="{{ $val }}"
                                       class="d-none cat-radio"
                                       {{ old('category', 'general') === $val ? 'checked' : '' }}>
                                <span class="d-flex align-items-center gap-2 px-3 py-2 rounded-3 border cat-label"
                                      style="font-size:.82rem;font-weight:600;background:{{ $cat['bg'] }};color:{{ $cat['color'] }};border-color:{{ $cat['color'] }}33 !important;transition:all .15s;">
                                    <i class="bi bi-{{ $cat['icon'] }}"></i> {{ $cat['label'] }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                        @error('category')
                        <div class="text-danger mt-1" style="font-size:.8rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Recipients --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.875rem;color:#374151;">
                            Recipients <span class="text-muted fw-normal">(select one or more)</span>
                        </label>
                        <div class="border rounded-3 p-3" style="max-height:220px;overflow-y:auto;border-color:#dee2e6 !important;">
                            @forelse($users as $user)
                            <label class="d-flex align-items-center gap-3 py-2 px-2 rounded-2 recipient-row" style="cursor:pointer;">
                                <input type="checkbox" name="participants[]" value="{{ $user->id }}"
                                       class="form-check-input flex-shrink-0 mt-0"
                                       {{ in_array($user->id, (array) old('participants', [])) ? 'checked' : '' }}>
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                     style="width:32px;height:32px;background:#dde3f0;color:#1c3faa;font-size:.75rem;">
                                    {{ $user->initials() }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold" style="font-size:.85rem;color:#111827;">{{ $user->name }}</div>
                                    <div style="font-size:.75rem;color:#6b7280;">{{ $user->getRoleNames()->first() }}
                                        @if($user->department) &middot; {{ $user->department }} @endif
                                    </div>
                                </div>
                            </label>
                            @empty
                            <div class="text-muted text-center py-3" style="font-size:.85rem;">No other users found.</div>
                            @endforelse
                        </div>
                        @error('participants')
                        <div class="text-danger mt-1" style="font-size:.8rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Message body --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="font-size:.875rem;color:#374151;">Message</label>
                        <textarea name="body" rows="6"
                                  class="form-control @error('body') is-invalid @enderror"
                                  placeholder="Write your message here…"
                                  style="border-radius:9px;font-size:.875rem;resize:vertical;"
                                  maxlength="5000" required>{{ old('body') }}</textarea>
                        <div class="d-flex justify-content-end mt-1">
                            <span id="charCount" style="font-size:.72rem;color:#9ca3af;">0 / 5000</span>
                        </div>
                        @error('body')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex align-items-center gap-2 justify-content-end">
                        <a href="{{ route('messages.index') }}" class="btn btn-outline-secondary" style="border-radius:9px;font-size:.875rem;">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2"
                                style="border-radius:9px;font-size:.875rem;background:#1c3faa;border-color:#1c3faa;">
                            <i class="bi bi-send"></i> Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@push('styles')
<style>
.recipient-row { transition: background .1s; }
.recipient-row:hover { background: #f8faff; }
.cat-radio:checked + .cat-label { box-shadow: 0 0 0 2px currentColor; }
</style>
@endpush

@push('scripts')
<script>
const body = document.querySelector('textarea[name="body"]');
const counter = document.getElementById('charCount');
body.addEventListener('input', () => counter.textContent = body.value.length + ' / 5000');

// Select all / none shortcuts
const checkboxes = document.querySelectorAll('input[type="checkbox"][name="participants[]"]');
</script>
@endpush
</x-app-layout>
