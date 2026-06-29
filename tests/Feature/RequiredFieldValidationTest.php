<?php

use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user forms validate required fields', function () {
    $admin = User::factory()->create();

    $response = $this
        ->actingAs($admin)
        ->post(route('users.store'), []);

    $response->assertSessionHasErrors(['name', 'email', 'password']);
});

test('customer forms validate required fields', function () {
    $admin = User::factory()->create();

    $response = $this
        ->actingAs($admin)
        ->post(route('customers.store'), []);

    $response->assertSessionHasErrors(['name', 'email', 'phone', 'address']);
});

test('product forms validate required fields', function () {
    $admin = User::factory()->create();

    $response = $this
        ->actingAs($admin)
        ->post(route('products.store'), []);

    $response->assertSessionHasErrors(['name', 'code', 'cost', 'price', 'quantity']);
});

test('invoice forms validate required fields', function () {
    $admin = User::factory()->create();

    $response = $this
        ->actingAs($admin)
        ->post(route('invoices.store'), []);

    $response->assertSessionHasErrors(['customer_id', 'invoice_date', 'items']);
});

test('invoice forms validate required line item fields', function () {
    $admin = User::factory()->create();
    $customer = Customer::create([
        'name' => 'Validation Customer',
        'email' => 'validation-customer@example.com',
        'phone' => '555-4000',
        'address' => 'Validation address',
    ]);
    Product::create([
        'name' => 'Validation Product',
        'code' => 'VALIDATION-001',
        'cost' => 10,
        'price' => 20,
        'quantity' => 5,
    ]);

    $response = $this
        ->actingAs($admin)
        ->post(route('invoices.store'), [
            'customer_id' => $customer->id,
            'invoice_date' => '2026-06-29',
            'items' => [
                [
                    'product_id' => '',
                    'quantity' => '',
                ],
            ],
        ]);

    $response->assertSessionHasErrors(['items.0.product_id', 'items.0.quantity']);
});

test('required field forms render successfully', function () {
    $admin = User::factory()->create();
    $customer = Customer::create([
        'name' => 'Render Customer',
        'email' => 'render-customer@example.com',
        'phone' => '555-5000',
        'address' => 'Render address',
    ]);
    $product = Product::create([
        'name' => 'Render Product',
        'code' => 'RENDER-001',
        'cost' => 10,
        'price' => 20,
        'quantity' => 5,
    ]);
    $invoice = $customer->invoices()->create([
        'invoice_number' => 'INV-RENDER-001',
        'invoice_date' => '2026-06-29',
        'total_amount' => 20,
    ]);
    $invoice->items()->create([
        'product_id' => $product->id,
        'quantity' => 1,
        'unit_price' => 20,
        'subtotal' => 20,
    ]);

    $this->actingAs($admin)->get(route('users.index'))->assertSuccessful();
    $this->actingAs($admin)->get(route('customers.index'))->assertSuccessful();
    $this->actingAs($admin)->get(route('products.index'))->assertSuccessful();
    $this->actingAs($admin)->get(route('invoices.create'))->assertSuccessful();
    $this->actingAs($admin)->get(route('invoices.edit', $invoice))->assertSuccessful();
});
