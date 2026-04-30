<?php

namespace App\Http\Controllers\Commission;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $pendingCount = Application::byStatus('submitted')->count();
        $approvedToday = Application::byStatus('approved')->whereDate('updated_at', today())->count();
        $rejectedToday = Application::byStatus('rejected')->whereDate('updated_at', today())->count();
        $reworkCount = Application::byStatus('rework_needed')->count();

        $recentApps = Application::with('program.specialty')
            ->whereIn('status', ['submitted', 'approved', 'rejected'])
            ->latest('updated_at')
            ->take(5)
            ->get();

        return view('commission.dashboard', compact(
            'pendingCount',
            'approvedToday',
            'rejectedToday',
            'reworkCount',
            'recentApps',
        ));
    }
}
