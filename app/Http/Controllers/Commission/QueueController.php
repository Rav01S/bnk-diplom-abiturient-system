<?php

namespace App\Http\Controllers\Commission;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QueueController extends Controller
{
    public function index(Request $request): View
    {
        $query = Application::with('program.specialty')
            ->byStatus('submitted')
            ->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('app_last_name', 'like', "%{$search}%")
                    ->orWhere('app_first_name', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%")
                    ->orWhereHas('program.specialty', function ($sq) use ($search): void {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $applications = $query->paginate(15);

        return view('commission.queue', compact('applications'));
    }

    public function reviewNext(): RedirectResponse
    {
        $application = Application::submitted()
            ->oldest()
            ->first();

        if (! $application) {
            return redirect()
                ->route('commission.queue')
                ->with('success', 'В очереди нет заявлений для проверки.');
        }

        return redirect()->route('commission.review', $application);
    }
}
