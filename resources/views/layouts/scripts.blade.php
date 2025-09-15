<!-- Common Scripts -->
<script src="{{ asset('js/heartbeat.js') }}"></script>
<script>
    // Initialize heartbeat system
    document.addEventListener('DOMContentLoaded', function() {
        // Only initialize if user is authenticated
        @auth
        const heartbeat = new UserHeartbeat({
            // Optional: Custom callback for online status updates
            onStatusUpdate: function(users) {
                // You can handle online status updates here if needed
                // For example, update UI elements showing online status
                const onlineCount = users.filter(user => user.is_online).length;
                const onlineCountElement = document.getElementById('online-users-count');
                if (onlineCountElement) {
                    onlineCountElement.textContent = onlineCount;
                }

                // Dispatch a custom event that other pages can listen to
                document.dispatchEvent(new CustomEvent('userStatusUpdate', { 
                    detail: { users } 
                }));
            }
        });
        
        heartbeat.start();
        @endauth
    });
</script>

<!-- Optional: Add this if you want to show online users count -->
<div id="online-users-count" class="d-none">0</div>
