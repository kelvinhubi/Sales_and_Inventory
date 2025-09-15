<x-app-layout>
    <!-- Add this near the top of your user pages -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const heartbeat = new UserHeartbeat({
                heartbeatInterval: 30000, // 30 seconds
                heartbeatEndpoint: '/api/heartbeat',
            });
            heartbeat.start();
        });
    </script>

    <!-- Rest of your page content -->
</x-app-layout>
