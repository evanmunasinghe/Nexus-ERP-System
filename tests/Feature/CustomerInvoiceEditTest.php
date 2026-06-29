<?php

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated users can update customer records', function () {
    $admin = User::factory()->create();
    $customer = Customer::create([
        'name' => 'Old Customer',
        'email' => 'old@example.com',
        'phone' => '555-1000',
        'address' => 'Old address',
    ]);

    $response = $this
        ->actingAs($admin)
        ->put(route('customers.update', $customer), [
            'name' => 'Updated Customer',
            'email' => 'updated@example.com',
            'phone' => '555-2000',
            'address' => 'Updated address',
        ]);

    $response->assertRedirect(route('customers.index'));

    expect($customer->refresh())
        ->name->toBe('Updated Customer')
        ->email->toBe('updated@example.com')
        ->phone->toBe('555-2000')
        ->address->toBe('Updated address');
});

test('authenticated users can update invoices and stock is adjusted', function () {
    $admin = User::factory()->create();
    $customer = Customer::create([
        'name' => 'Customer One',
        'email' => 'customer-one@example.com',
        'phone' => '555-1000',
        'address' => 'Customer address',
    ]);
    $product = Product::create([
        'name' => 'Product One',
        'code' => 'SKU-001',
        'cost' => 10,
        'price' => 25,
        'quantity' => 8,
    ]);
    $invoice = Invoice::create([
        'customer_id' => $customer->id,
        'invoice_number' => 'INV-TEST-001',
        'invoice_date' => '2026-06-29',
        'total_amount' => 50,
    ]);
    $invoice->items()->create([
        'product_id' => $product->id,
        'quantity' => 2,
        'unit_price' => 25,
        'subtotal' => 50,
    ]);
    $product->decrement('quantity', 2);

    $response = $this
        ->actingAs($admin)
        ->put(route('invoices.update', $invoice), [
            'customer_id' => $customer->id,
            'invoice_date' => '2026-06-30',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 4,
                ],
            ],
        ]);

    $response->assertRedirect(route('invoices.index'));

    $invoice->refresh();
    $invoiceItem = $invoice->items()->first();

    expect($invoice->invoice_date->toDateString())->toBe('2026-06-30');
    expect($invoice->total_amount)->toBe('100.00');
    expect($invoiceItem->quantity)->toBe(4);
    expect($invoiceItem->subtotal)->toBe('100.00');
    expect($product->refresh()->quantity)->toBe(4);
});
