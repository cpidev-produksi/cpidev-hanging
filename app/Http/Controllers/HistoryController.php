<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $q = AuditLog::query()
            ->latest()
            ->whereJsonContains('meta->was_done', true);

        if ($request->filled('form')) {
            $q->where('form_key', $request->form);
        }

        return view('history.index', [
            'logs' => $q->paginate(15)->withQueryString(),
            'form' => $request->form,
        ]);
    }

    public function show(AuditLog $auditLog)
    {
        return view('history.show', compact('auditLog'));
    }
}