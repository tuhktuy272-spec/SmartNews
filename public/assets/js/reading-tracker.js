(function () {
    const scriptTag = document.currentScript;
    const articleId = scriptTag.getAttribute('data-article-id');

    if (!articleId) {
        console.warn('[reading-tracker] Thiếu data-article-id, bỏ qua tracking.');
        return;
    }

    const API_URL = '/SmartNews/public/api/reading_log.php';

    const startTime = Date.now();

    let maxScrollPercentage = 0;
    let sent = false;

    function getSessionId() {
        let sessionId = localStorage.getItem('reader_session_id');

        if (!sessionId) {
            if (crypto.randomUUID) {
                sessionId = crypto.randomUUID();
            } else {
                sessionId = String(Date.now()) + Math.random();
            }

            localStorage.setItem('reader_session_id', sessionId);
        }

        return sessionId;
    }

    function calcScrollPercentage() {
        const scrollTop = window.scrollY || document.documentElement.scrollTop;
        const viewportHeight = window.innerHeight;
        const fullHeight = document.documentElement.scrollHeight;

        if (fullHeight <= viewportHeight) {
            return 100;
        }

        const percentage = ((scrollTop + viewportHeight) / fullHeight) * 100;

        return Math.max(0, Math.min(100, percentage));
    }

    function detectDevice() {
        const userAgent = navigator.userAgent;

        if (/mobile/i.test(userAgent)) {
            return 'mobile';
        }

        if (/tablet|ipad/i.test(userAgent)) {
            return 'tablet';
        }

        return 'desktop';
    }

    window.addEventListener('scroll', function () {
        const percentage = calcScrollPercentage();

        if (percentage > maxScrollPercentage) {
            maxScrollPercentage = percentage;
        }
    }, {
        passive: true,
    });

    function sendLog() {
        if (sent) {
            return;
        }

        sent = true;

        const timeSpent = Math.round((Date.now() - startTime) / 1000);

        const payload = JSON.stringify({
            article_id: parseInt(articleId, 10),
            session_id: getSessionId(),
            time_spent: timeSpent,
            scroll_percentage: Math.round(maxScrollPercentage * 10) / 10,
            device: detectDevice(),
        });

        if (navigator.sendBeacon) {
            const blob = new Blob([payload], {
                type: 'application/json',
            });

            navigator.sendBeacon(API_URL, blob);
        } else {
            fetch(API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: payload,
                keepalive: true,
            });
        }
    }

    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'hidden') {
            sendLog();
        }
    });

    window.addEventListener('beforeunload', sendLog);
})();