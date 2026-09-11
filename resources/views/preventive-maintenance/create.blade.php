<x-app-layout title="Add PM Schedule">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">Add PM Schedule</h4>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:#1c3faa;"><i class="bi bi-house-door"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('preventive-maintenance.index') }}" style="color:#1c3faa;">Preventive Maintenance</a></li>
                    <li class="breadcrumb-item active text-muted">Add Schedule</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('preventive-maintenance.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2" style="border-radius:9px;font-size:.875rem;">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="row g-4">
        {{-- Info panel --}}
        <div class="col-lg-4">
            <div class="card h-100" style="border-radius:14px;background:#f0f4ff;border:1px solid #c7d4f8;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-robot" style="color:#1c3faa;font-size:1.3rem;"></i>
                        <span class="fw-bold" style="color:#1c3faa;font-size:.95rem;">Smart Scheduling</span>
                    </div>
                    <p class="text-muted mb-3" style="font-size:.82rem;line-height:1.55;">
                        The AI automatically adjusts maintenance intervals based on vehicle age, breakdown history, and mileage. Older or high-breakdown buses are serviced more frequently.
                    </p>

                    {{-- Priority adjustment legend --}}
                    <div class="rounded-3 p-3 mb-3" style="background:#e0e7ff;border:1px solid #c7d4f8;">
                        <div class="mb-2" style="font-size:.72rem;font-weight:700;color:#3730a3;text-transform:uppercase;letter-spacing:.04em;">Interval Adjustments</div>
                        <div class="d-flex flex-column gap-1">
                            <div class="d-flex justify-content-between align-items-center" style="font-size:.75rem;color:#3730a3;">
                                <span><span class="badge" style="background:#fee2e2;color:#991b1b;font-size:.62rem;">Critical</span> priority</span>
                                <strong>50% of standard</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center" style="font-size:.75rem;color:#3730a3;">
                                <span><span class="badge" style="background:#ffedd5;color:#c2410c;font-size:.62rem;">High</span> priority</span>
                                <strong>65% of standard</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center" style="font-size:.75rem;color:#3730a3;">
                                <span><span class="badge" style="background:#fef3c7;color:#92400e;font-size:.62rem;">Medium</span> priority</span>
                                <strong>80% of standard</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center" style="font-size:.75rem;color:#3730a3;">
                                <span><span class="badge" style="background:#dcfce7;color:#15803d;font-size:.62rem;">Low</span> priority</span>
                                <strong>Standard (100%)</strong>
                            </div>
                        </div>
                    </div>

                    <div class="mb-1" style="font-size:.78rem;font-weight:700;text-transform:uppercase;color:#6b7280;letter-spacing:.04em;">Standard Intervals</div>
                    <ul class="list-unstyled mb-0" style="font-size:.78rem;color:#374151;">
                        @foreach($templates as $type => $tpl)
                        <li class="d-flex justify-content-between py-1" style="border-bottom:1px solid #dbe4fc;">
                            <span>{{ $type }}</span>
                            <span class="text-muted">{{ $tpl['interval_days'] }} days</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <div class="col-lg-8">
            <div class="card" style="border-radius:14px;">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-3" style="border-bottom:1px solid #f0f3fb;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:#fef9c3;flex-shrink:0;">
                        <i class="bi bi-calendar2-plus" style="color:#854d0e;font-size:1rem;"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size:.95rem;color:#111827;">Schedule Details</div>
                        <div class="text-muted" style="font-size:.75rem;">Intervals are automatically adjusted based on vehicle priority</div>
                    </div>
                </div>
                <div class="card-body px-4 py-4">
                    <form method="POST" action="{{ route('preventive-maintenance.store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold small">Bus <span class="text-danger">*</span></label>
                                <select name="bus_id" id="bus_id" class="form-select @error('bus_id') is-invalid @enderror" style="border-radius:9px;">
                                    <option value="">Select bus…</option>
                                    @foreach($buses as $bus)
                                    <option value="{{ $bus->id }}" {{ old('bus_id', $selectedBus?->id) == $bus->id ? 'selected' : '' }}>
                                        {{ $bus->registration_number }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('bus_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label fw-semibold small">Service Type <span class="text-danger">*</span></label>
                                <select name="service_type" id="service_type" class="form-select @error('service_type') is-invalid @enderror" style="border-radius:9px;">
                                    <option value="">Select service type…</option>
                                    @foreach($templates as $type => $tpl)
                                    <option value="{{ $type }}" {{ old('service_type') === $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('service_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- AI vehicle priority profile (dynamic, shown when bus is selected) --}}
                            <div class="col-12" id="priority_panel" style="display:none;">
                                <div id="priority_content" class="rounded-3 p-3" style="border:1.5px solid #fde68a;background:#fffbeb;">
                                    {{-- filled by JS --}}
                                </div>
                            </div>

                            {{-- Auto-calculated interval preview --}}
                            <div class="col-12" id="preview_row" style="display:none;">
                                <div class="d-flex gap-3 p-3 rounded-3" style="background:#f8faff;border:1px solid #e0e7ff;">
                                    <div class="flex-fill text-center">
                                        <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;font-weight:700;letter-spacing:.04em;">Adjusted Interval</div>
                                        <div class="fw-bold mt-1" id="preview_interval" style="font-size:.9rem;color:#1c3faa;">—</div>
                                    </div>
                                    <div style="width:1px;background:#e0e7ff;"></div>
                                    <div class="flex-fill text-center">
                                        <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;font-weight:700;letter-spacing:.04em;">Next Service Due</div>
                                        <div class="fw-bold mt-1" id="preview_next" style="font-size:.9rem;color:#15803d;">—</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold small">Notes <span class="text-muted fw-normal">(optional)</span></label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Any additional notes…" style="border-radius:9px;">{{ old('notes') }}</textarea>
                            </div>

                            <div class="col-12 pt-2 d-flex gap-2">
                                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2"
                                        style="background:#1c3faa;border-color:#1c3faa;border-radius:9px;font-weight:600;">
                                    <i class="bi bi-check-lg"></i> Add Schedule
                                </button>
                                <a href="{{ route('preventive-maintenance.index') }}" class="btn btn-outline-secondary" style="border-radius:9px;">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
(function () {
    const templates   = @json($templates);
    const busProfiles = @json($busProfiles);

    const busSel   = document.getElementById('bus_id');
    const typeSel  = document.getElementById('service_type');
    const row      = document.getElementById('preview_row');
    const intv     = document.getElementById('preview_interval');
    const next     = document.getElementById('preview_next');
    const pPanel   = document.getElementById('priority_panel');
    const pContent = document.getElementById('priority_content');

    const levelColors = {
        critical: { bg:'#fee2e2', text:'#991b1b', border:'#fca5a5', panelBg:'#fff5f5' },
        high:     { bg:'#ffedd5', text:'#c2410c', border:'#fed7aa', panelBg:'#fffbf5' },
        medium:   { bg:'#fef3c7', text:'#92400e', border:'#fde68a', panelBg:'#fffbeb' },
        low:      { bg:'#dcfce7', text:'#15803d', border:'#bbf7d0', panelBg:'#f0fdf4' },
    };

    function formatDate(d) {
        return d.toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric' });
    }

    function updatePriorityPanel() {
        const busId = busSel.value;
        if (!busId || !busProfiles[busId]) { pPanel.style.display = 'none'; return; }

        const p      = busProfiles[busId];
        const colors = levelColors[p.level] || levelColors.low;
        const pct    = Math.round(p.multiplier * 100);
        const cut    = Math.round((1 - p.multiplier) * 100);
        const adjText = p.level !== 'low'
            ? `Maintenance intervals reduced to <strong>${pct}%</strong> of standard (${cut}% shorter) due to vehicle risk profile.`
            : 'Standard maintenance intervals apply — this vehicle has a clean history.';

        pContent.innerHTML = `
            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                <i class="bi bi-robot" style="color:${colors.text};font-size:1rem;"></i>
                <span class="fw-bold" style="font-size:.85rem;color:${colors.text};">AI Vehicle Priority Profile</span>
                <span class="badge" style="background:${colors.bg};color:${colors.text};font-size:.68rem;font-weight:700;">${p.level.toUpperCase()}</span>
                <span class="ms-auto text-muted" style="font-size:.72rem;">Score: ${p.score}/120</span>
            </div>
            <div class="mb-2" style="font-size:.78rem;color:${colors.text};">${adjText}</div>
            <ul class="mb-0 ps-3" style="font-size:.74rem;color:${colors.text};opacity:.9;line-height:1.55;">
                ${p.insights.map(i => `<li>${i}</li>`).join('')}
            </ul>`;

        pContent.style.borderColor = colors.border;
        pContent.style.background  = colors.panelBg;
        pPanel.style.display = '';
    }

    function updatePreview() {
        const tpl = templates[typeSel.value];
        if (!tpl) { row.style.display = 'none'; return; }

        const busId      = busSel.value;
        const profile    = busId && busProfiles[busId] ? busProfiles[busId] : null;
        const multiplier = profile ? profile.multiplier : 1.0;
        const adjDays    = Math.max(7, Math.round(tpl.interval_days * multiplier));
        const nextDt     = new Date();
        nextDt.setDate(nextDt.getDate() + adjDays);

        let intvHtml;
        if (multiplier < 1.0) {
            const kmPart = tpl.interval_km ? ` / ${tpl.interval_km.toLocaleString()} km` : '';
            intvHtml = `${adjDays} days${kmPart} <span class="text-muted" style="font-size:.78rem;">(standard: ${tpl.interval_days} days)</span>`;
        } else {
            intvHtml = tpl.interval_km
                ? `${adjDays} days / ${tpl.interval_km.toLocaleString()} km`
                : `${adjDays} days`;
        }

        intv.innerHTML   = intvHtml;
        next.textContent = formatDate(nextDt);
        row.style.display = '';
    }

    busSel.addEventListener('change', function () {
        updatePriorityPanel();
        updatePreview();
    });

    typeSel.addEventListener('change', updatePreview);

    // Initialize on page load (restores state after validation error)
    if (busSel.value)  updatePriorityPanel();
    if (typeSel.value) updatePreview();
})();
</script>
@endpush
</x-app-layout>
