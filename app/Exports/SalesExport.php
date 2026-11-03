<?php

namespace App\Exports;

use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SalesExport implements Export, WithMultipleSheets
{
    public function __construct(protected Carbon $start, protected Carbon $end, protected ?int $cashierId = null) {}

    public function sheets(): array
    {
        return [
            new SalesTransactionsSheet($this->start, $this->end, $this->cashierId),
            new BestSellingProductsSheet($this->start, $this->end, $this->cashierId),
        ];
    }
}
