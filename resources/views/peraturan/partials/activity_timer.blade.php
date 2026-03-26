<script>
    (() => {
        const PERATURAN_ID = {{ (int) $peraturan->id }};
        const TOKEN = '{{ csrf_token() }}';
        const START_URL = '{{ route('peraturan.view-session.start') }}';
        const PING_URL = '{{ route('peraturan.view-session.ping') }}';
        const END_URL = '{{ route('peraturan.view-session.end') }}';
        const IDLE_TIMEOUT_MS = 15000; // 15 detik

        let sessionId = null;
        let activeSeconds = 0;
        let idleSeconds = 0;
        let lastInteractionAt = Date.now();
        let lastSyncAt = 0;
        let timerInterval = null;

        function isIdle() {
            if (document.visibilityState !== 'visible') {
                return true;
            }
            return (Date.now() - lastInteractionAt) > IDLE_TIMEOUT_MS;
        }

        function markInteraction() {
            lastInteractionAt = Date.now();
        }

        async function startSession() {
            try {
                const response = await fetch(START_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': TOKEN,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        peraturan_id: PERATURAN_ID,
                        page_url: window.location.pathname
                    })
                });
                const data = await response.json();
                if (data && data.ok) {
                    sessionId = data.session_id;
                    lastSyncAt = Date.now();
                }
            } catch (e) {
                // silent fail on timer backend
            }
        }

        async function syncPing() {
            if (!sessionId) return;
            try {
                await fetch(PING_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': TOKEN,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        session_id: sessionId,
                        active_seconds: activeSeconds,
                        idle_seconds: idleSeconds
                    })
                });
            } catch (e) {
                // silent fail on timer backend
            }
        }

        function sendEndBeacon() {
            if (!sessionId) return;
            const data = new FormData();
            data.append('_token', TOKEN);
            data.append('session_id', String(sessionId));
            data.append('active_seconds', String(activeSeconds));
            data.append('idle_seconds', String(idleSeconds));
            navigator.sendBeacon(END_URL, data);
        }

        function tick() {
            if (isIdle()) {
                idleSeconds += 1;
            } else {
                activeSeconds += 1;
            }

            if (sessionId && Date.now() - lastSyncAt >= 15000) {
                lastSyncAt = Date.now();
                syncPing();
            }
        }

        ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'].forEach((eventName) => {
            document.addEventListener(eventName, markInteraction, { passive: true });
        });
        document.addEventListener('visibilitychange', markInteraction);

        window.addEventListener('pagehide', sendEndBeacon);
        window.addEventListener('beforeunload', sendEndBeacon);

        startSession().finally(() => {
            timerInterval = setInterval(tick, 1000);
        });
    })();
</script>