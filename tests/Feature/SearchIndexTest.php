<?php

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('users can be searched by name or email', function () {
    $admin = User::factory()->create();
    User::factory()->create([
        'name' => 'Alice Searchable',
        'email' => 'alice@example.com',
    ]);
    User::factory()->create([
        'name' => 'Bob Hidden',
        'email' => 'bob@example.com',
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('users.index', ['search' => 'Alice']));

    $response
        ->assertSuccessful()
        ->assertSee('Alice Searchable')
        ->assertDontSee('Bob Hidden');
});

test('customers can be searched by contact details', function () {
    $admin = User::factory()->create();
    Customer::create([
        'name' => 'Northwind Tools',
        'email' => 'northwind@example.com',
        'phone' => '555-1000',
        'address' => 'Harbor Street',
    ]);
    Customer::create([
        'name' => 'Southridge Supply',
        'email' => 'southridge@example.com',
        'phone' => '555-2000',
        'address' => 'Market Road',
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('customers.index', ['search' => 'Harbor']));

    $response
        ->assertSuccessful()
        ->assertSee('Northwind Tools')
        ->assertDontSee('Southridge Supply');
});

test('products can be searched by name sku or description', function () {
    $admin = User::factory()->create();
    Product::create([
        'name' => 'Hydraulic Pump',
        'code' => 'PUMP-SEARCH',
        'cost' => 75,
        'price' => 125,
        'quantity' => 10,
        'description' => 'Pressure control assembly',
    ]);
    Product::create([
        'name' => 'Steel Valve',
        'code' => 'VALVE-HIDDEN',
        'cost' => 20,
        'price' => 35,
        'quantity' => 8,
        'description' => 'Valve assembly',
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('products.index', ['search' => 'PUMP-SEARCH']));

    $response
        ->assertSuccessful()
        ->assertSee('Hydraulic Pump')
        ->assertDontSee('Steel Valve');
});

test('invoices can be searched by invoice or customer details', function () {
    $admin = User::factory()->create();
    $matchingCustomer = Customer::create([
        'name' => 'Acme Billing',
        'email' => 'billing@acme.test',
        'phone' => '555-3000',
        'address' => 'Invoice Avenue',
    ]);
    $hiddenCustomer = Customer::create([
        'name' => 'Hidden Billing',
        'email' => 'billing@hidden.test',
        'phone' => '555-4000',
        'address' => 'Hidden Avenue',
    ]);
    Invoice::create([
        'customer_id' => $matchingCustomer->id,
        'invoice_number' => 'INV-SEARCH-001',
        'invoice_date' => '2026-06-29',
        'total_amount' => 100,
    ]);
    Invoice::create([
        'customer_id' => $hiddenCustomer->id,
        'invoice_number' => 'INV-HIDDEN-001',
        'invoice_date' => '2026-06-30',
        'total_amount' => 200,
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('invoices.index', ['search' => 'Acme']));

    $response
        ->assertSuccessful()
        ->assertSee('INV-SEARCH-001')
        ->assertDontSee('INV-HIDDEN-001');
});
