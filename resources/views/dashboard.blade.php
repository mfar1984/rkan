<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - RKAN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    </style>
</head>
<body class="bg-gray-100 font-poppins">
    <!-- Navigation Header -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <img src="/images/logo.png" alt="RKAN Logo" class="w-8 h-auto mr-3">
                    <span class="text-xl font-semibold text-gray-900">RKAN Dashboard</span>
                </div>
                
                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">Welcome, {{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-700 transition-colors duration-200">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $totalUsers ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Active Sessions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Live Streams</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $liveStreams ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                        <p class="text-2xl font-semibold text-gray-900">${{ number_format($totalRevenue ?? 0) }}</p>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">System Status</p>
                        <p class="text-2xl font-semibold text-green-600">Online</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Live Streams Section -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-3">
                    <h2 class="text-xl font-semibold text-gray-900">Live Streams</h2>
                    <div id="liveStreamsCount" class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                        0 live
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <button id="refreshLiveStreams" class="text-gray-500 hover:text-gray-700 transition-colors duration-200" title="Refresh Live Streams">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </button>
                    <a href="{{ route('broadcasting') }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                        Go Live
                    </a>
                </div>
            </div>
            
            <div id="liveStreamsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Live streams will be loaded here -->
            </div>
            
            <div id="noLiveStreams" class="text-center py-8 text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                <p class="text-lg font-medium">No live streams at the moment</p>
                <p class="text-sm">Be the first to go live!</p>
                <div class="mt-4">
                    <button id="manualRefresh" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        Click here to refresh
                    </button>
                </div>
            </div>
        </div>

        <!-- User Management Section -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900">User Management</h2>
                <button id="createUserBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    Create User
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="usersTableBody">
                        @foreach($users ?? [] as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white text-sm font-bold" title="User Avatar">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->created_at->diffForHumans() }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                                <button class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Recent Activity</h2>
            <div class="space-y-4" id="recentActivity">
                <!-- Activity items will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Create User Modal -->
    <div id="createUserModal" class="fixed inset-0 bg-black/50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Create New User</h3>
                    <button class="close-modal text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form id="createUserForm">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                            <input type="text" id="userName" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" id="userEmail" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <input type="password" id="userPassword" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500" required>
                        </div>
                    </div>
                    
                    <div class="flex space-x-3 mt-6">
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-medium transition-colors duration-200">
                            Create User
                        </button>
                        <button type="button" class="close-modal flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 rounded-lg font-medium transition-colors duration-200">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        class DashboardManager {
            constructor() {
                this.init();
            }

            init() {
                this.setupEventListeners();
                this.loadLiveStreams();
                this.loadRecentActivity();
                this.startAutoRefresh();
            }

            setupEventListeners() {
                // Create User Modal
                const createUserBtn = document.getElementById('createUserBtn');
                const createUserModal = document.getElementById('createUserModal');
                const closeModalBtns = document.querySelectorAll('.close-modal');
                const createUserForm = document.getElementById('createUserForm');

                if (createUserBtn) {
                    createUserBtn.addEventListener('click', () => {
                        createUserModal.classList.remove('hidden');
                    });
                }

                closeModalBtns.forEach(btn => {
                    btn.addEventListener('click', () => {
                        createUserModal.classList.add('hidden');
                    });
                });

                if (createUserForm) {
                    createUserForm.addEventListener('submit', (e) => {
                        e.preventDefault();
                        this.createUser();
                    });
                }

                // Refresh Live Streams
                const refreshBtn = document.getElementById('refreshLiveStreams');
                const manualRefreshBtn = document.getElementById('manualRefresh');
                
                if (refreshBtn) {
                    refreshBtn.addEventListener('click', () => {
                        this.loadLiveStreams();
                        this.showNotification('Live streams refreshed!', 'success');
                    });
                }

                if (manualRefreshBtn) {
                    manualRefreshBtn.addEventListener('click', () => {
                        this.loadLiveStreams();
                        this.showNotification('Live streams refreshed!', 'success');
                    });
                }

                // Close modal when clicking outside
                createUserModal.addEventListener('click', (e) => {
                    if (e.target === createUserModal) {
                        createUserModal.classList.add('hidden');
                    }
                });
            }

            async loadLiveStreams() {
                try {
                    const response = await fetch('/api/live-streams');
                    const data = await response.json();
                    
                    if (data.success) {
                        this.renderLiveStreams(data.streams);
                    }
                } catch (error) {
                    console.error('Error loading live streams:', error);
                }
            }

            renderLiveStreams(streams) {
                const container = document.getElementById('liveStreamsContainer');
                const noStreams = document.getElementById('noLiveStreams');
                const liveStreamsCount = document.getElementById('liveStreamsCount');
                
                if (!container || !noStreams || !liveStreamsCount) return;

                if (streams.length === 0) {
                    container.innerHTML = '';
                    noStreams.classList.remove('hidden');
                    liveStreamsCount.textContent = '0 live';
                    return;
                }

                noStreams.classList.add('hidden');
                container.innerHTML = streams.map(stream => `
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-red-500 to-pink-500 rounded-full flex items-center justify-center text-white text-sm font-bold" title="User Avatar">
                                    ${stream.user_name.charAt(0).toUpperCase()}
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-900">${stream.user_name}</h3>
                                    <p class="text-sm text-gray-500">${stream.title}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                                <span class="text-xs text-red-600 font-medium">LIVE</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <span>👥 ${stream.viewers} viewer${stream.viewers !== 1 ? 's' : ''}</span>
                            <span>⏱️ ${this.formatDuration(stream.started_at)}</span>
                        </div>
                        
                        <div class="mt-3">
                            <a href="/broadcasting?view=${stream.stream_id}" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors duration-200 block text-center">
                                Watch Stream
                            </a>
                        </div>
                    </div>
                `).join('');
                liveStreamsCount.textContent = `${streams.length} live`;
            }

            async loadRecentActivity() {
                try {
                    const response = await fetch('/api/recent-activity');
                    const data = await response.json();
                    
                    if (data.success) {
                        this.renderRecentActivity(data.activities);
                    }
                } catch (error) {
                    console.error('Error loading recent activity:', error);
                }
            }

            renderRecentActivity(activities) {
                const container = document.getElementById('recentActivity');
                if (!container) return;

                container.innerHTML = activities.map(activity => `
                    <div class="flex items-center space-x-3">
                        <div class="w-2 h-2 bg-${this.getActivityColor(activity.type)}-500 rounded-full"></div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-900">${activity.message}</p>
                            <p class="text-xs text-gray-500">${this.formatTime(activity.created_at)}</p>
                        </div>
                    </div>
                `).join('');
            }

            async createUser() {
                const name = document.getElementById('userName').value;
                const email = document.getElementById('userEmail').value;
                const password = document.getElementById('userPassword').value;

                try {
                    const response = await fetch('/api/users', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            name: name,
                            email: email,
                            password: password
                        })
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        this.showNotification('User created successfully!', 'success');
                        document.getElementById('createUserModal').classList.add('hidden');
                        document.getElementById('createUserForm').reset();
                        this.loadRecentActivity();
                        location.reload(); // Refresh to show new user
                    } else {
                        this.showNotification(data.message || 'Failed to create user', 'error');
                    }
                } catch (error) {
                    console.error('Error creating user:', error);
                    this.showNotification('Failed to create user', 'error');
                }
            }

            formatDuration(startedAt) {
                const start = new Date(startedAt);
                const now = new Date();
                const diff = Math.floor((now - start) / 1000);
                
                const hours = Math.floor(diff / 3600);
                const minutes = Math.floor((diff % 3600) / 60);
                
                if (hours > 0) {
                    return `${hours}h ${minutes}m`;
                }
                return `${minutes}m`;
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

            getActivityColor(type) {
                const colors = {
                    'user_created': 'green',
                    'stream_started': 'red',
                    'stream_ended': 'yellow',
                    'system': 'blue'
                };
                return colors[type] || 'gray';
            }

            startAutoRefresh() {
                // Refresh live streams every 5 seconds (more frequent)
                setInterval(() => {
                    this.loadLiveStreams();
                }, 5000);

                // Refresh activity every 30 seconds
                setInterval(() => {
                    this.loadRecentActivity();
                }, 30000);
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

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', () => {
            window.dashboardManager = new DashboardManager();
        });
    </script>
</body>
</html> 