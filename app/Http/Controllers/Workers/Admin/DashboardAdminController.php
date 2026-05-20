<?php

namespace App\Http\Controllers\Workers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Need;
use App\Models\Patient;
use App\Models\Report;
use App\Models\Worker;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardAdminController extends Controller
{
    /**
     * Show the admin dashboard with totals and 7-day activity charts.
     */
    public function index()
    {
        $totalPatients = Patient::query()->count('*');
        $totalWorkers = Worker::query()->count('*');
        $totalReports = Report::query()->count('*');
        $totalNeeds = Need::query()->count('*');

        $days = [];
        $reports = [];
        $patients = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->locale('ca')->isoFormat('ddd');
            $reports[] = Report::whereDate('created_at', '=', $date->format('Y-m-d'), 'and')->count('*');
            $patients[] = Worker::whereDate('created_at', '=', $date->format('Y-m-d'), 'and')->count('*');
        }

        return Inertia::render('Workers/Dashboard', [
            'stats' => [
                'totalPatients' => $totalPatients,
                'totalWorkers' => $totalWorkers,
                'totalReports' => $totalReports,
                'totalNeeds' => $totalNeeds,
            ],
            'chartDays' => $days,
            'chartReports' => $reports,
            'chartPatients' => $patients,
        ]);
    }
}
