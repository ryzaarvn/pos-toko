<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Riwayat Transaksi Saya
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <div class="mb-6 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Riwayat Transaksi Saya</h3>
                    <a href="{{ route('pos.index') }}"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white">
                        Transaksi Baru
                    </a>
                </div>

                @if ($transactions->isEmpty())
                    <p class="text-gray-600">Belum ada transaksi.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 text-left text-gray-600">
                                <tr>
                                    <th scope="col" class="px-4 py-3 font-medium">Tanggal</th>
                                    <th scope="col" class="px-4 py-3 font-medium">No. Invoice</th>
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
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
