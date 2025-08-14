<!-- JOIN MODE - Join Existing Stream -->
<!-- Mobile Header -->
<header class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-b from-black/50 to-transparent px-4 py-3">
    <div class="flex items-center justify-between">
        <!-- Back Button -->
        <a href="/dashboard" class="text-white hover:text-gray-300 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        
        <!-- Join Status -->
        <div class="flex items-center space-x-2 bg-blue-600 px-3 py-1 rounded-full">
            <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
            <span class="text-sm font-medium">JOINING</span>
        </div>
        
        <!-- Close -->
        <button class="close-join text-white hover:text-gray-300 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</header>

<!-- Main Stream Container -->
<div class="mobile-fullscreen relative bg-black">
    <!-- Video Stream (will be loaded from broadcaster) -->
    <div id="videoContainer" class="w-full h-full bg-gray-900 flex items-center justify-center">
        <div class="text-center">
            <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="text-white text-lg font-medium">Joining stream...</p>
            <p class="text-gray-400 text-sm mt-2">Please wait while we connect you</p>
        </div>
    </div>
    
    <!-- Mobile Overlay -->
    <div class="mobile-overlay absolute inset-0"></div>
    
    <!-- Stream Info (Top Left) -->
    <div class="absolute top-16 left-4 max-w-xs">
        <h2 class="stream-title text-xl font-semibold font-poppins mb-1">Loading...</h2>
        <p class="stream-description text-sm text-gray-300 font-poppins">Loading stream info...</p>
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
        <!-- Broadcaster Avatar -->
        <div class="flex flex-col items-center space-y-2">
            <div id="broadcasterAvatar" class="w-12 h-12 bg-gradient-to-r from-red-500 to-pink-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                ?
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
        
        <!-- Follow Button -->
        <button class="follow-btn flex flex-col items-center space-y-1">
            <div class="w-12 h-12 bg-red-600 hover:bg-red-700 rounded-full flex items-center justify-center transition-colors duration-200">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
            </div>
            <span class="text-xs text-white font-poppins">Follow</span>
        </button>
    </div>

    <!-- Bottom Controls -->
    <div class="absolute bottom-0 left-0 right-0 p-4">
        <!-- Join Button -->
        <div class="flex justify-center mb-4">
            <button class="join-stream-btn bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-full font-medium transition-colors duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
                <span>Join Stream</span>
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
        <!-- Loading Message -->
        <div class="flex items-start space-x-3">
            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                RK
            </div>
            <div class="flex-1">
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-normal text-white font-poppins">RKAN System</span>
                    <span class="text-xs text-gray-400 font-poppins">now</span>
                </div>
                <p class="text-sm text-gray-300 font-poppins">Connecting to live chat...</p>
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

<!-- Stream Not Found Modal -->
<div id="streamNotFoundModal" class="fixed inset-0 bg-black/50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md text-center">
            <div class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold font-poppins text-white mb-2">Stream Not Found</h3>
            <p class="text-gray-300 font-poppins mb-6">This live stream is no longer available or has ended.</p>
            <div class="flex space-x-3">
                <a href="/dashboard" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-medium transition-colors duration-200 font-poppins">Back to Dashboard</a>
            </div>
        </div>
    </div>
</div> 