class UserHeartbeat {
    constructor(options = {}) {
        this.options = {
            heartbeatInterval: options.heartbeatInterval || 30000, // 30 seconds
            heartbeatEndpoint: options.heartbeatEndpoint || '/api/heartbeat'
        };
        this.heartbeatIntervalId = null;
    }

    async sendHeartbeat() {
        try {
            const response = await fetch(this.options.heartbeatEndpoint, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error('Heartbeat failed');
            }
        } catch (error) {
            console.error('Failed to send heartbeat:', error);
        }
    }

    start() {
        // Send initial heartbeat
        this.sendHeartbeat();

        // Set up regular heartbeat
        this.heartbeatIntervalId = setInterval(() => {
            this.sendHeartbeat();
        }, this.options.heartbeatInterval);

        // Handle page visibility changes
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                this.sendHeartbeat(); // Immediate heartbeat when page becomes visible
            }
        });

        // Handle before unload
        window.addEventListener('beforeunload', () => {
            this.stop();
        });
    }

    stop() {
        if (this.heartbeatIntervalId) {
            clearInterval(this.heartbeatIntervalId);
            this.heartbeatIntervalId = null;
        }
    }
}
