<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows all products in the category and their total stock', function () {
    $user = User::factory()->create();
    $user->forceFill(['role' => 'admin'])->save();
    $category = Category::create([
        'name' => 'Makanan',
        'description' => 'Produk makanan',
    ]);

    Product::create([
        'category_id' => $category->id,
        'code' => 'FOOD-001',
        'name' => 'Biskuit',
        'unit' => 'pcs',
        'price' => 5000,
        'stock' => 4,
    ]);
    Product::create([
        'category_id' => $category->id,
        'code' => 'FOOD-002',
        'name' => 'Cokelat',
        'unit' => 'box',
        'price' => 12000,
        'stock' => 7,
    ]);
    $otherCategory = Category::create(['name' => 'Minuman']);
    Product::create([
        'category_id' => $otherCategory->id,
        'code' => 'DRINK-001',
        'name' => 'Air Mineral',
        'price' => 3000,
        'stock' => 20,
    ]);

    $response = $this->actingAs($user)->get(route('categories.show', $category));

    $response->assertOk()
        ->assertViewHas('category', fn ($shownCategory) => $shownCategory->is($category)
            && $shownCategory->products->pluck('code')->all() === ['FOOD-001', 'FOOD-002'])
        ->assertViewHas('totalStock', 11)
        ->assertSee('Produk makanan')
        ->assertSee('FOOD-001')
        ->assertSee('FOOD-002')
        ->assertDontSee('DRINK-001')
        ->assertSee('11');
});

it('shows an empty product list and zero stock for an empty category', function () {
    $user = User::factory()->create();
    $user->forceFill(['role' => 'admin'])->save();
    $category = Category::create(['name' => 'Kategori Kosong']);

    $response = $this->actingAs($user)->get(route('categories.show', $category));

    $response->assertOk()
        ->assertViewHas('totalStock', 0)
        ->assertSee('Belum ada produk dalam kategori ini.')
        ->assertSee('Kategori Kosong');
});

it('links to category details from the category list', function () {
    $user = User::factory()->create();
    $user->forceFill(['role' => 'admin'])->save();
    $category = Category::create(['name' => 'Makanan']);

    $response = $this->actingAs($user)->get(route('categories.index'));

    $response->assertOk()
        ->assertSee(route('categories.show', $category));
});

it('forbids cashiers from viewing category details', function () {
    $user = User::factory()->create();
    $user->forceFill(['role' => 'kasir'])->save();
    $category = Category::create(['name' => 'Makanan']);

    $response = $this->actingAs($user)->get(route('categories.show', $category));

    $response->assertForbidden();
});
