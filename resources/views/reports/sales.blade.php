<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Laporan Penjualan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-card>
                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <p class="text-sm text-gray-500">Total Penjualan</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">
                            Rp{{ number_format($totalSales, 0, ',', '.') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Jumlah Transaksi</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">
                            {{ number_format($totalTransactions, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </x-card>

            <x-card>
                <h3 class="mb-4 text-lg font-semibold text-gray-800">Transaksi Terbaru</h3>

                @if ($transactions->isEmpty())
                    <p class="text-gray-600">Belum ada transaksi.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 text-left text-gray-600">
                                <tr>
                                    <th scope="col" class="px-4 py-3 font-medium">Tanggal</th>
                                    <th scope="col" class="px-4 py-3 font-medium">No. Invoice</th>
                                    <th scope="col" class="px-4 py-3 font-medium">Kasir</th>
                                    <th scope="col" class="px-4 py-3 text-right font-medium">Total</th>
                                    <th scope="col" class="px-4 py-3 text-right font-medium">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 text-gray-600">
                                            {{ $transaction->created_at->format('d M Y H:i') }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 font-medium text-gray-900">
                                            {{ $transaction->invoice_number }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-gray-600">
                                            {{ $transaction->user->name }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-gray-900">
                                            Rp{{ number_format($transaction->total, 0, ',', '.') }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right">
                                            <a href="{{ route('pos.struk', $transaction) }}"
                                                class="font-medium text-indigo-600 hover:text-indigo-800">
                                                Lihat Struk
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
