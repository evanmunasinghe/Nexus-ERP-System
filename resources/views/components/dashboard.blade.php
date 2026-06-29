<x-layout>
    <x-slot:title>Dashboard | Nexus ERP</x-slot:title>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="fa-solid fa-gauge text-secondary me-2"></i> Dashboard</h2>
            <p class="text-muted small mb-0">Overview of users, customers, inventory, and billing activity.</p>
        </div>
        <a href="{{ route('invoices.create') }}" class="btn btn-primary px-4 shadow-sm">
            <i class="fa-solid fa-plus me-2"></i> New Invoice
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <x-card class="h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Users</div>
                        <div class="fs-3 fw-bold">{{ $userCount }}</div>
                    </div>
                    <i class="fa-solid fa-users-gear fs-2 text-secondary"></i>
                </div>
            </x-card>
        </div>
        <div class="col-md-3">
            <x-card class="h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Customers</div>
                        <div class="fs-3 fw-bold">{{ $customerCount }}</div>
                    </div>
                    <i class="fa-solid fa-address-book fs-2 text-info"></i>
                </div>
            </x-card>
        </div>
        <div class="col-md-3">
            <x-card class="h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Products</div>
                        <div class="fs-3 fw-bold">{{ $productCount }}</div>
                    </div>
                    <i class="fa-solid fa-boxes-stacked fs-2 text-success"></i>
                </div>
            </x-card>
        </div>
        <div class="col-md-3">
            <x-card class="h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Invoices</div>
                        <div class="fs-3 fw-bold">{{ $invoiceCount }}</div>
                    </div>
                    <i class="fa-solid fa-file-invoice-dollar fs-2 text-primary"></i>
                </div>
            </x-card>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <x-card title="Low Stock Products">
                <x-table :headers="[
                    'Product',
                    ['text' => 'Stock', 'align' => 'center'],
                ]">
                    @forelse($lowStockProducts as $product)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $product->name }}</div>
                                <code class="small text-muted">{{ $product->code }}</code>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $product->quantity <= 0 ? 'bg-danger' : 'bg-warning text-dark' }} rounded-pill px-3 py-2">{{ $product->quantity }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center py-4 text-muted">No low stock products.</td>
                        </tr>
                    @endforelse
                </x-table>
            </x-card>
        </div>

        <div class="col-lg-6">
            <x-card title="Recent Invoices">
                <x-table :headers="[
                    'Invoice',
                    'Customer',
                    ['text' => 'Total', 'align' => 'end'],
                ]">
                    @forelse($recentInvoices as $invoice)
                        <tr>
                            <td><code class="text-dark fw-bold">{{ $invoice->invoice_number }}</code></td>
                            <td>{{ $invoice->customer?->name ?? 'Unknown customer' }}</td>
                            <td class="text-end fw-semibold">LKR {{ number_format($invoice->total_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">No invoices created yet.</td>
                        </tr>
                    @endforelse
                </x-table>
            </x-card>
        </div>
    </div>
</x-layout>
