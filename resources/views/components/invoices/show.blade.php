<x-layout>
    <x-slot:title>{{ $invoice->invoice_number }} | Printable Invoice</x-slot:title>

    <style>
        .invoice-print-sheet {
            max-width: 960px;
            margin: 0 auto;
            background: #fff;
        }

        .invoice-meta {
            border-left: 4px solid #0d6efd;
        }

        @media print {
            body {
                background: #fff !important;
            }

            #wrapper {
                display: block !important;
            }

            .container-fluid {
                padding: 0 !important;
            }

            .invoice-print-sheet {
                max-width: none;
                margin: 0;
                box-shadow: none !important;
                border: 0 !important;
            }

            .invoice-print-sheet .card-body {
                padding: 0 !important;
            }

            .invoice-table th,
            .invoice-table td {
                padding: 0.55rem 0.75rem !important;
            }
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h2 class="mb-1"><i class="fa-solid fa-print text-secondary me-2"></i> Print Invoice</h2>
            <p class="text-muted small mb-0">Review and print {{ $invoice->invoice_number }}.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary px-4">
                <i class="fa-solid fa-arrow-left me-2"></i> Back
            </a>
            <button type="button" class="btn btn-primary px-4 shadow-sm" onclick="window.print()">
                <i class="fa-solid fa-print me-2"></i> Print
            </button>
        </div>
    </div>

    <x-card class="invoice-print-sheet">
        <div class="d-flex justify-content-between align-items-start gap-4 border-bottom pb-4 mb-4">
            <div>
                <div class="text-uppercase text-muted small fw-bold mb-2">Nexus ERP</div>
                <h1 class="h3 fw-bold mb-1">Invoice</h1>
                <p class="text-muted mb-0">Industrial Administration Panel</p>
            </div>
            <div class="text-end invoice-meta ps-3">
                <div class="text-muted small fw-semibold">Invoice Number</div>
                <div class="fs-5 fw-bold">{{ $invoice->invoice_number }}</div>
                <div class="text-muted small mt-2">Date</div>
                <div class="fw-semibold">{{ $invoice->invoice_date?->format('M d, Y') }}</div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="text-uppercase text-muted small fw-bold mb-2">Bill To</div>
                <div class="fw-bold fs-5">{{ $invoice->customer?->name ?? 'Unknown customer' }}</div>
                <div>{{ $invoice->customer?->email ?? 'No email recorded' }}</div>
                <div>{{ $invoice->customer?->phone ?? 'No phone recorded' }}</div>
                <div class="text-muted">{{ $invoice->customer?->address ?? 'No address recorded' }}</div>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="text-uppercase text-muted small fw-bold mb-2">Record Details</div>
                <div>Created: {{ $invoice->created_at?->format('M d, Y h:i A') }}</div>
                <div>Items: {{ $invoice->items->count() }}</div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle invoice-table">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $item->product?->name ?? 'Deleted product' }}</div>
                                <div class="text-muted small">{{ $item->product?->code ?? 'No SKU' }}</div>
                            </td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">LKR {{ number_format((float) $item->unit_price, 2) }}</td>
                            <td class="text-end fw-semibold">LKR {{ number_format((float) $item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end border-0 pt-4">Total</th>
                        <th class="text-end border-0 pt-4 fs-5">LKR {{ number_format((float) $invoice->total_amount, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="border-top pt-4 mt-4">
            <div class="row g-4">
                <div class="col-md-7">
                    <div class="text-uppercase text-muted small fw-bold mb-2">Notes</div>
                    <p class="text-muted mb-0">Thank you for your business. This document was generated from Nexus ERP invoice records.</p>
                </div>
                <div class="col-md-5">
                    <div class="border-top mt-5 pt-2 text-center text-muted small">Authorized Signature</div>
                </div>
            </div>
        </div>
    </x-card>
</x-layout>
