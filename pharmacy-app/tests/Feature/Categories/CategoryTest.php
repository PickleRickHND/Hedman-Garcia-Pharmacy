<?php

use App\Models\Category;
use App\Models\Product;

it('allows admin to view categories page', function () {
    loginAs('Administrador');

    $this->get(route('categories.index'))
        ->assertOk();
});

it('blocks non-admin from categories page', function () {
    loginAs('Cajero');

    $this->get(route('categories.index'))
        ->assertForbidden();
});

it('allows admin to create a category', function () {
    loginAs('Administrador');

    \Livewire\Livewire::test(\App\Livewire\Categories\Index::class)
        ->set('name', 'Analgesicos')
        ->set('description', 'Para el dolor')
        ->set('color', '#ef4444')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('categories', [
        'name' => 'Analgesicos',
        'color' => '#ef4444',
    ]);
});

it('validates unique category name', function () {
    loginAs('Administrador');

    Category::create(['name' => 'Duplicada', 'color' => '#000000']);

    \Livewire\Livewire::test(\App\Livewire\Categories\Index::class)
        ->set('name', 'Duplicada')
        ->call('save')
        ->assertHasErrors('name');
});

it('allows admin to update a category', function () {
    loginAs('Administrador');

    $category = Category::create(['name' => 'Vieja', 'color' => '#000000']);

    \Livewire\Livewire::test(\App\Livewire\Categories\Index::class)
        ->call('startEdit', $category->id)
        ->set('editName', 'Nueva')
        ->set('editColor', '#ff0000')
        ->call('update')
        ->assertHasNoErrors();

    expect($category->fresh()->name)->toBe('Nueva');
});

it('allows admin to delete a category and nullifies products', function () {
    loginAs('Administrador');

    $category = Category::create(['name' => 'Borrar', 'color' => '#000000']);
    $product = Product::factory()->create(['category_id' => $category->id]);

    \Livewire\Livewire::test(\App\Livewire\Categories\Index::class)
        ->call('delete', $category->id);

    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    expect($product->fresh()->category_id)->toBeNull();
});

it('shows product count per category', function () {
    loginAs('Administrador');

    $category = Category::create(['name' => 'Con productos', 'color' => '#000000']);
    Product::factory()->count(3)->create(['category_id' => $category->id]);

    \Livewire\Livewire::test(\App\Livewire\Categories\Index::class)
        ->assertSee('Con productos')
        ->assertSee('3');
});
