<?php

namespace App\Exports;

use App\Models\TransactionDetail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class BestSellingProductsSheet implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    public function __construct(protected Carbon $start, protected Carbon $end, protected ?int $cashierId = null) {}

    public function collection(): Collection
    {
        return TransactionDetail::query()
            ->selectRaw('product_id, SUM(qty) as total_qty, SUM(subtotal) as total_sales')
            ->whereHas('transaction', fn (Builder $query) => $query
                ->whereBetween('created_at', [$this->start, $this->end])
                ->when($this->cashierId !== null, fn (Builder $query) => $query->where('user_id', $this->cashierId)))
            ->with(['product' => fn (BelongsTo $relation) => $relation->withTrashed()])
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->orderByDesc('total_sales')
            ->get();
    }

    public function headings(): array
    {
        return ['Produk', 'Jumlah Terjual', 'Total Penjualan'];
    }

    /**
     * @param  TransactionDetail  $detail
     * @return array<int, mixed>
     */
    public function map(mixed $detail): array
    {
        return [
            $detail->product->name,
            $detail->total_qty,
            $detail->total_sales,
        ];
    }

    public function title(): string
    {
        return 'Produk Terlaris';
    }
}
