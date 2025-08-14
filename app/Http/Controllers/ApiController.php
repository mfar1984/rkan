<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ApiController extends Controller
{
    public function getLiveStreams()
    {
        $currentUser = Auth::user();
        $liveStreams = [];
        $cacheKeys = Cache::get('active_streams', []);
        
        foreach ($cacheKeys as $streamId) {
            if (Cache::has($streamId)) {
                $streamData = Cache::get($streamId);
                if ($streamData['is_live'] ?? false) {
                    // Don't show streams from current user
                    if ($streamData['user_id'] != $currentUser->id) {
                        $liveStreams[] = [
                            'stream_id' => $streamId,
                            'user_id' => $streamData['user_id'],
                            'user_name' => $streamData['user_name'],
                            'title' => $streamData['title'],
                            'description' => $streamData['description'],
                            'viewers' => $streamData['viewers'] ?? 1,
                            'started_at' => $streamData['started_at'],
                            'is_live' => true
                        ];
                    }
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'streams' => $liveStreams
        ]);
    }

    public function getRecentActivity()
    {
        $activities = Cache::get('recent_activities', []);
        
        // If no activities, create some default ones
        if (empty($activities)) {
            $activities = [
                [
                    'id' => 1,
                    'type' => 'system',
                    'message' => 'System initialized successfully',
                    'created_at' => now()->subMinutes(5)->toISOString()
                ],
                [
                    'id' => 2,
                    'type' => 'user_created',
                    'message' => 'Admin user account created',
                    'created_at' => now()->subMinutes(10)->toISOString()
                ]
            ];
            Cache::put('recent_activities', $activities, 3600);
        }
        
        return response()->json([
            'success' => true,
            'activities' => $activities
        ]);
    }

    public function createUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:4'
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            // Add activity
            $this->addActivity('user_created', "New user created: {$user->name} ({$user->email})");

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getUsers()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'users' => $users
        ]);
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