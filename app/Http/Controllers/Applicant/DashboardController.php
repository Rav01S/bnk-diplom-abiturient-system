<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $applicant = auth()->user()->applicant;
        $applications = $applicant
            ? $applicant->applications()->with('program.specialty', 'scores')->latest()->take(4)->get()
            : collect();

        $activeCount = $applicant ? $applicant->applications()->active()->count() : 0;
        $pendingCount = $applicant ? $applicant->applications()->byStatus('submitted')->count() : 0;
        $approvedCount = $applicant ? $applicant->applications()->byStatus('approved')->count() : 0;
        $attentionCount = $applicant ? $applicant->applications()->whereIn('status', ['rejected', 'rework_needed'])->count() : 0;

        return view('applicant.dashboard', compact(
            'applications',
            'activeCount',
            'pendingCount',
            'approvedCount',
            'attentionCount',
        ));
    }
}
