<x-app-layout title="IoT Real-Time Monitor">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-broadcast me-2 text-danger"></i>IoT Real-Time Condition Monitor</h5>
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small">Auto-refresh in <span id="countdown" class="fw-bold">5</span>s</span>
            <button class="btn btn-sm btn-outline-secondary" id="refreshNowBtn">
                <i class="bi bi-arrow-clockwise"></i> Refresh Now
            </button>
        </div>
    </div>

    <div id="iotContent">
        @include('iot._content')
    </div>

    @push('scripts')
    <script>
    const iotDataUrl = @json(route('iot.dashboard.data'));
    const iotContent = document.getElementById('iotContent');
    const countdownEl = document.getElementById('countdown');
    const refreshSeconds = 2;
    let seconds = refreshSeconds;

    async function refreshIotDashboard() {
        try {
            const res = await fetch(iotDataUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) return;
            iotContent.innerHTML = await res.text();
        } catch (e) {
            console.error('IoT dashboard refresh failed', e);
        }
    }

    document.getElementById('refreshNowBtn').addEventListener('click', () => {
        seconds = refreshSeconds;
        refreshIotDashboard();
    });

    setInterval(() => {
        seconds--;
        countdownEl.textContent = seconds;
        if (seconds <= 0) {
            seconds = refreshSeconds;
            refreshIotDashboard();
        }
    }, 1000);
    </script>
    @endpush

</x-app-layout>
