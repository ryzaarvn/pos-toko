<?php

namespace App\Http\Controllers;

use App\Exports\SalesExport;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $cashierId = $this->cashierId($request);
        $start = $request->filled('start')
            ? Carbon::parse($request->input('start'))
            : now()->subDays(6)->startOfDay();

        $end = $request->filled('end')
            ? Carbon::parse($request->input('end'))->endOfDay()
            : now()->endOfDay();

        $transactions = Transaction::with('user')
            ->whereBetween('created_at', [$start, $end])
            ->when($cashierId !== null, fn ($query) => $query->where('user_id', $cashierId))
            ->latest()
            ->get();

        $totalPenjualan = $transactions->sum('total');
        $jumlahTransaksi = $transactions->count();
        $cashiers = User::query()
            ->where('role', 'kasir')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('reports.sales', compact(
            'transactions',
            'totalPenjualan',
            'jumlahTransaksi',
            'cashiers',
            'cashierId',
            'start',
            'end'
        ));
    }

    public function export(Request $request)
    {
        $cashierId = $this->cashierId($request);
        $start = $request->filled('start')
            ? Carbon::parse($request->input('start'))
            : now()->subDays(6)->startOfDay();

        $end = $request->filled('end')
            ? Carbon::parse($request->input('end'))->endOfDay()
            : now()->endOfDay();

        $filename = 'laporan-penjualan-'.$start->format('Ymd').'-'.$end->format('Ymd').'.xlsx';

        return Excel::download(new SalesExport($start, $end, $cashierId), $filename);
    }

    private function cashierId(Request $request): ?int
    {
        $validated = $request->validate([
            'cashier_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where('role', 'kasir'),
            ],
        ]);

        return isset($validated['cashier_id']) ? (int) $validated['cashier_id'] : null;
    }
}
