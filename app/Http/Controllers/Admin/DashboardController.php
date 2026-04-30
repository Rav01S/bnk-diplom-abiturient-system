<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Program;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalUsers = User::count();
        $applicantCount = User::where('role', 'applicant')->count();
        $staffCount = User::whereIn('role', ['commission', 'admin'])->count();
        $totalApplications = Application::count();
        $pendingCount = Application::byStatus('submitted')->count();
        $approvedCount = Application::byStatus('approved')->count();
        $specialtyCount = Specialty::count();
        $openProgramCount = Program::where('is_open', true)->count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'applicantCount',
            'staffCount',
            'totalApplications',
            'pendingCount',
            'approvedCount',
            'specialtyCount',
            'openProgramCount',
        ));
    }
}
