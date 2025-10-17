
<footer class="main-footer">

  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{asset('plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- ChartJS -->
<script src="{{asset('plugins/chart.js/Chart.min.js')}}"></script>
<!-- Sparkline -->
<script src="{{asset('plugins/sparklines/sparkline.js')}}"></script>
<!-- JQVMap -->
<script src="{{asset('plugins/jqvmap/jquery.vmap.min.js')}}"></script>
<script src="{{asset('plugins/jqvmap/maps/jquery.vmap.usa.js')}}"></script>
<!-- jQuery Knob Chart -->
<script src="{{asset('plugins/jquery-knob/jquery.knob.min.js')}}"></script>
<!-- daterangepicker -->
<script src="{{asset('plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
<!-- Summernote -->
<script src="{{asset('plugins/summernote/summernote-bs4.min.js')}}"></script>
<!-- overlayScrollbars -->
<script src="{{asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('dist/js/adminlte.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{asset('dist/js/pages/dashboard.js')}}"></script>

{{--
<script src="{{ asset('assets/js/jquery.min.js') }}"></script> --}}



<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/jszip/jszip.min.js')}}"></script>
<script src="{{asset('plugins/pdfmake/pdfmake.min.js')}}"></script>
<script src="{{asset('plugins/pdfmake/vfs_fonts.js')}}"></script>
<script src="{{asset('plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{asset('plugins/datatables-buttons/js/buttons.print.min.js')}}"></script>
<script src="{{asset('plugins/datatables-buttons/js/buttons.colVis.min.js')}}"></script>

<!-- Heartbeat System -->
<script src="{{ asset('js/heartbeat.js') }}"></script>
<script>
    // Initialize heartbeat system
    document.addEventListener('DOMContentLoaded', function() {
        // Only initialize if user is authenticated
        @auth
        const heartbeat = new UserHeartbeat({
            heartbeatInterval: 45000, // 45 seconds (longer for InfinityFree)
            onlineCheckInterval: 120000, // 2 minutes (longer for InfinityFree)
            inactivityTimeout: 300000, // 5 minutes of inactivity = offline
            // Optional: Custom callback for online status updates
            onStatusUpdate: function(users) {
                // Handle online status updates
                const onlineCount = users.filter(user => user.is_online).length;
                const onlineCountElement = document.getElementById('online-users-count');
                if (onlineCountElement) {
                    onlineCountElement.textContent = onlineCount;
                }

                // Update status icon based on current user activity
                updateStatusIcon();

                // Dispatch a custom event that other pages can listen to
                document.dispatchEvent(new CustomEvent('userStatusUpdate', { 
                    detail: { users } 
                }));
            }
        });
        
        // Enhanced inactivity and activity tracking
        let isUserActive = true;
        let lastActivityTime = Date.now();
        let inactivityTimer = null;
        let statusIcon = document.getElementById('online-status-icon');
        
        // Function to update status icon
        function updateStatusIcon() {
            if (!statusIcon) return;
            
            const now = Date.now();
            const timeSinceLastActivity = now - lastActivityTime;
            
            if (timeSinceLastActivity > 300000) { // 5 minutes
                statusIcon.className = 'fas fa-circle text-warning';
                statusIcon.title = 'Inactive (Away)';
                isUserActive = false;
            } else if (timeSinceLastActivity > 60000) { // 1 minute
                statusIcon.className = 'fas fa-circle text-info';
                statusIcon.title = 'Idle';
                isUserActive = true;
            } else {
                statusIcon.className = 'fas fa-circle text-success';
                statusIcon.title = 'Active';
                isUserActive = true;
            }
        }
        
        // Function to track user activity
        function trackActivity() {
            lastActivityTime = Date.now();
            isUserActive = true;
            updateStatusIcon();
            
            // Clear existing timer
            if (inactivityTimer) {
                clearTimeout(inactivityTimer);
            }
            
            // Set new inactivity timer
            inactivityTimer = setTimeout(function() {
                isUserActive = false;
                updateStatusIcon();
            }, 300000); // 5 minutes
        }
        
        // Enhanced activity event listeners
        const activityEvents = [
            'mousedown', 'mousemove', 'keydown', 'keyup', 'keypress',
            'scroll', 'touchstart', 'touchmove', 'touchend',
            'click', 'dblclick', 'wheel', 'focus', 'blur'
        ];
        
        activityEvents.forEach(event => {
            document.addEventListener(event, trackActivity, true);
        });
        
        // Handle tab visibility changes (when user switches tabs or minimizes)
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                trackActivity();
            } else {
                // When tab becomes hidden, mark as less active but not offline
                setTimeout(updateStatusIcon, 1000);
            }
        });
        
        // Handle page unload (browser close, navigation away)
        window.addEventListener('beforeunload', function() {
            // Send final heartbeat marking user as offline
            if (heartbeat) {
                heartbeat.isActive = false;
                
                // For InfinityFree - try multiple methods
                try {
                    // Method 1: sendBeacon (preferred)
                    const formData = new FormData();
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                    formData.append('is_active', 'false');
                    formData.append('last_activity', new Date().toISOString());
                    
                    if (navigator.sendBeacon) {
                        navigator.sendBeacon('/api/heartbeat', formData);
                    } else {
                        // Method 2: Synchronous XHR (fallback)
                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', '/api/heartbeat', false);
                        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhr.send('is_active=false&last_activity=' + encodeURIComponent(new Date().toISOString()));
                    }
                } catch (error) {
                    console.warn('Failed to send final heartbeat:', error);
                }
            }
        });
        
        // Handle browser/tab close detection
        window.addEventListener('pagehide', function() {
            if (heartbeat) {
                heartbeat.isActive = false;
                
                try {
                    const formData = new FormData();
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                    formData.append('is_active', 'false');
                    formData.append('last_activity', new Date().toISOString());
                    
                    if (navigator.sendBeacon) {
                        navigator.sendBeacon('/api/heartbeat', formData);
                    }
                } catch (error) {
                    console.warn('Failed to send pagehide heartbeat:', error);
                }
            }
        });
        
        // Start the heartbeat system
        heartbeat.start();
        
        // Initialize activity tracking
        trackActivity();
        
        // Handle network status changes
        window.addEventListener('online', function() {
            if (statusIcon) {
                statusIcon.className = 'fas fa-circle text-success';
                statusIcon.title = 'Back Online';
            }
            // Restart heartbeat when coming back online
            if (heartbeat) {
                heartbeat.start();
            }
        });

        window.addEventListener('offline', function() {
            if (statusIcon) {
                statusIcon.className = 'fas fa-circle text-danger';
                statusIcon.title = 'No Internet Connection';
            }
        });
        
        // Update status every minute
        setInterval(updateStatusIcon, 60000);
        
        @endauth
    });
</script>

<!-- Heartbeat Status Styles -->
<style>
    #online-status-icon {
        font-size: 8px;
        margin-right: 5px;
        transition: all 0.3s ease;
    }
    
    /* Active status - green with pulse */
    #online-status-icon.text-success {
        color: #28a745 !important;
        animation: pulse 2s infinite;
    }
    
    /* Idle status - blue, slower pulse */
    #online-status-icon.text-info {
        color: #17a2b8 !important;
        animation: pulse-slow 3s infinite;
    }
    
    /* Inactive/Away status - orange, no pulse */
    #online-status-icon.text-warning {
        color: #ffc107 !important;
        animation: none;
    }
    
    /* Offline status - red, no pulse */
    #online-status-icon.text-danger {
        color: #dc3545 !important;
        animation: none;
    }
    
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    
    @keyframes pulse-slow {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }
    
    /* Tooltip styling */
    #online-status-icon[title]:hover::after {
        content: attr(title);
        position: absolute;
        background: #333;
        color: white;
        padding: 5px 8px;
        border-radius: 4px;
        font-size: 12px;
        white-space: nowrap;
        z-index: 1000;
        top: 30px;
        left: 0;
    }
</style>

</body>
</html>




