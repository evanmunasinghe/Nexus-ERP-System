<x-layout>
    <x-slot:title>Edit Invoice | Nexus ERP</x-slot:title>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="fa-solid fa-file-pen text-secondary me-2"></i> Edit Invoice</h2>
            <p class="text-muted small mb-0">{{ $invoice->invoice_number }} stock-linked transaction details.</p>
        </div>
        <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary px-4">
            <i class="fa-solid fa-arrow-left me-2"></i> Back
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show no-print" role="alert">
            <div class="fw-semibold mb-1">Please fix the following:</div>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @php
        $formItems = old('items') ?? $invoice->items->map(fn ($invoiceItem) => [
            'product_id' => $invoiceItem->product_id,
            'quantity' => $invoiceItem->quantity,
            'original_product_id' => $invoiceItem->product_id,
            'original_quantity' => $invoiceItem->quantity,
        ])->all();
    @endphp

    <form action="{{ route('invoices.update', $invoice) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-4">
                <x-card title="Invoice Configuration Matrix">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Customer Selection</label>
                        <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                            <option value="">-- Select Target Customer --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" @selected((int) old('customer_id', $invoice->customer_id) === $customer->id)>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="form-label fw-semibold text-muted">Billing Date</label>
                        <input type="date" name="invoice_date" class="form-control @error('invoice_date') is-invalid @enderror" value="{{ old('invoice_date', $invoice->invoice_date?->format('Y-m-d')) }}" required>
                        @error('invoice_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </x-card>
            </div>

            <div class="col-md-8">
                <x-card title="Line Items Collection">
                    <div id="invoice-items-container">
                        @foreach($formItems as $index => $invoiceItem)
                            <div class="row g-2 align-items-center mb-2 item-row">
                                <div class="col-md-7">
                                    <select name="items[{{ $index }}][product_id]" class="form-select @error('items.'.$index.'.product_id') is-invalid @enderror" required>
                                        <option value="">-- Choose Inventory Product --</option>
                                        @foreach($products as $product)
                                            @php
                                                $selectedProductId = (int) ($invoiceItem['product_id'] ?? '');
                                                $originalProductId = (int) ($invoiceItem['original_product_id'] ?? 0);
                                                $originalQuantity = (int) ($invoiceItem['original_quantity'] ?? 0);
                                                $availableStock = $product->quantity + ($originalProductId === $product->id ? $originalQuantity : 0);
                                            @endphp
                                            <option value="{{ $product->id }}" @selected($selectedProductId === $product->id)>
                                                {{ $product->name }} (Stock: {{ $availableStock }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('items.'.$index.'.product_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <input type="number" name="items[{{ $index }}][quantity]" class="form-control @error('items.'.$index.'.quantity') is-invalid @enderror" value="{{ $invoiceItem['quantity'] ?? '' }}" placeholder="Qty" min="1" required>
                                    @error('items.'.$index.'.quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-danger w-100 remove-item-btn" @disabled(count($formItems) === 1)>
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" id="add-item-btn" class="btn btn-sm btn-outline-primary mt-2"><i class="fa-solid fa-plus"></i> Append Item Row</button>
                </x-card>
            </div>
        </div>

        <div class="text-end mt-2">
            <button type="submit" class="btn btn-success btn-lg px-5 shadow-sm"><i class="fa-solid fa-floppy-disk"></i> Update Transaction</button>
        </div>
    </form>

    <x-slot:scripts>
        <script>
            let itemIndex = {{ count($formItems) }};
            document.getElementById('add-item-btn').addEventListener('click', function() {
                const container = document.getElementById('invoice-items-container');
                const firstRow = container.querySelector('.item-row');
                const newRow = firstRow.cloneNode(true);

                newRow.querySelector('select').name = `items[${itemIndex}][product_id]`;
                newRow.querySelector('select').value = '';
                newRow.querySelector('select').classList.remove('is-invalid');
                newRow.querySelector('input').name = `items[${itemIndex}][quantity]`;
                newRow.querySelector('input').value = '';
                newRow.querySelector('input').classList.remove('is-invalid');
                newRow.querySelectorAll('.invalid-feedback').forEach((feedback) => feedback.remove());
                newRow.querySelector('.remove-item-btn').removeAttribute('disabled');

                container.appendChild(newRow);
                itemIndex++;
            });

            document.getElementById('invoice-items-container').addEventListener('click', function(e) {
                if(e.target.closest('.remove-item-btn')) {
                    const row = e.target.closest('.item-row');
                    if(document.querySelectorAll('.item-row').length > 1) row.remove();
                }
            });
        </script>
    </x-slot:scripts>
</x-layout>
