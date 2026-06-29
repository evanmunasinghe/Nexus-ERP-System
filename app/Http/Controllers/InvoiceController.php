<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();

        return view('components.invoices.index', [
            'invoices' => Invoice::query()
                ->with(['customer', 'items'])
                ->when($search !== '', function ($query) use ($search): void {
                    $query->where(function ($query) use ($search): void {
                        $query
                            ->where('invoice_number', 'like', "%{$search}%")
                            ->orWhere('invoice_date', 'like', "%{$search}%")
                            ->orWhereHas('customer', function ($query) use ($search): void {
                                $query
                                    ->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            });
                    });
                })
                ->latest()
                ->get(),
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('components.invoices.create', [
            'customers' => Customer::query()
                ->orderBy('name')
                ->get(),
            'products' => Product::query()
                ->where('quantity', '>', 0)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function show(Invoice $invoice): View
    {
        return view('components.invoices.show', [
            'invoice' => $invoice->load(['customer', 'items.product']),
        ]);
    }

    public function edit(Invoice $invoice): View
    {
        return view('components.invoices.edit', [
            'invoice' => $invoice->load(['items.product', 'customer']),
            'customers' => Customer::query()
                ->orderBy('name')
                ->get(),
            'products' => Product::query()
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'invoice_date' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($validated): void {
            $totalAmount = 0;
            $invoiceItems = [];

            foreach ($validated['items'] as $item) {
                $product = Product::query()
                    ->whereKey($item['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if (! $product->hasStock((int) $item['quantity'])) {
                    abort(422, "{$product->name} does not have enough stock.");
                }

                $subtotal = (float) $product->price * (int) $item['quantity'];
                $totalAmount += $subtotal;

                $invoiceItems[] = [
                    'product_id' => $product->id,
                    'quantity' => (int) $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal,
                ];

                $product->decrement('quantity', (int) $item['quantity']);
            }

            $invoice = Invoice::create([
                'customer_id' => $validated['customer_id'],
                'invoice_number' => 'INV-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
                'invoice_date' => $validated['invoice_date'],
                'total_amount' => $totalAmount,
            ]);

            $invoice->items()->createMany($invoiceItems);
        });

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Invoice created and stock updated.');
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'invoice_date' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($validated, $invoice): void {
            $invoice = Invoice::query()
                ->whereKey($invoice->id)
                ->lockForUpdate()
                ->firstOrFail();

            $invoice->load('items');

            foreach ($invoice->items as $invoiceItem) {
                Product::query()
                    ->whereKey($invoiceItem->product_id)
                    ->lockForUpdate()
                    ->firstOrFail()
                    ->increment('quantity', $invoiceItem->quantity);
            }

            $totalAmount = 0;
            $invoiceItems = [];

            foreach ($validated['items'] as $item) {
                $product = Product::query()
                    ->whereKey($item['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if (! $product->hasStock((int) $item['quantity'])) {
                    throw ValidationException::withMessages([
                        'items' => "{$product->name} does not have enough stock.",
                    ]);
                }

                $subtotal = (float) $product->price * (int) $item['quantity'];
                $totalAmount += $subtotal;

                $invoiceItems[] = [
                    'product_id' => $product->id,
                    'quantity' => (int) $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal,
                ];

                $product->decrement('quantity', (int) $item['quantity']);
            }

            $invoice->update([
                'customer_id' => $validated['customer_id'],
                'invoice_date' => $validated['invoice_date'],
                'total_amount' => $totalAmount,
            ]);

            $invoice->items()->delete();
            $invoice->items()->createMany($invoiceItems);
        });

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Invoice updated and stock adjusted.');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $invoice->delete();

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Invoice removed.');
    }
}
