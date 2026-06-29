<x-layout>
    <x-slot:title>Product Inventory | Nexus ERP</x-slot:title>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="fa-solid fa-boxes-stacked text-secondary me-2"></i> Product Inventory</h2>
            <p class="text-muted small mb-0">Monitor catalog pricing, track active manufacturing costs, and audit warehouse stock levels.</p>
        </div>
        <button type="button" class="btn btn-primary px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createProductModal">
            <i class="fa-solid fa-plus me-2"></i> Add New Product
        </button>
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

    <x-searchbar
        :action="route('products.index')"
        placeholder="Search products by name, SKU, or description..."
        :value="$search"
    />

    <x-card>
        <x-table :headers="[
            'SKU Code',
            'Product Description',
            ['text' => 'Cost Price', 'align' => 'end'],
            ['text' => 'Selling Price', 'align' => 'end'],
            ['text' => 'Available Stock', 'align' => 'center'],
            ['text' => 'Actions', 'align' => 'center', 'class' => 'no-print'],
        ]">
            @forelse($products as $product)
                <tr>
                    <td><code class="text-dark fw-bold">{{ $product->code }}</code></td>
                    <td>
                        <div class="fw-semibold text-dark">{{ $product->name }}</div>
                        <span class="text-muted small d-block text-truncate" style="max-width: 300px;">{{ $product->description ?? 'No description provided' }}</span>
                    </td>
                    <td class="text-end">${{ number_format($product->cost, 2) }}</td>
                    <td class="text-end fw-semibold text-primary">${{ number_format($product->price, 2) }}</td>
                    <td class="text-center">
                        @if($product->quantity <= 0)
                            <span class="badge bg-danger px-3 py-2 rounded-pill w-75"><i class="fa-solid fa-triangle-exclamation me-1"></i> Out of Stock</span>
                        @elseif($product->quantity <= 10)
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill w-75"><i class="fa-solid fa-circle-exclamation me-1"></i> Low Stock ({{ $product->quantity }})</span>
                        @else
                            <span class="badge bg-success px-3 py-2 rounded-pill w-75"><i class="fa-solid fa-check me-1"></i> {{ $product->quantity }} Available</span>
                        @endif
                    </td>
                    <td class="text-center no-print">
                        <div class="btn-group shadow-sm">
                            <button class="btn btn-sm btn-outline-secondary" title="Edit Product" data-bs-toggle="modal" data-bs-target="#editProductModal{{ $product->id }}">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to completely remove this product from inventory controls?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Product">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="fa-solid fa-box-open fs-2 d-block mb-3 text-secondary opacity-50"></i>
                        No inventory items found in the database directory.
                    </td>
                </tr>
            @endforelse
        </x-table>
    </x-card>

    <div class="modal fade" id="createProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-box me-2 text-info"></i> Register New Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-shadow="none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('products.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted">Product Name</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g., Hydraulic Pump" maxlength="255" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted">SKU / Item Code</label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="e.g., IND-PMP-01" maxlength="255" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted">Cost Price ($)</label>
                                <input type="number" step="0.01" name="cost" class="form-control @error('cost') is-invalid @enderror" value="{{ old('cost') }}" placeholder="0.00" min="0" required>
                                @error('cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted">Retail Price ($)</label>
                                <input type="number" step="0.01" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" placeholder="0.00" min="0" required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted">Initial Stock Quantity</label>
                                <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" placeholder="0" min="0" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted">Specification Description</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Optional specifications or physical storage locations...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-layout>
