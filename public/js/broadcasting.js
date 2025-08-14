class LiveBroadcasting {
    constructor() {
        this.streamId = null;
        this.isLive = false;
        this.viewerCount = 0;
        this.messages = [];
        this.stream = null;
        this.cameras = [];
        this.selectedCamera = null;
        this.messageInterval = null;
        this.viewerInterval = null;
        this.isChatOpen = false;
        
        this.init();
    }

    async init() {
        this.setupEventListeners();
        await this.loadCameras();
        this.loadMessages();
        this.updateViewerCount('join');
        this.startAutoRefresh();
        this.setupMobileInteractions();
    }

    setupEventListeners() {
        // Start/Stop Stream Button
        const streamButton = document.querySelector('.stream-control-btn');
        if (streamButton) {
            streamButton.addEventListener('click', () => {
                if (this.isLive) {
                    this.stopStream();
                } else {
                    this.startStream();
                }
            });
        }

        // Chat Input
        const chatInput = document.querySelector('.chat-input');
        const sendButton = document.querySelector('.send-btn');
        
        if (chatInput && sendButton) {
            sendButton.addEventListener('click', () => {
                this.sendMessage(chatInput.value);
                chatInput.value = '';
            });

            chatInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.sendMessage(chatInput.value);
                    chatInput.value = '';
                }
            });
        }

        // Quick Actions
        const likeBtn = document.querySelector('.like-btn');
        const shareBtn = document.querySelector('.share-btn');
        const giftBtn = document.querySelector('.gift-btn');
        const refreshBtn = document.querySelector('.refresh-btn');
        const settingsBtn = document.querySelector('.settings-btn');

        if (likeBtn) likeBtn.addEventListener('click', () => this.handleLike());
        if (shareBtn) shareBtn.addEventListener('click', () => this.handleShare());
        if (giftBtn) giftBtn.addEventListener('click', () => this.handleGift());
        if (refreshBtn) refreshBtn.addEventListener('click', () => this.handleRefresh());
        if (settingsBtn) settingsBtn.addEventListener('click', () => this.openSettings());

        // Settings Modal
        const closeSettings = document.querySelectorAll('.close-settings');
        const saveSettings = document.querySelector('.save-settings');
        const settingsModal = document.getElementById('settingsModal');

        closeSettings.forEach(btn => {
            btn.addEventListener('click', () => {
                settingsModal.classList.add('hidden');
            });
        });

        if (saveSettings) {
            saveSettings.addEventListener('click', () => this.saveSettings());
        }

        // Close modal when clicking outside
        settingsModal.addEventListener('click', (e) => {
            if (e.target === settingsModal) {
                settingsModal.classList.add('hidden');
            }
        });

        // Camera selection change
        const cameraSelect = document.getElementById('cameraSelect');
        if (cameraSelect) {
            cameraSelect.addEventListener('change', (e) => {
                this.selectedCamera = e.target.value;
            });
        }
    }

    setupMobileInteractions() {
        const chatToggle = document.getElementById('chatToggle');
        const chatPanel = document.getElementById('chatPanel');

        if (chatToggle && chatPanel) {
            chatToggle.addEventListener('click', () => {
                this.toggleChat();
            });

            // Close chat when clicking outside
            chatPanel.addEventListener('click', (e) => {
                if (e.target === chatPanel) {
                    this.closeChat();
                }
            });

            // Prevent body scroll when chat is open
            chatPanel.addEventListener('touchmove', (e) => {
                if (this.isChatOpen) {
                    e.stopPropagation();
                }
            });
        }
    }

    toggleChat() {
        const chatToggle = document.getElementById('chatToggle');
        const chatPanel = document.getElementById('chatPanel');
        
        this.isChatOpen = !this.isChatOpen;
        
        if (this.isChatOpen) {
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
    }

    closeChat() {
        const chatToggle = document.getElementById('chatToggle');
        const chatPanel = document.getElementById('chatPanel');
        
        this.isChatOpen = false;
        chatPanel.classList.add('translate-y-full');
        chatToggle.innerHTML = `
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
        `;
    }

    async loadCameras() {
        try {
            // Request camera permission first
            await navigator.mediaDevices.getUserMedia({ video: true });
            
            const devices = await navigator.mediaDevices.enumerateDevices();
            this.cameras = devices.filter(device => device.kind === 'videoinput');
            
            const cameraSelect = document.getElementById('cameraSelect');
            if (cameraSelect) {
                cameraSelect.innerHTML = '';
                this.cameras.forEach((camera, index) => {
                    const option = document.createElement('option');
                    option.value = camera.deviceId;
                    option.textContent = camera.label || `Camera ${index + 1}`;
                    cameraSelect.appendChild(option);
                });
                
                if (this.cameras.length > 0) {
                    this.selectedCamera = this.cameras[0].deviceId;
                    cameraSelect.value = this.selectedCamera;
                }
            }
        } catch (error) {
            console.error('Error loading cameras:', error);
            this.showNotification('Camera access denied. Please allow camera access to start streaming.', 'error');
        }
    }

    async startStream() {
        try {
            if (!this.selectedCamera) {
                this.showNotification('Please select a camera first', 'error');
                return;
            }

            // Get camera stream
            const stream = await navigator.mediaDevices.getUserMedia({
                video: { 
                    deviceId: this.selectedCamera ? { exact: this.selectedCamera } : undefined,
                    width: { ideal: 1920 },
                    height: { ideal: 1080 }
                },
                audio: true
            });

            // Display video stream
            const videoElement = document.getElementById('videoStream');
            if (videoElement) {
                videoElement.srcObject = stream;
                this.stream = stream;
            }

            // Start backend stream
            const response = await fetch('/broadcasting/start', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    title: document.getElementById('streamTitle')?.value || 'My Live Stream',
                    description: document.getElementById('streamDescription')?.value || 'Welcome to my live stream!',
                    camera_device: this.selectedCamera
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.streamId = data.stream_id;
                this.isLive = true;
                this.updateUI();
                this.showNotification('🎥 Stream started successfully!', 'success');
                
                // Update stream info
                this.updateStreamInfo();
                
                // Start real-time updates
                this.startRealTimeUpdates();
            }
        } catch (error) {
            console.error('Error starting stream:', error);
            this.showNotification('Failed to start stream: ' + error.message, 'error');
        }
    }

    async stopStream() {
        try {
            // Stop video stream
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
                this.stream = null;
            }

            const videoElement = document.getElementById('videoStream');
            if (videoElement) {
                videoElement.srcObject = null;
            }

            // Stop backend stream
            if (this.streamId) {
                const response = await fetch('/broadcasting/stop', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        stream_id: this.streamId
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    this.isLive = false;
                    this.streamId = null;
                    this.updateUI();
                    this.stopRealTimeUpdates();
                    this.showNotification('⏹️ Stream stopped successfully!', 'success');
                }
            }
        } catch (error) {
            console.error('Error stopping stream:', error);
            this.showNotification('Failed to stop stream', 'error');
        }
    }

    async sendMessage(message) {
        if (!message.trim()) return;

        try {
            const response = await fetch('/broadcasting/message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    message: message,
                    stream_id: this.streamId || 'demo'
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.addMessageToUI(data.message);
            }
        } catch (error) {
            console.error('Error sending message:', error);
            // Add message locally for demo
            this.addMessageToUI({
                id: Date.now(),
                user_name: 'You',
                message: message,
                timestamp: new Date().toISOString(),
                avatar: this.generateAvatar('You'),
                type: 'message'
            });
        }
    }

    async loadMessages() {
        try {
            const response = await fetch(`/broadcasting/messages?stream_id=${this.streamId || 'demo'}`);
            const data = await response.json();
            
            if (data.success) {
                this.messages = data.messages;
                this.renderMessages();
            }
        } catch (error) {
            console.error('Error loading messages:', error);
        }
    }

    async updateViewerCount(action) {
        try {
            const response = await fetch('/broadcasting/viewers', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    stream_id: this.streamId || 'demo',
                    action: action
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.viewerCount = data.viewers;
                this.updateViewerDisplay();
            }
        } catch (error) {
            console.error('Error updating viewer count:', error);
            // Simulate viewer count for demo
            this.viewerCount = Math.floor(Math.random() * 100) + 1;
            this.updateViewerDisplay();
        }
    }

    async handleInteraction(type) {
        try {
            const response = await fetch('/broadcasting/interaction', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    type: type,
                    stream_id: this.streamId || 'demo'
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.showInteractionNotification(type);
            }
        } catch (error) {
            console.error('Error handling interaction:', error);
            this.showInteractionNotification(type);
        }
    }

    startRealTimeUpdates() {
        // Update messages every 3 seconds
        this.messageInterval = setInterval(() => {
            this.loadMessages();
        }, 3000);

        // Update viewer count every 5 seconds
        this.viewerInterval = setInterval(() => {
            this.updateViewerCount('join');
        }, 5000);
    }

    stopRealTimeUpdates() {
        if (this.messageInterval) {
            clearInterval(this.messageInterval);
            this.messageInterval = null;
        }
        if (this.viewerInterval) {
            clearInterval(this.viewerInterval);
            this.viewerInterval = null;
        }
    }

    startAutoRefresh() {
        // Auto refresh messages every 10 seconds when not live
        setInterval(() => {
            if (!this.isLive) {
                this.loadMessages();
                this.updateViewerCount('join');
            }
        }, 10000);
    }

    updateUI() {
        const streamButton = document.querySelector('.stream-control-btn');
        const liveIndicator = document.querySelector('.live-indicator');
        
        if (streamButton) {
            if (this.isLive) {
                streamButton.innerHTML = `
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                    </svg>
                    <span>Stop Stream</span>
                `;
                streamButton.classList.remove('bg-green-600', 'hover:bg-green-700');
                streamButton.classList.add('bg-red-600', 'hover:bg-red-700');
            } else {
                streamButton.innerHTML = `
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                    <span>Start Stream</span>
                `;
                streamButton.classList.remove('bg-red-600', 'hover:bg-red-700');
                streamButton.classList.add('bg-green-600', 'hover:bg-green-700');
            }
        }

        if (liveIndicator) {
            liveIndicator.classList.toggle('hidden', !this.isLive);
        }
    }

    updateStreamInfo() {
        const title = document.getElementById('streamTitle')?.value || 'My Live Stream';
        const description = document.getElementById('streamDescription')?.value || 'Welcome to my live stream!';
        
        const titleElement = document.querySelector('.stream-title');
        const descElement = document.querySelector('.stream-description');
        
        if (titleElement) titleElement.textContent = title;
        if (descElement) descElement.textContent = description;
    }

    addMessageToUI(message) {
        const chatContainer = document.querySelector('.chat-messages');
        if (!chatContainer) return;

        const messageElement = this.createMessageElement(message);
        messageElement.classList.add('message-animation');
        chatContainer.appendChild(messageElement);
        chatContainer.scrollTop = chatContainer.scrollHeight;

        // Keep only last 50 messages in DOM
        const messages = chatContainer.children;
        if (messages.length > 50) {
            chatContainer.removeChild(messages[0]);
        }
    }

    createMessageElement(message) {
        const div = document.createElement('div');
        div.className = 'flex items-start space-x-3';
        div.innerHTML = `
            <div class="w-8 h-8 bg-gradient-to-r ${message.avatar.color} rounded-full flex items-center justify-center text-white text-xs font-bold">
                ${message.avatar.initials}
            </div>
            <div class="flex-1">
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-normal text-white font-poppins">${message.user_name}</span>
                    <span class="text-xs text-gray-400 font-poppins">${this.formatTime(message.timestamp)}</span>
                </div>
                <p class="text-sm text-gray-300 font-poppins">${this.escapeHtml(message.message)}</p>
            </div>
        `;
        return div;
    }

    renderMessages() {
        const chatContainer = document.querySelector('.chat-messages');
        if (!chatContainer) return;

        chatContainer.innerHTML = '';
        this.messages.forEach(message => {
            this.addMessageToUI(message);
        });
    }

    updateViewerDisplay() {
        const viewerElement = document.querySelector('.viewer-count');
        if (viewerElement) {
            viewerElement.textContent = this.formatNumber(this.viewerCount);
        }
    }

    formatTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diff = Math.floor((now - date) / 1000);

        if (diff < 60) return 'now';
        if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
        return `${Math.floor(diff / 3600)}h ago`;
    }

    formatNumber(num) {
        if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
        if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
        return num.toString();
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    generateAvatar(name) {
        const colors = [
            'from-pink-500 to-purple-500',
            'from-blue-500 to-green-500',
            'from-yellow-500 to-orange-500',
            'from-red-500 to-pink-500',
            'from-green-500 to-blue-500',
            'from-purple-500 to-pink-500'
        ];
        
        const color = colors[Math.floor(Math.random() * colors.length)];
        const initials = name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
        
        return {
            initials: initials || 'U',
            color: color
        };
    }

    handleLike() {
        this.handleInteraction('like');
        // Add like animation with heart effect
        const likeBtn = document.querySelector('.like-btn');
        if (likeBtn) {
            likeBtn.classList.add('heart-animation');
            setTimeout(() => likeBtn.classList.remove('heart-animation'), 600);
        }
    }

    handleShare() {
        this.handleInteraction('share');
        if (navigator.share) {
            navigator.share({
                title: 'Live Stream',
                url: window.location.href
            });
        } else {
            // Fallback: copy to clipboard
            navigator.clipboard.writeText(window.location.href).then(() => {
                this.showNotification('Link copied to clipboard!', 'success');
            });
        }
    }

    handleGift() {
        this.handleInteraction('gift');
        // Add gift animation
        const giftBtn = document.querySelector('.gift-btn');
        if (giftBtn) {
            giftBtn.classList.add('scale-110');
            setTimeout(() => giftBtn.classList.remove('scale-110'), 200);
        }
    }

    handleRefresh() {
        this.showNotification('🔄 Refreshing...', 'info');
        setTimeout(() => {
            this.loadMessages();
            this.updateViewerCount('join');
            this.showNotification('Stream refreshed!', 'success');
        }, 1000);
    }

    openSettings() {
        const modal = document.getElementById('settingsModal');
        modal.classList.remove('hidden');
    }

    saveSettings() {
        const title = document.getElementById('streamTitle')?.value;
        const description = document.getElementById('streamDescription')?.value;
        const camera = document.getElementById('cameraSelect')?.value;
        
        if (camera) {
            this.selectedCamera = camera;
        }
        
        this.updateStreamInfo();
        this.showNotification('Settings saved!', 'success');
        
        const modal = document.getElementById('settingsModal');
        modal.classList.add('hidden');
    }

    showInteractionNotification(type) {
        const messages = {
            'like': '❤️ Liked!',
            'share': '📤 Shared!',
            'gift': '🎁 Gift sent!'
        };
        
        this.showNotification(messages[type] || 'Interaction recorded!', 'success');
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg text-white font-poppins text-sm transition-all duration-300 transform translate-x-full`;
        
        switch (type) {
            case 'success':
                notification.classList.add('bg-green-600');
                break;
            case 'error':
                notification.classList.add('bg-red-600');
                break;
            default:
                notification.classList.add('bg-blue-600');
        }

        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);

        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.liveBroadcasting = new LiveBroadcasting();
});

// Handle page unload
window.addEventListener('beforeunload', () => {
    if (window.liveBroadcasting && window.liveBroadcasting.isLive) {
        window.liveBroadcasting.updateViewerCount('leave');
    }
}); 