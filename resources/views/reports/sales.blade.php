<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Laporan Penjualan</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form method="GET" class="flex gap-3 items-end mb-4">
                    <div>
                        <label class="block text-sm text-gray-700">Dari Tanggal</label>
                        <input type="date" name="start" value="{{ $start->format('Y-m-d') }}"
                            class="rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Sampai Tanggal</label>
                        <input type="date" name="end" value="{{ $end->format('Y-m-d') }}"
                            class="rounded-md border-gray-300">
                    </div>
                    <div>
                        <label for="cashier_id" class="block text-sm text-gray-700">Kasir</label>
                        <select id="cashier_id" name="cashier_id" class="rounded-md border-gray-300">
                            <option value="">Semua kasir</option>
                            @foreach ($cashiers as $cashier)
                                <option value="{{ $cashier->id }}" @selected($cashierId === $cashier->id)>
                                    {{ $cashier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md">Tampilkan</button>
                    <a href="{{ route('report.export', request()->query()) }}"
                        class="bg-green-600 text-white px-4 py-2 rounded-md">Export Excel</a>
                </form>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="bg-gray-50 rounded-md p-4">
                        <p class="text-xs text-gray-500">Total Penjualan</p>

                        <p class="text-xl font-semibold">Rp{{ number_format($totalPenjualan, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-md p-4">
                        <p class="text-xs text-gray-500">Jumlah Transaksi</p>
                        <p class="text-xl font-semibold">{{ $jumlahTransaksi }}</p>
                    </div>
                </div>

                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr>
                            <th class="px-3 py-2 text-left">No. Invoice</th>
                            <th class="px-3 py-2 text-left">Tanggal</th>
                            <th class="px-3 py-2 text-left">Kasir</th>
                            <th class="px-3 py-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($transactions as $trx)
                            <tr>
                                <td class="px-3 py-2">{{ $trx->invoice_number }}</td>
                                <td class="px-3 py-2">{{ $trx->created_at->format('d M Y H:i') }}</td>
                                <td class="px-3 py-2">{{ $trx->user->name }}</td>
                                <td class="px-3 py-2 text-right">Rp{{ number_format($trx->total, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-card>
        </div>
    </div>
</x-app-layout>