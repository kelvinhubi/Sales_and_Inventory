<!-- Example user page -->
@extends('layouts.app')

@section('content')
    <!-- Include the heartbeat script -->
    <script src="{{ asset('js/user-heartbeat.js') }}"></script>

    <!-- Initialize the heartbeat -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const heartbeat = new UserHeartbeat();
            heartbeat.start();
        });
    </script>

    <!-- Your page content here -->
    <div class="container">
        <!-- ... -->
    </div>
@endsection
