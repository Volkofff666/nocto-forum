<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LogController extends Controller
{
    public function index(Request $request): Response
    {
        $logs = AdminLog::with('admin:id,name,username')
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return Inertia::render('Admin/Logs', [
            'logs' => $logs,
        ]);
    }
}
