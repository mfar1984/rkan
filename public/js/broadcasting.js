class LiveBroadcasting {
    constructor() {
        this.isLive = false;
        this.streamId = null;
        this.stream = null;
        this.selectedCamera = null;
        this.messageInterval = null;
        this.viewerInterval = null;
        this.isChatOpen = false;
        this.mode = 'broadcast'; // broadcast, view, join
        this.targetStreamId = null;
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupMobileInteractions();
        this.loadCameras();
    }

    setMode(mode, streamId = null) {
        this.mode = mode;
        this.targetStreamId = streamId;
        
        if (mode === 'view' || mode === 'join') {
            this.loadStreamInfo();
            this.startViewerMode();
        } else {
            this.startAutoRefresh();
        }
    }

    setupEventListeners() {
        // Stream Control
        const streamControlBtn = document.querySelector('.stream-control-btn');
        if (streamControlBtn) {
            streamControlBtn.addEventListener('click', () => {
                if (this.isLive) {
                    this.stopStream();
                } else {
                    this.startStream();
                }
            });
        }

        // Join Stream Button
        const joinStreamBtn = document.querySelector('.join-stream-btn');
        if (joinStreamBtn) {
            joinStreamBtn.addEventListener('click', () => {
                this.joinStream();
            });
        }

        // Chat
        const sendBtn = document.querySelector('.send-btn');
        const chatInput = document.querySelector('.chat-input');
        
        if (sendBtn && chatInput) {
            sendBtn.addEventListener('click', () => this.sendMessage());
            chatInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.sendMessage();
                }
            });
        }

        // Interactions
        const likeBtn = document.querySelector('.like-btn');
        const shareBtn = document.querySelector('.share-btn');
        const giftBtn = document.querySelector('.gift-btn');
        const refreshBtn = document.querySelector('.refresh-btn');
        const followBtn = document.querySelector('.follow-btn');

        if (likeBtn) likeBtn.addEventListener('click', () => this.handleLike());
        if (shareBtn) shareBtn.addEventListener('click', () => this.handleShare());
        if (giftBtn) giftBtn.addEventListener('click', () => this.handleGift());
        if (refreshBtn) refreshBtn.addEventListener('click', () => this.handleRefresh());
        if (followBtn) followBtn.addEventListener('click', () => this.handleFollow());

        // Settings
        const settingsBtn = document.querySelector('.settings-btn');
        const settingsModal = document.getElementById('settingsModal');
        const closeSettingsBtns = document.querySelectorAll('.close-settings');
        const saveSettingsBtn = document.querySelector('.save-settings');

        if (settingsBtn) {
            settingsBtn.addEventListener('click', () => {
                settingsModal.classList.remove('hidden');
            });
        }

        closeSettingsBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                settingsModal.classList.add('hidden');
            });
        });

        if (saveSettingsBtn) {
            saveSettingsBtn.addEventListener('click', () => this.saveSettings());
        }

        // Close modals when clicking outside
        if (settingsModal) {
            settingsModal.addEventListener('click', (e) => {
                if (e.target === settingsModal) {
                    settingsModal.classList.add('hidden');
                }
            });
        }

        // Close join
        const closeJoinBtn = document.querySelector('.close-join');
        if (closeJoinBtn) {
            closeJoinBtn.addEventListener('click', () => {
                window.location.href = '/dashboard';
            });
        }
    }

    async loadStreamInfo() {
        if (!this.targetStreamId) return;

        try {
            const response = await fetch(`/broadcasting/info?stream_id=${this.targetStreamId}`);
            const data = await response.json();
            
            if (data.success) {
                this.updateStreamInfo(data.stream_info);
            } else {
                this.showStreamNotFound();
            }
        } catch (error) {
            console.error('Error loading stream info:', error);
            this.showStreamNotFound();
        }
    }

    updateStreamInfo(streamInfo) {
        // Update stream title and description
        const streamTitle = document.querySelector('.stream-title');
        const streamDescription = document.querySelector('.stream-description');
        const broadcasterAvatar = document.getElementById('broadcasterAvatar');

        if (streamTitle) streamTitle.textContent = streamInfo.title || 'Live Stream';
        if (streamDescription) streamDescription.textContent = streamInfo.description || 'Welcome to the live stream!';
        
        if (broadcasterAvatar && streamInfo.user_name) {
            broadcasterAvatar.textContent = streamInfo.user_name.charAt(0).toUpperCase();
        }

        // Update viewer count
        this.updateViewerCount(streamInfo.viewers || 1);
    }

    startViewerMode() {
        // Start polling for stream updates
        this.viewerInterval = setInterval(() => {
            this.updateViewerCount();
        }, 5000);

        // Start loading messages
        this.messageInterval = setInterval(() => {
            this.loadMessages();
        }, 3000);

        // Join as viewer
        this.joinAsViewer();
    }

    async joinAsViewer() {
        if (!this.targetStreamId) return;

        try {
            const response = await fetch('/broadcasting/viewers', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    stream_id: this.targetStreamId,
                    action: 'join'
                })
            });

            const data = await response.json();
            if (data.success) {
                this.updateViewerCount(data.viewers);
            }
        } catch (error) {
            console.error('Error joining as viewer:', error);
        }
    }

    async joinStream() {
        if (this.mode === 'join') {
            // Switch to view mode
            window.location.href = `/broadcasting?view=${this.targetStreamId}`;
        }
    }

    showStreamNotFound() {
        const modal = document.getElementById('streamNotFoundModal');
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    showStreamEnded() {
        const modal = document.getElementById('streamEndedModal');
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    async startStream() {
        if (this.mode !== 'broadcast') return;

        try {
            // Request camera permission
            const stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    deviceId: this.selectedCamera ? { exact: this.selectedCamera } : undefined,
                    width: { ideal: 1920 },
                    height: { ideal: 1080 }
                },
                audio: true
            });

            this.stream = stream;
            const videoElement = document.getElementById('videoStream');
            if (videoElement) {
                videoElement.srcObject = stream;
            }

            // Get stream title and description
            const streamTitle = document.getElementById('streamTitle')?.value || 'My Live Stream';
            const streamDescription = document.getElementById('streamDescription')?.value || 'Welcome to my live stream!';

            // Start stream on server
            const response = await fetch('/broadcasting/start', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    title: streamTitle,
                    description: streamDescription,
                    camera_device: this.selectedCamera || 'default'
                })
            });

            const data = await response.json();
            if (data.success) {
                this.streamId = data.stream_id;
                this.isLive = true;
                this.updateUI();
                this.startRealTimeUpdates();
            }
        } catch (error) {
            console.error('Error starting stream:', error);
            alert('Failed to start stream. Please check camera permissions.');
        }
    }

    async stopStream() {
        if (this.mode !== 'broadcast') return;

        try {
            // Stop camera
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
                this.stream = null;
            }

            const videoElement = document.getElementById('videoStream');
            if (videoElement) {
                videoElement.srcObject = null;
            }

            // Stop stream on server
            if (this.streamId) {
                await fetch('/broadcasting/stop', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        stream_id: this.streamId
                    })
                });
            }

            this.isLive = false;
            this.streamId = null;
            this.updateUI();
            this.stopRealTimeUpdates();
        } catch (error) {
            console.error('Error stopping stream:', error);
        }
    }

    updateUI() {
        const liveIndicator = document.querySelector('.live-indicator');
        const streamControlBtn = document.querySelector('.stream-control-btn');
        const streamTitle = document.querySelector('.stream-title');
        const streamDescription = document.querySelector('.stream-description');

        if (this.isLive) {
            if (liveIndicator) liveIndicator.classList.remove('hidden');
            if (streamControlBtn) {
                streamControlBtn.innerHTML = `
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                    </svg>
                    <span>Stop Stream</span>
                `;
                streamControlBtn.className = 'stream-control-btn bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-full font-medium transition-colors duration-200 flex items-center space-x-2';
            }
        } else {
            if (liveIndicator) liveIndicator.classList.add('hidden');
            if (streamControlBtn) {
                streamControlBtn.innerHTML = `
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                    <span>Start Stream</span>
                `;
                streamControlBtn.className = 'stream-control-btn bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-full font-medium transition-colors duration-200 flex items-center space-x-2';
            }
        }
    }

    startRealTimeUpdates() {
        this.messageInterval = setInterval(() => {
            this.loadMessages();
        }, 3000);

        this.viewerInterval = setInterval(() => {
            this.updateViewerCount();
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
        // Only for broadcast mode when not live
        if (this.mode === 'broadcast' && !this.isLive) {
            setInterval(() => {
                this.loadMessages();
            }, 10000);
        }
    }

    async loadMessages() {
        const streamId = this.streamId || this.targetStreamId;
        if (!streamId) return;

        try {
            const response = await fetch(`/broadcasting/messages?stream_id=${streamId}`);
            const data = await response.json();
            
            if (data.success) {
                this.renderMessages(data.messages);
            }
        } catch (error) {
            console.error('Error loading messages:', error);
        }
    }

    renderMessages(messages) {
        const chatMessages = document.querySelector('.chat-messages');
        if (!chatMessages) return;

        chatMessages.innerHTML = messages.map(msg => `
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                    ${msg.avatar?.initials || msg.user_name.charAt(0).toUpperCase()}
                </div>
                <div class="flex-1">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-normal text-white font-poppins">${msg.user_name}</span>
                        <span class="text-xs text-gray-400 font-poppins">${this.formatTime(msg.timestamp)}</span>
                    </div>
                    <p class="text-sm text-gray-300 font-poppins">${msg.message}</p>
                </div>
            </div>
        `).join('');

        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    async sendMessage() {
        const chatInput = document.querySelector('.chat-input');
        const message = chatInput?.value.trim();
        
        if (!message) return;

        const streamId = this.streamId || this.targetStreamId;
        if (!streamId) return;

        try {
            const response = await fetch('/broadcasting/message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    stream_id: streamId,
                    message: message,
                    type: 'message'
                })
            });

            const data = await response.json();
            if (data.success) {
                chatInput.value = '';
                this.loadMessages();
            }
        } catch (error) {
            console.error('Error sending message:', error);
        }
    }

    async updateViewerCount(count = null) {
        const streamId = this.streamId || this.targetStreamId;
        if (!streamId) return;

        try {
            if (count === null) {
                const response = await fetch(`/broadcasting/info?stream_id=${streamId}`);
                const data = await response.json();
                if (data.success) {
                    count = data.stream_info.viewers || 1;
                }
            }

            const viewerCount = document.querySelector('.viewer-count');
            if (viewerCount) {
                viewerCount.textContent = count || 1;
            }
        } catch (error) {
            console.error('Error updating viewer count:', error);
        }
    }

    async handleLike() {
        await this.handleInteraction('like');
        this.showInteractionNotification('like');
    }

    async handleShare() {
        await this.handleInteraction('share');
        this.showInteractionNotification('share');
    }

    async handleGift() {
        await this.handleInteraction('gift');
        this.showInteractionNotification('gift');
    }

    async handleRefresh() {
        this.loadMessages();
        this.updateViewerCount();
    }

    async handleFollow() {
        // Follow functionality
        console.log('Follow clicked');
    }

    async handleInteraction(type) {
        const streamId = this.streamId || this.targetStreamId;
        if (!streamId) return;

        try {
            const response = await fetch('/broadcasting/interaction', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    stream_id: streamId,
                    type: type
                })
            });

            const data = await response.json();
            if (data.success) {
                console.log(`${type} interaction sent`);
            }
        } catch (error) {
            console.error(`Error sending ${type} interaction:`, error);
        }
    }

    showInteractionNotification(type) {
        const messages = {
            like: '❤️ Liked!',
            share: '📤 Shared!',
            gift: '🎁 Gift sent!'
        };

        const notification = document.createElement('div');
        notification.className = 'fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-black/80 text-white px-4 py-2 rounded-lg z-50';
        notification.textContent = messages[type] || 'Interaction sent!';
        document.body.appendChild(notification);

        setTimeout(() => {
            document.body.removeChild(notification);
        }, 2000);
    }

    async loadCameras() {
        try {
            const response = await fetch('/broadcasting/cameras');
            const data = await response.json();
            
            if (data.success) {
                const cameraSelect = document.getElementById('cameraSelect');
                if (cameraSelect) {
                    cameraSelect.innerHTML = data.cameras.map(camera => 
                        `<option value="${camera.id}">${camera.name}</option>`
                    ).join('');
                    
                    cameraSelect.addEventListener('change', (e) => {
                        this.selectedCamera = e.target.value;
                    });
                }
            }
        } catch (error) {
            console.error('Error loading cameras:', error);
        }
    }

    saveSettings() {
        const streamTitle = document.getElementById('streamTitle')?.value;
        const streamDescription = document.getElementById('streamDescription')?.value;
        
        // Update UI
        const titleElement = document.querySelector('.stream-title');
        const descElement = document.querySelector('.stream-description');
        
        if (titleElement) titleElement.textContent = streamTitle || 'My Live Stream';
        if (descElement) descElement.textContent = streamDescription || 'Welcome to my live stream!';
        
        // Close modal
        const settingsModal = document.getElementById('settingsModal');
        if (settingsModal) {
            settingsModal.classList.add('hidden');
        }
    }

    setupMobileInteractions() {
        const chatToggle = document.getElementById('chatToggle');
        const chatPanel = document.getElementById('chatPanel');
        
        if (chatToggle && chatPanel) {
            chatToggle.addEventListener('click', () => {
                this.toggleChat();
            });
        }
    }

    toggleChat() {
        const chatPanel = document.getElementById('chatPanel');
        const chatToggle = document.getElementById('chatToggle');
        
        if (!chatPanel || !chatToggle) return;

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
        const chatPanel = document.getElementById('chatPanel');
        const chatToggle = document.getElementById('chatToggle');
        
        if (chatPanel && chatToggle) {
            chatPanel.classList.add('translate-y-full');
            chatToggle.innerHTML = `
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
            `;
            this.isChatOpen = false;
        }
    }

    formatTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diff = Math.floor((now - date) / 1000);

        if (diff < 60) return 'just now';
        if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
        if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
        return `${Math.floor(diff / 86400)}d ago`;
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
        const initials = name.substring(0, 2).toUpperCase();
        
        return {
            initials: initials,
            color: color
        };
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.liveBroadcasting = new LiveBroadcasting();
}); 