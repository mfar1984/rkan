<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Pusher\Pusher;

class BroadcastingController extends Controller
{
    public function index(Request $request)
    {
        $joinStreamId = $request->query('join');
        $viewMode = $request->query('view');
        
        if ($joinStreamId) {
            // Join existing stream mode
            return view('broadcasting', ['mode' => 'join', 'stream_id' => $joinStreamId]);
        } elseif ($viewMode) {
            // View stream mode (for viewers)
            return view('broadcasting', ['mode' => 'view', 'stream_id' => $viewMode]);
        } else {
            // Start new stream mode
            return view('broadcasting', ['mode' => 'broadcast']);
        }
    }

    public function startStream(Request $request)
    {
        $user = Auth::user();
        $streamId = 'stream_' . $user->id . '_' . time();
        
        // Store stream info in cache
        $streamData = [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'title' => $request->input('title', 'Live Stream'),
            'description' => $request->input('description', 'Welcome to my live stream!'),
            'started_at' => now(),
            'viewers' => 1, // Start with 1 viewer (the broadcaster)
            'viewer_sessions' => [session()->getId()],
            'is_live' => true,
            'camera_device' => $request->input('camera_device', 'default')
        ];
        
        Cache::put($streamId, $streamData, 3600); // 1 hour

        // Track active streams
        $activeStreams = Cache::get('active_streams', []);
        if (!in_array($streamId, $activeStreams)) {
            $activeStreams[] = $streamId;
            Cache::put('active_streams', $activeStreams, 3600);
        }

        // Add activity
        $this->addActivity('stream_started', "{$user->name} started a live stream: {$request->input('title', 'Live Stream')}");

        return response()->json([
            'success' => true,
            'stream_id' => $streamId,
            'message' => 'Stream started successfully',
            'stream_info' => [
                'title' => $request->input('title', 'Live Stream'),
                'description' => $request->input('description', 'Welcome to my live stream!'),
                'viewers' => 1
            ]
        ]);
    }

    public function stopStream(Request $request)
    {
        $streamId = $request->input('stream_id');
        
        if (Cache::has($streamId)) {
            $streamData = Cache::get($streamId);
            $streamData['is_live'] = false;
            $streamData['ended_at'] = now();
            Cache::put($streamId, $streamData, 3600);

            // Remove from active streams
            $activeStreams = Cache::get('active_streams', []);
            $activeStreams = array_diff($activeStreams, [$streamId]);
            Cache::put('active_streams', $activeStreams, 3600);

            // Add activity
            $this->addActivity('stream_ended', "{$streamData['user_name']} ended their live stream");

            // Clear stream cache after 5 minutes
            Cache::forget($streamId);
        }

        return response()->json([
            'success' => true,
            'message' => 'Stream stopped successfully'
        ]);
    }

    public function sendMessage(Request $request)
    {
        $user = Auth::user();
        $message = $request->input('message');
        $streamId = $request->input('stream_id');

        if (empty($message)) {
            return response()->json(['success' => false, 'message' => 'Message cannot be empty']);
        }

        $chatMessage = [
            'id' => uniqid(),
            'user_id' => $user->id,
            'user_name' => $user->name,
            'message' => $message,
            'timestamp' => now()->toISOString(),
            'avatar' => $this->generateAvatar($user->name),
            'type' => 'message'
        ];

        // Store message in cache
        $messages = Cache::get('chat_' . $streamId, []);
        $messages[] = $chatMessage;
        
        // Keep only last 100 messages
        if (count($messages) > 100) {
            $messages = array_slice($messages, -100);
        }
        
        Cache::put('chat_' . $streamId, $messages, 3600);

        // Broadcast to Pusher (if configured)
        $this->broadcastMessage($streamId, $chatMessage);

        return response()->json([
            'success' => true,
            'message' => $chatMessage
        ]);
    }

    public function getMessages(Request $request)
    {
        $streamId = $request->input('stream_id');
        $messages = Cache::get('chat_' . $streamId, []);

        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }

