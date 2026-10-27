<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the five days with the highest product sales', function () {
    $user = User::factory()->create();
    $user->forceFill(['role' => 'admin'])->save();
    $category = Category::create(['name' => 'Makanan']);
    $product = Product::create([
        'category_id' => $category->id,
        'code' => 'FOOD-001',
        'name' => 'Biskuit',
        'price' => 5000,
        'stock' => 10,
    ]);
    $otherProduct = Product::create([
        'category_id' => $category->id,
        'code' => 'FOOD-002',
        'name' => 'Cokelat',
        'price' => 10000,
        'stock' => 10,
    ]);

    foreach ([
        ['date' => '2026-10-01', 'qty' => 4],
        ['date' => '2026-10-01', 'qty' => 5],
        ['date' => '2026-10-02', 'qty' => 15],
        ['date' => '2026-10-03', 'qty' => 2],
        ['date' => '2026-10-04', 'qty' => 12],
        ['date' => '2026-10-05', 'qty' => 7],
        ['date' => '2026-10-06', 'qty' => 10],
        ['date' => '2026-10-07', 'qty' => 1],
    ] as $sale) {
        $transaction = Transaction::create([
            'invoice_number' => 'INV-'.$sale['date'].'-'.$sale['qty'].'-'.fake()->unique()->numerify('###'),
            'user_id' => $user->id,
            'total' => $sale['qty'] * 5000,
            'pay' => $sale['qty'] * 5000,
            'change' => 0,
        ]);
        $transaction->forceFill(['created_at' => $sale['date'].' 12:00:00'])->saveQuietly();

        TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'qty' => $sale['qty'],
            'price' => 5000,
            'subtotal' => $sale['qty'] * 5000,
        ]);
    }

    $otherProductTransaction = Transaction::create([
        'invoice_number' => 'INV-OTHER-PRODUCT',
        'user_id' => $user->id,
        'total' => 100000,
        'pay' => 100000,
        'change' => 0,
    ]);
    $otherProductTransaction->forceFill(['created_at' => '2026-10-02 13:00:00'])->saveQuietly();
    TransactionDetail::create([
        'transaction_id' => $otherProductTransaction->id,
        'product_id' => $otherProduct->id,
        'qty' => 100,
        'price' => 1000,
        'subtotal' => 100000,
    ]);

    $response = $this->actingAs($user)->get(route('products.show', $product));

    $response->assertOk()
        ->assertViewHas('penjualanHarian')
        ->assertSee('5 Hari Penjualan Tertinggi')
        ->assertSee('15 pcs')
        ->assertSee('12 pcs')
        ->assertSee('10 pcs')
        ->assertSee('9 pcs')
        ->assertSee('7 pcs');

    expect($response->viewData('penjualanHarian')->map(
        fn ($penjualan) => [$penjualan->tanggal, (int) $penjualan->total_terjual]
    )->all())->toEqual([
        ['2026-10-02', 15],
        ['2026-10-04', 12],
        ['2026-10-06', 10],
        ['2026-10-01', 9],
        ['2026-10-05', 7],
    ]);
});

it('shows an empty ranking when the product has no sales', function () {
    $user = User::factory()->create();
    $user->forceFill(['role' => 'admin'])->save();
    $category = Category::create(['name' => 'Makanan']);
    $product = Product::create([
        'category_id' => $category->id,
        'code' => 'FOOD-001',
        'name' => 'Biskuit',
        'price' => 5000,
        'stock' => 10,
    ]);

    $response = $this->actingAs($user)->get(route('products.show', $product));

    $response->assertOk()
        ->assertViewHas('penjualanHarian', fn ($penjualanHarian) => $penjualanHarian->isEmpty())
        ->assertSee('Belum ada data penjualan.');
});
