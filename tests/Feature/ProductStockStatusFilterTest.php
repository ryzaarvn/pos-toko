<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('filters products by stock status', function (?string $stockStatus, array $expectedCodes) {
    $user = User::factory()->create();
    $user->forceFill(['role' => 'admin'])->save();

    $category = Category::create(['name' => 'Makanan']);

    foreach ([
        ['code' => 'OUT', 'stock' => 0],
        ['code' => 'LOW', 'stock' => 1],
        ['code' => 'LOW-EDGE', 'stock' => 9],
        ['code' => 'THRESHOLD', 'stock' => 10],
        ['code' => 'NORMAL', 'stock' => 11],
    ] as $product) {
        Product::create([
            ...$product,
            'category_id' => $category->id,
            'name' => $product['code'],
            'price' => 1000,
        ]);
    }

    $url = route('products.index', $stockStatus === null ? [] : ['stock_status' => $stockStatus]);

    $response = $this->actingAs($user)->get($url);

    $response->assertOk()
        ->assertViewHas('products', fn ($products) => $products->getCollection()
            ->pluck('code')
            ->sort()
            ->values()
            ->all() === $expectedCodes)
        ->assertSee('name="stock_status"', false);

    if ($stockStatus !== null) {
        $response->assertSee('value="'.$stockStatus.'" selected', false);
    } else {
        $response->assertSee('value="" selected', false);
    }
})->with([
    'all products' => [null, ['LOW', 'LOW-EDGE', 'NORMAL', 'OUT', 'THRESHOLD']],
    'low stock products' => ['menipis', ['LOW', 'LOW-EDGE']],
    'out of stock products' => ['habis', ['OUT']],
]);