    public function updateViewerCount(Request $request)
    {
        $streamId = $request->input('stream_id');
        $action = $request->input('action'); // 'join' or 'leave'

        if (Cache::has($streamId)) {
            $streamData = Cache::get($streamId);
            
            if ($action === 'join') {
                // Only increment if not already counted
                if (!isset($streamData['viewer_sessions']) || !in_array(session()->getId(), $streamData['viewer_sessions'])) {
                    $streamData['viewers'] = ($streamData['viewers'] ?? 1) + 1;
                    $streamData['viewer_sessions'] = $streamData['viewer_sessions'] ?? [];
                    $streamData['viewer_sessions'][] = session()->getId();
                }
            } elseif ($action === 'leave') {
                // Only decrement if this session was counted
                if (isset($streamData['viewer_sessions']) && in_array(session()->getId(), $streamData['viewer_sessions'])) {
                    $streamData['viewers'] = max(1, ($streamData['viewers'] ?? 1) - 1);
                    $streamData['viewer_sessions'] = array_diff($streamData['viewer_sessions'], [session()->getId()]);
                }
            }
            
            Cache::put($streamId, $streamData, 3600);
        } else {
            // Create stream if it doesn't exist (for demo purposes)
            $streamData = [
                'viewers' => 1,
                'viewer_sessions' => [session()->getId()],
                'is_live' => true
            ];
            Cache::put($streamId, $streamData, 3600);
        }

        return response()->json([
            'success' => true,
            'viewers' => $streamData['viewers'] ?? 1
        ]);
    }

    public function getStreamInfo(Request $request)
    {
        $streamId = $request->input('stream_id');
        $streamData = Cache::get($streamId, []);

        return response()->json([
            'success' => true,
            'stream_info' => $streamData
        ]);
    }

    public function handleInteraction(Request $request)
    {
        $user = Auth::user();
        $type = $request->input('type'); // 'like', 'share', 'gift'
        $streamId = $request->input('stream_id');

        $interaction = [
            'id' => uniqid(),
            'user_id' => $user->id,
            'user_name' => $user->name,
            'type' => $type,
            'timestamp' => now()->toISOString(),
            'avatar' => $this->generateAvatar($user->name)
        ];

        // Store interaction in cache
        $interactions = Cache::get('interactions_' . $streamId, []);
        $interactions[] = $interaction;
        
        // Keep only last 50 interactions
        if (count($interactions) > 50) {
            $interactions = array_slice($interactions, -50);
        }
        
        Cache::put('interactions_' . $streamId, $interactions, 3600);

        // Broadcast interaction
        $this->broadcastMessage($streamId, $interaction);

        return response()->json([
            'success' => true,
            'interaction' => $interaction
        ]);
    }

    public function getCameras()
    {
        // This would typically return available camera devices
        // For now, we'll return a mock list
        return response()->json([
            'success' => true,
            'cameras' => [
                ['id' => 'default', 'name' => 'Default Camera'],
                ['id' => 'front', 'name' => 'Front Camera'],
                ['id' => 'back', 'name' => 'Back Camera']
            ]
        ]);
    }

    private function generateAvatar($name)
    {
        $colors = [
            'from-pink-500 to-purple-500',
            'from-blue-500 to-green-500',
            'from-yellow-500 to-orange-500',
            'from-red-500 to-pink-500',
            'from-green-500 to-blue-500',
            'from-purple-500 to-pink-500'
        ];
        
        $color = $colors[array_rand($colors)];
        $initials = strtoupper(substr($name, 0, 2));
        
        return [
            'initials' => $initials,
            'color' => $color
        ];
    }

    private function broadcastMessage($streamId, $message)
    {
        try {
            $pusher = new Pusher(
                config('broadcasting.connections.pusher.key'),
                config('broadcasting.connections.pusher.secret'),
                config('broadcasting.connections.pusher.app_id'),
                config('broadcasting.connections.pusher.options')
            );

            $pusher->trigger('stream-' . $streamId, 'new-message', $message);
        } catch (\Exception $e) {
            // Log error but don't break the app
            \Log::error('Pusher broadcast failed: ' . $e->getMessage());
        }
    }

    private function addActivity($type, $message)
    {
        $activities = Cache::get('recent_activities', []);
        
        $newActivity = [
            'id' => time(),
            'type' => $type,
            'message' => $message,
            'created_at' => now()->toISOString()
        ];
        
        array_unshift($activities, $newActivity);
        
        // Keep only last 50 activities
        $activities = array_slice($activities, 0, 50);
        
        Cache::put('recent_activities', $activities, 3600);
    }
} 