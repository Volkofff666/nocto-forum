<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Services\AdminLogger;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function index(Request $request): Response
    {
        $status = $request->input('status', 'pending');

        $reports = Report::with(['user:id,name,username', 'resolver:id,name,username', 'reportable'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $rawCounts = Report::selectRaw('status, count(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status');

        $counts = [
            'pending'   => (int) ($rawCounts['pending']   ?? 0),
            'resolved'  => (int) ($rawCounts['resolved']  ?? 0),
            'dismissed' => (int) ($rawCounts['dismissed'] ?? 0),
        ];

        return Inertia::render('Admin/Reports', [
            'reports' => $reports,
            'filters' => ['status' => $status],
            'counts'  => $counts,
        ]);
    }

    public function resolve(Report $report)
    {
        $report->update([
            'status'      => 'resolved',
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        AdminLogger::log('resolve_report', "Жалоба #{$report->id} принята", 'report', $report->id);

        return back()->with('success', 'Жалоба принята.');
    }

    public function dismiss(Report $report)
    {
        $report->update([
            'status'      => 'dismissed',
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        AdminLogger::log('dismiss_report', "Жалоба #{$report->id} отклонена", 'report', $report->id);

        return back()->with('success', 'Жалоба отклонена.');
    }
}
