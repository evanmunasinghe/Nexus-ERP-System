<?php

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated users can view a printable invoice', function () {
    $admin = User::factory()->create();
    $customer = Customer::create([
        'name' => 'Acme Industries',
        'email' => 'billing@acme.test',
        'phone' => '555-3000',
        'address' => '100 Factory Road',
    ]);
    $product = Product::create([
        'name' => 'Hydraulic Pump',
        'code' => 'PUMP-001',
        'cost' => 75,
        'price' => 125,
        'quantity' => 10,
    ]);
    $invoice = Invoice::create([
        'customer_id' => $customer->id,
        'invoice_number' => 'INV-PRINT-001',
        'invoice_date' => '2026-06-29',
        'total_amount' => 250,
    ]);
    $invoice->items()->create([
        'product_id' => $product->id,
        'quantity' => 2,
        'unit_price' => 125,
        'subtotal' => 250,
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('invoices.show', $invoice));

    $response
        ->assertSuccessful()
        ->assertSee('Printable Invoice')
        ->assertSee('INV-PRINT-001')
        ->assertSee('Acme Industries')
        ->assertSee('Hydraulic Pump')
        ->assertSee('LKR 250.00');
});
