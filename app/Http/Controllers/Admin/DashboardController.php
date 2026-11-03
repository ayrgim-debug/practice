<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $counts = SupportTicket::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            'total' => (int) $counts->sum(),
            'new' => (int) ($counts['new'] ?? 0),
            'in_progress' => (int) ($counts['in_progress'] ?? 0),
            'resolved' => (int) ($counts['resolved'] ?? 0),
            'closed' => (int) ($counts['closed'] ?? 0),
            'open_bugs' => SupportTicket::where('type', 'bug')
                ->whereIn('status', ['new', 'in_progress'])
                ->count(),
            'users' => User::count(),
        ];

        $latest = SupportTicket::with('user')->latest()->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'latest'));
    }
}