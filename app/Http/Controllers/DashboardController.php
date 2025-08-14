<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Get dashboard statistics
        $totalUsers = User::count();
        $liveStreams = $this->getLiveStreamsCount();
        $totalRevenue = $this->calculateTotalRevenue();
        $users = User::orderBy('created_at', 'desc')->get();

        return view('dashboard', compact('totalUsers', 'liveStreams', 'totalRevenue', 'users'));
    }

    private function getLiveStreamsCount()
    {
        // Count active live streams from cache
        $liveStreams = 0;
        $cacheKeys = Cache::get('active_streams', []);
        
        foreach ($cacheKeys as $streamId) {
            if (Cache::has($streamId)) {
                $streamData = Cache::get($streamId);
                if ($streamData['is_live'] ?? false) {
                    $liveStreams++;
                }
            }
        }
        
        return $liveStreams;
    }

    private function calculateTotalRevenue()
    {
        // Mock revenue calculation (in real app, this would come from transactions)
        $baseRevenue = 1000;
        $userMultiplier = User::count() * 10;
        $liveStreamMultiplier = $this->getLiveStreamsCount() * 50;
        
        return $baseRevenue + $userMultiplier + $liveStreamMultiplier;
    }
} 