@props(['userId', 'initialStatus' => false])

<span 
    class="badge badge-{{ $initialStatus ? 'success' : 'secondary' }} online-status-badge"
    data-user-id="{{ $userId }}"
>
    {{ $initialStatus ? 'Online' : 'Offline' }}
</span>

<script>
    document.addEventListener('userStatusUpdate', function(event) {
        const userId = {{ $userId }};
        const users = event.detail.users;
        const userStatus = users.find(u => u.id === userId);
        
        if (userStatus) {
            const badge = document.querySelector(`.online-status-badge[data-user-id="${userId}"]`);
            if (badge) {
                badge.className = `badge badge-${userStatus.is_online ? 'success' : 'secondary'} online-status-badge`;
                badge.textContent = userStatus.is_online ? 'Online' : 'Offline';
            }
        }
    });
</script>
