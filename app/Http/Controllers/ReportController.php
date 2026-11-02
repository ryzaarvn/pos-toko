<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Contracts\View\View;

class ReportController extends Controller
{
    public function sales(): View
    {
        $transactions = Transaction::query()
            ->with('user:id,name')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15);
        $totalTransactions = Transaction::count();
        $totalSales = Transaction::sum('total');

        return view('reports.sales', compact('transactions', 'totalTransactions', 'totalSales'));
    }
}
