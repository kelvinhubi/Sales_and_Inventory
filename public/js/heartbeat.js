class UserHeartbeat {
    constructor(options = {}) {
        this.options = {
            heartbeatInterval: options.heartbeatInterval || 30000, // 30 seconds
            onlineCheckInterval: options.onlineCheckInterval || 60000, // 1 minute
            inactivityTimeout: options.inactivityTimeout || 300000, // 5 minutes
            heartbeatEndpoint: options.heartbeatEndpoint || '/api/heartbeat',
            onlineUsersEndpoint: options.onlineUsersEndpoint || '/api/online-users',
            userEndpoint: options.userEndpoint || '/api/user',
            onStatusUpdate: options.onStatusUpdate || null // Callback for status updates
        };

        this.heartbeatIntervalId = null;
        this.onlineCheckIntervalId = null;
        this.activityCheckIntervalId = null;
        this.currentUser = null;
        this.lastActivityTime = Date.now();
        this.isActive = true;
    }

    async validateSession() {
        try {
            const response = await this.makeAuthenticatedRequest(this.options.userEndpoint);
            if (response.status === 419) { // CSRF token mismatch
                // Refresh the page to get a new CSRF token
                window.location.reload();
                return false;
            }
            if (!response.ok) {
                if (response.status === 401) {
                    // Redirect to login if unauthorized
                    window.location.href = '/login';
                }
                throw new Error('Session invalid');
            }
            this.currentUser = await response.json();
            return true;
        } catch (error) {
            console.error('Session validation error:', error);
            if (error.message === 'Session invalid') {
                // Redirect to login
                window.location.href = '/login';
            }
            return false;
        }
    }

    makeAuthenticatedRequest(url, options = {}) {
        const headers = {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };

        return fetch(url, {
            ...options,
            headers: { ...headers, ...options.headers },
            credentials: 'same-origin'
        });
    }

    updateActivityTimestamp() {
        this.lastActivityTime = Date.now();
        this.isActive = true;
    }

    checkActivity() {
        const now = Date.now();
        const timeSinceLastActivity = now - this.lastActivityTime;

        if (timeSinceLastActivity > this.options.inactivityTimeout) {
            this.isActive = false;
        }
    }

    async sendHeartbeat() {
        try {
            // Check if user is active
            this.checkActivity();

            // Validate CSRF token before sending heartbeat
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken || !csrfToken.getAttribute('content')) {
                // If no CSRF token, refresh the page
                window.location.reload();
                return;
            }

            const response = await this.makeAuthenticatedRequest(this.options.heartbeatEndpoint, {
                method: 'POST',
                body: JSON.stringify({
                    is_active: this.isActive,
                    last_activity: new Date(this.lastActivityTime).toISOString()
                })
            });

            if (!response.ok) {
                throw new Error(`Heartbeat failed: ${response.status}`);
            }
        } catch (error) {
            console.error('Heartbeat error:', error);
        }
    }

    async checkOnlineUsers() {
        try {
            const response = await this.makeAuthenticatedRequest(this.options.onlineUsersEndpoint);

            if (!response.ok) {
                throw new Error(`Failed to fetch online users: ${response.status}`);
            }

            const data = await response.json();

            // Call the callback function if provided
            if (this.options.onStatusUpdate && typeof this.options.onStatusUpdate === 'function') {
                this.options.onStatusUpdate(data);
            }

            return data;
        } catch (error) {
            console.error('Online status check error:', error);
            return [];
        }
    }

    setupActivityListeners() {
        // Track user activity
        const activityEvents = ['mousedown', 'mousemove', 'keydown', 'scroll', 'touchstart'];
        activityEvents.forEach(event => {
            document.addEventListener(event, () => this.updateActivityTimestamp());
        });

        // Handle tab visibility changes
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                this.updateActivityTimestamp();
                this.isActive = true;
                this.sendHeartbeat();
            } else {
                this.isActive = false;
                this.sendHeartbeat();
            }
        });

        // Handle before page unload
        window.addEventListener('beforeunload', async () => {
            this.isActive = false;
            // Attempt to send one last heartbeat synchronously
            try {
                const formData = new FormData();
                formData.append('is_active', 'false');
                navigator.sendBeacon(this.options.heartbeatEndpoint, formData);
            } catch (error) {
                console.error('Failed to send final heartbeat:', error);
            }
            this.stop();
        });
    }

    async start() {
        // Validate session first
        const isValid = await this.validateSession();
        if (!isValid) {
            console.error('Failed to validate session');
            return false;
        }

        // Set up activity tracking
        this.setupActivityListeners();

        // Send initial heartbeat
        await this.sendHeartbeat();

        // Set up regular heartbeat
        this.heartbeatIntervalId = setInterval(() => {
            this.sendHeartbeat();
        }, this.options.heartbeatInterval);

        // Set up regular online status check if callback is provided
        if (this.options.onStatusUpdate) {
            this.onlineCheckIntervalId = setInterval(() => {
                this.checkOnlineUsers();
            }, this.options.onlineCheckInterval);
        }

        // Set up activity checking
        this.activityCheckIntervalId = setInterval(() => {
            this.checkActivity();
        }, 30000); // Check every 30 seconds
    }

    stop() {
        if (this.heartbeatIntervalId) {
            clearInterval(this.heartbeatIntervalId);
            this.heartbeatIntervalId = null;
        }
        if (this.onlineCheckIntervalId) {
            clearInterval(this.onlineCheckIntervalId);
            this.onlineCheckIntervalId = null;
        }
        if (this.activityCheckIntervalId) {
            clearInterval(this.activityCheckIntervalId);
            this.activityCheckIntervalId = null;
        }
    }
}
