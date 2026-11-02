<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePosRequest;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::where('stock', '>', 0)
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'price', 'stock', 'unit']);

        return view('pos.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePosRequest $request)
    {
        $transaction = DB::transaction(function () use ($request) {
            $total = 0;
            $details = [];

            foreach ($request->items as $item) {
                // lockForUpdate mengunci baris produk agar tidak dibaca kasir lain
                // secara bersamaan selagi stok sedang diperbarui.
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock < $item['qty']) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$product->name} tidak mencukupi.",
                    ]);
                }

                $subtotal = $product->price * $item['qty'];
                $total += $subtotal;

                $details[] = [
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ];

                $product->decrement('stock', $item['qty']);
            }

            if ($request->pay < $total) {
                throw ValidationException::withMessages([
                    'pay' => 'Nominal bayar kurang dari total belanja.',
                ]);
            }

            $trx = Transaction::create([
                'invoice_number' => 'INV-'.now()->format('Ymd').'-'.str_pad(Transaction::whereDate('created_at', now())->count() + 1, 4, '0', STR_PAD_LEFT),
                'user_id' => auth()->id(),
                'total' => $total,
                'pay' => $request->pay,
                'change' => $request->pay - $total,
            ]);

            $trx->details()->createMany($details);

            return $trx;
        });

        return redirect()->route('pos.struk', $transaction)->with('success', 'Transaksi berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function struk(Transaction $transaction)
    {
        abort_unless(
            auth()->user()->role === 'admin' || $transaction->user_id === auth()->id(),
            404
        );

        $transaction->load('details.product', 'user');

        return view('pos.struk', compact('transaction'));
    }

    public function history(): View
    {
        $transactions = auth()->user()->transactions()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return view('pos.history', compact('transactions'));
    }
}
