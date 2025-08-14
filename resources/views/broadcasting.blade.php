<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        @if($mode === 'broadcast')
            Start Live Stream - RKAN
        @elseif($mode === 'view')
            Watch Live Stream - RKAN
        @else
            Join Live Stream - RKAN
        @endif
    </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            overflow-x: hidden;
        }
        .mobile-fullscreen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 10;
        }
        .mobile-overlay {
            background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0.7) 100%);
        }
        .slide-up {
            animation: slideUp 0.3s ease-out;
        }
        @keyframes slideUp {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
        }
        .heart-animation {
            animation: heartbeat 0.6s ease-in-out;
        }
        @keyframes heartbeat {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }
        @media (max-width: 768px) {
            .mobile-fullscreen {
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                z-index: 10;
            }
            .mobile-overlay {
                background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0.7) 100%);
            }
        }
    </style>
</head>
<body class="bg-black min-h-screen">

    @if($mode === 'broadcast')
        <!-- BROADCAST MODE - Start New Stream -->
        @include('broadcasting.broadcast-mode')
    @elseif($mode === 'view')
        <!-- VIEW MODE - Watch Live Stream -->
        @include('broadcasting.view-mode')
    @else
        <!-- JOIN MODE - Join Existing Stream -->
        @include('broadcasting.join-mode')
    @endif

    <script src="/js/broadcasting.js"></script>
    <script>
        // Initialize based on mode
        document.addEventListener('DOMContentLoaded', () => {
            const mode = '{{ $mode }}';
            const streamId = '{{ $stream_id ?? "" }}';
            
            if (window.liveBroadcasting) {
                window.liveBroadcasting.setMode(mode, streamId);
            }
        });
    </script>
</body>
</html> 