<x-layout>
    <x-slot:title>Invoices | Nexus ERP</x-slot:title>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="fa-solid fa-file-invoice-dollar text-secondary me-2"></i> Invoices</h2>
            <p class="text-muted small mb-0">Review customer invoices and create new stock-linked transactions.</p>
        </div>
        <a href="{{ route('invoices.create') }}" class="btn btn-primary px-4 shadow-sm">
            <i class="fa-solid fa-plus me-2"></i> New Invoice
        </a>
    </div>

    <x-searchbar
        id="invoices-search-form"
        :action="route('invoices.index')"
        target="#invoices-search-results"
        placeholder="Search invoices by number, date, customer, or email..."
        :value="$search"
    />

    <div id="invoices-search-results">
        <x-card>
            <x-table :headers="[
                'Invoice',
                'Customer',
                'Date',
                ['text' => 'Items', 'align' => 'center'],
                ['text' => 'Total', 'align' => 'end'],
                ['text' => 'Actions', 'align' => 'center', 'class' => 'no-print'],
            ]">
                @forelse($invoices as $invoice)
                    <tr>
                        <td><code class="text-dark fw-bold">{{ $invoice->invoice_number }}</code></td>
                        <td>{{ $invoice->customer?->name ?? 'Unknown customer' }}</td>
                        <td>{{ $invoice->invoice_date?->format('M d, Y') }}</td>
                        <td class="text-center">{{ $invoice->items->count() }}</td>
                        <td class="text-end fw-semibold">LKR {{ number_format($invoice->total_amount, 2) }}</td>
                        <td class="text-center no-print">
                            <div class="btn-group shadow-sm">
                                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-outline-primary" title="View Printable Invoice">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm btn-outline-secondary" title="Edit Invoice">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this invoice record?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Invoice">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">No invoices created yet.</td>
                    </tr>
                @endforelse
            </x-table>
        </x-card>
    </div>
</x-layout>
