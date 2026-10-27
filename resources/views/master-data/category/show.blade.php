<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Detail Kategori</h2>
            <a href="{{ route('categories.index') }}" class="text-indigo-600 hover:text-indigo-900">
                Kembali ke Kategori
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <h3 class="text-lg font-semibold">{{ $category->name }}</h3>
                <p class="text-sm text-gray-500 mb-6">{{ $category->description ?: '-' }}</p>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-gray-50 rounded-md p-4 text-center">
                        <p class="text-xs text-gray-500">Jumlah Produk</p>
                        <p class="text-lg font-semibold">{{ $category->products->count() }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-md p-4 text-center">
                        <p class="text-xs text-gray-500">Total Stok</p>
                        <p class="text-lg font-semibold">{{ $totalStock }}</p>
                    </div>
                </div>

                <h4 class="font-medium mb-2">Daftar Produk</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left">Kode</th>
                                <th class="px-3 py-2 text-left">Nama Produk</th>
                                <th class="px-3 py-2 text-right">Harga</th>
                                <th class="px-3 py-2 text-right">Stok</th>
                                <th class="px-3 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($category->products as $product)
                                <tr>
                                    <td class="px-3 py-2">{{ $product->code }}</td>
                                    <td class="px-3 py-2">{{ $product->name }}</td>
                                    <td class="px-3 py-2 text-right">
                                        Rp{{ number_format($product->price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-2 text-right">{{ $product->stock }} {{ $product->unit }}</td>
                                    <td class="px-3 py-2 text-center">
                                        <a href="{{ route('products.show', $product) }}"
                                            class="text-indigo-600 hover:text-indigo-900">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-4 text-center text-gray-500">
                                        Belum ada produk dalam kategori ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
