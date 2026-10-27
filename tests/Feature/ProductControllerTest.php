<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('uses the selected page size for the product list', function (?int $perPage, int $expectedPerPage) {
    $user = User::factory()->create();
    $user->forceFill(['role' => 'admin'])->save();
    $category = Category::create(['name' => 'Makanan']);

    foreach (range(1, 26) as $number) {
        Product::create([
            'category_id' => $category->id,
            'code' => "PRODUCT-{$number}",
            'name' => "Produk {$number}",
            'price' => 1000,
            'stock' => 10,
        ]);
    }

    $url = route('products.index', $perPage === null ? [] : ['per_page' => $perPage]);
    $response = $this->actingAs($user)->get($url);

    $response->assertOk()
        ->assertViewHas('products', fn ($products) => $products->perPage() === $expectedPerPage
            && $products->count() === min($expectedPerPage, 26))
        ->assertSee('name="per_page"', false)
        ->assertSee('value="'.$expectedPerPage.'" selected', false);
})->with([
    'default page size' => [null, 10],
    '10 products per page' => [10, 10],
    '25 products per page' => [25, 25],
    '50 products per page' => [50, 50],
    '100 products per page' => [100, 100],
]);

it('preserves the selected page size in pagination links', function () {
    $user = User::factory()->create();
    $user->forceFill(['role' => 'admin'])->save();
    $category = Category::create(['name' => 'Makanan']);

    foreach (range(1, 26) as $number) {
        Product::create([
            'category_id' => $category->id,
            'code' => "PRODUCT-{$number}",
            'name' => "Produk {$number}",
            'price' => 1000,
            'stock' => 10,
        ]);
    }

    $response = $this->actingAs($user)->get(route('products.index', ['per_page' => 25]));

    $response->assertOk()
        ->assertViewHas('products', fn ($products) => str_contains($products->nextPageUrl(), 'per_page=25'));
});

it('rejects a page size that is not available', function () {
    $user = User::factory()->create();
    $user->forceFill(['role' => 'admin'])->save();

    $response = $this->from(route('products.index'))
        ->actingAs($user)
        ->get(route('products.index', ['per_page' => 5000]));

    $response->assertRedirect(route('products.index'))
        ->assertSessionHasErrors([
            'per_page' => 'Pilih jumlah data per halaman yang tersedia.',
        ]);
});
