<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Live Broadcasting - RKAN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            overflow-x: hidden;
        }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stream-container { aspect-ratio: 16/9; }
        .chat-container { max-height: 400px; }
        .message-animation { animation: slideIn 0.3s ease-out; }
        @keyframes slideIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .pulse-live { animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
        .slide-up { animation: slideUp 0.3s ease-out; }
        @keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
        .heart-animation { animation: heartBeat 0.6s ease-in-out; }
        @keyframes heartBeat { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.2); } }
        
        /* Mobile-specific styles */
        @media (max-width: 768px) {
            .mobile-fullscreen { height: 100vh; }
            .mobile-overlay { background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0.7) 100%); }
        }
    </style>
</head>
<body class="bg-black text-white">
    <!-- Mobile Header -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-b from-black/50 to-transparent px-4 py-3">
        <div class="flex items-center justify-between">
            <!-- Back Button -->
            <a href="/dashboard" class="text-white hover:text-gray-300 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            
            <!-- Live Indicator -->
            <div class="live-indicator hidden flex items-center space-x-2 bg-red-600 px-3 py-1 rounded-full">
                <div class="w-2 h-2 bg-white rounded-full pulse-live"></div>
                <span class="text-sm font-medium">LIVE</span>
            </div>
            
            <!-- Settings -->
            <button class="settings-btn text-white hover:text-gray-300 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </button>
        </div>
    </header>

    <!-- Main Stream Container -->
    <div class="mobile-fullscreen relative bg-black">
        <!-- Video Stream -->
        <video id="videoStream" class="w-full h-full object-cover" autoplay muted playsinline></video>
        
        <!-- Mobile Overlay -->
        <div class="mobile-overlay absolute inset-0"></div>
        
        <!-- Stream Info (Top Left) -->
        <div class="absolute top-16 left-4 max-w-xs">
            <h2 class="stream-title text-xl font-semibold font-poppins mb-1">My Live Stream</h2>
            <p class="stream-description text-sm text-gray-300 font-poppins">Welcome to my live stream!</p>
        </div>
        
        <!-- Viewer Count (Top Right) -->
        <div class="absolute top-16 right-4 flex items-center space-x-2 bg-black/30 backdrop-blur-sm px-3 py-1 rounded-full">
            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
            </svg>
            <span class="viewer-count text-sm text-white font-medium">0</span>
        </div>

        <!-- Right Side Actions (TikTok Style) -->
        <div class="absolute right-4 bottom-32 flex flex-col space-y-6">
            <!-- Profile Avatar -->
            <div class="flex flex-col items-center space-y-2">
                <div class="w-12 h-12 bg-gradient-to-r from-pink-500 to-purple-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                    {{ Auth::user()->name[0] ?? 'U' }}
                </div>
                <div class="w-1 h-8 bg-white rounded-full"></div>
            </div>
            
            <!-- Like Button -->
            <button class="like-btn flex flex-col items-center space-y-1">
                <div class="w-12 h-12 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center transition-colors duration-200">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                </div>
                <span class="text-xs text-white font-poppins">Like</span>
            </button>
            
            <!-- Share Button -->
            <button class="share-btn flex flex-col items-center space-y-1">
                <div class="w-12 h-12 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center transition-colors duration-200">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92-1.31-2.92-2.92-2.92z"/>
                    </svg>
                </div>
                <span class="text-xs text-white font-poppins">Share</span>
            </button>
            
            <!-- Gift Button -->
            <button class="gift-btn flex flex-col items-center space-y-1">
                <div class="w-12 h-12 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center transition-colors duration-200">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                    </svg>
                </div>
                <span class="text-xs text-white font-poppins">Gift</span>
            </button>
            
            <!-- Refresh Button -->
            <button class="refresh-btn flex flex-col items-center space-y-1">
                <div class="w-12 h-12 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center transition-colors duration-200">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </div>
                <span class="text-xs text-white font-poppins">Refresh</span>
            </button>
        </div>

        <!-- Bottom Controls -->
        <div class="absolute bottom-0 left-0 right-0 p-4">
            <!-- Stream Control Button -->
            <div class="flex justify-center mb-4">
                <button class="stream-control-btn bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-full font-medium transition-colors duration-200 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                    <span>Start Stream</span>
                </button>
            </div>
            
            <!-- Chat Input -->
            <div class="flex items-center space-x-3 bg-black/30 backdrop-blur-sm rounded-full px-4 py-2">
                <input type="text" placeholder="Add a comment..." class="chat-input flex-1 bg-transparent text-white placeholder-gray-300 focus:outline-none font-poppins text-sm">
                <button class="send-btn text-white hover:text-gray-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Chat Panel (Slide Up) -->
    <div id="chatPanel" class="fixed bottom-0 left-0 right-0 bg-gray-900 rounded-t-3xl transform translate-y-full transition-transform duration-300 z-40 max-h-96">
        <!-- Chat Handle -->
        <div class="flex justify-center pt-3 pb-2">
            <div class="w-12 h-1 bg-gray-600 rounded-full"></div>
        </div>
        
        <!-- Chat Header -->
        <div class="px-4 py-3 border-b border-gray-800">
            <h3 class="text-lg font-semibold font-poppins">Live Chat</h3>
            <p class="text-sm text-gray-400 font-poppins">Join the conversation</p>
        </div>

        <!-- Chat Messages -->
        <div class="chat-messages flex-1 overflow-y-auto px-4 py-3 space-y-3 max-h-64">
            <!-- Welcome Message -->
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                    RK
                </div>
                <div class="flex-1">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-normal text-white font-poppins">RKAN System</span>
                        <span class="text-xs text-gray-400 font-poppins">now</span>
                    </div>
                    <p class="text-sm text-gray-300 font-poppins">Welcome to the live stream! Start chatting with everyone.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Toggle Button -->
    <button id="chatToggle" class="fixed bottom-20 right-4 w-12 h-12 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center transition-colors duration-200 z-30">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
    </button>

    <!-- Settings Modal -->
    <div id="settingsModal" class="fixed inset-0 bg-black/50 hidden z-50">
        <div class="flex items-end justify-center min-h-screen p-4">
            <div class="bg-gray-800 rounded-t-3xl p-6 w-full max-w-md slide-up">
                <!-- Modal Handle -->
                <div class="flex justify-center mb-4">
                    <div class="w-12 h-1 bg-gray-600 rounded-full"></div>
                </div>
                
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold font-poppins">Stream Settings</h3>
                    <button class="close-settings text-gray-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2 font-poppins">Stream Title</label>
                        <input type="text" id="streamTitle" value="My Live Stream" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-blue-500 font-poppins">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2 font-poppins">Description</label>
                        <textarea id="streamDescription" rows="3" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-blue-500 font-poppins">Welcome to my live stream!</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2 font-poppins">Camera</label>
                        <select id="cameraSelect" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-blue-500 font-poppins">
                            <option value="">Loading cameras...</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex space-x-3 mt-6">
                    <button class="save-settings flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-medium transition-colors duration-200 font-poppins">Save Settings</button>
                    <button class="close-settings flex-1 bg-gray-600 hover:bg-gray-700 text-white py-3 rounded-lg font-medium transition-colors duration-200 font-poppins">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Broadcasting JavaScript -->
    <script src="/js/broadcasting.js"></script>
    <script>
        // Mobile-specific interactions
        document.addEventListener('DOMContentLoaded', function() {
            const chatToggle = document.getElementById('chatToggle');
            const chatPanel = document.getElementById('chatPanel');
            let isChatOpen = false;

            // Chat toggle functionality
            chatToggle.addEventListener('click', function() {
                isChatOpen = !isChatOpen;
                if (isChatOpen) {
                    chatPanel.classList.remove('translate-y-full');
                    chatToggle.innerHTML = `
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    `;
                } else {
                    chatPanel.classList.add('translate-y-full');
                    chatToggle.innerHTML = `
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    `;
                }
            });

            // Close chat when clicking outside
            chatPanel.addEventListener('click', function(e) {
                if (e.target === chatPanel) {
                    isChatOpen = false;
                    chatPanel.classList.add('translate-y-full');
                    chatToggle.innerHTML = `
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    `;
                }
            });

            // Prevent body scroll when chat is open
            chatPanel.addEventListener('touchmove', function(e) {
                if (isChatOpen) {
                    e.stopPropagation();
                }
            });
        });
    </script>
</body>
</html> 