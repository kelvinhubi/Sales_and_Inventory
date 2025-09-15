<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Rejected Goods - Manager Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">   
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="{{ asset('js/heartbeat.js') }}"></script>
    <style>
        .content-wrapper {
            background-color: #f4f4f4;
        }
        #dr-info-section .form-control {
            border: 2px solid #28a745;
            background-color: #f8f9fa !important;
            color: #495057;
            font-weight: 500;
        }
        .dr-selection-highlight {
            border: 2px solid #007bff;
            border-radius: 5px;
            background-color: #f8f9fa;
        }
    </style>
    @push('styles')
    <style>
        .content-wrapper {
            margin-left: 260px !important;
            position: relative !important;
            z-index: 1 !important;
        }
        .main-sidebar {
            z-index: 1000 !important;
        }
    </style>
    @endpush
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        @include('manager.olayouts.header')
        @include('manager.olayouts.sidebar')
        <div class="content-wrapper" style="margin-left: 260px; position: relative; z-index: 1;">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Create Rejected Goods</h1>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-sm-right">
                                <a href="{{ route('manager.rejected-goods.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-plus-circle mr-2 text-primary"></i>New Rejected Goods
                                    </h5>
                                </div>
                                <form method="POST" action="{{ route('manager.rejected-goods.store') }}" id="rejected-goods-form">
                                    @csrf
                                    <div class="card-body">
                                        <!-- DR Number Selection - Top Priority -->
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group dr-selection-highlight p-3">
                                                    <label for="dr_no"><i class="fas fa-receipt mr-2 text-primary"></i>DR No <span class="text-danger">*</span></label>
                                                    <select class="form-control @error('dr_no') is-invalid @enderror" id="dr_no" name="dr_no" required>
                                                        <option value="">-- Select DR Number --</option>
                                                        @foreach($drNumbers as $drNumber)
                                                            <option value="{{ $drNumber }}" {{ old('dr_no') == $drNumber ? 'selected' : '' }}>
                                                                {{ $drNumber }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('dr_no')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    @if(count($drNumbers) == 0)
                                                        <small class="text-muted">No DR numbers available. Please create some past orders first.</small>
                                                    @else
                                                        <small class="text-muted">Select a DR number to automatically populate brand and branch information.</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Auto-populated Brand and Branch Display -->
                                        <div class="row" id="dr-info-section" style="display: none;">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Brand</label>
                                                    <div class="form-control bg-light" id="brand_display">-</div>
                                                    <small class="text-success">Auto-populated from DR</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Branch</label>
                                                    <div class="form-control bg-light" id="branch_display">-</div>
                                                    <small class="text-success">Auto-populated from DR</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Hidden inputs for brand_id and branch_id -->
                                        <input type="hidden" id="brand_id" name="brand_id" value="{{ old('brand_id') }}">
                                        <input type="hidden" id="branch_id" name="branch_id" value="{{ old('branch_id') }}">
                                        
                                        <!-- Date and other fields -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="date">Date <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date') }}" required>
                                                    @error('date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="amount">Amount <span class="text-danger">*</span></label>
                                                    <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount') }}" required readonly>
                                                    @error('amount')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="reason">Reason <span class="text-danger">*</span></label>
                                                    <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="3" required>{{ old('reason') }}</textarea>
                                                    @error('reason')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Product Items <span class="text-danger">*</span></label>
                                            <div id="items-container">
                                                <!-- Dynamic rows will be added here -->
                                            </div>
                                            <button type="button" class="btn btn-secondary mt-2" onclick="addItemRow()">
                                                <i class="fas fa-plus mr-2"></i>Add Item
                                            </button>
                                            @error('product_items')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-2"></i>Create Rejected Goods
                                        </button>
                                        <a href="{{ route('manager.rejected-goods.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left mr-2"></i>Back
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        @include('manager.olayouts.footer')
    </div>
    <script>
    let itemIndex = 0;
    let selectedProducts = new Set();

    // Handle DR number selection change to auto-populate brand and branch
    document.addEventListener('DOMContentLoaded', function() {
        const drSelect = document.getElementById('dr_no');
        const brandHiddenInput = document.getElementById('brand_id');
        const branchHiddenInput = document.getElementById('branch_id');
        const brandDisplay = document.getElementById('brand_display');
        const branchDisplay = document.getElementById('branch_display');
        const drInfoSection = document.getElementById('dr-info-section');
        
        drSelect.addEventListener('change', function() {
            const drNumber = this.value;
            
            if (drNumber) {
                // Show loading state
                brandDisplay.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                branchDisplay.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                drInfoSection.style.display = 'flex';
                
                // Fetch DR details
                fetch(`{{ url('manager/rejected-goods/dr-details') }}/${drNumber}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert('Error: ' + data.error);
                            drInfoSection.style.display = 'none';
                            return;
                        }
                        
                        // Set hidden input values
                        brandHiddenInput.value = data.brand_id;
                        branchHiddenInput.value = data.branch_id;
                        
                        // Update display elements
                        brandDisplay.innerHTML = `<i class="fas fa-check text-success mr-2"></i>${data.brand_name}`;
                        branchDisplay.innerHTML = `<i class="fas fa-check text-success mr-2"></i>${data.branch_name}`;
                    })
                    .catch(error => {
                        console.error('Error fetching DR details:', error);
                        alert('Error fetching DR details. Please try again.');
                        drInfoSection.style.display = 'none';
                    });
            } else {
                // Reset when no DR is selected
                brandHiddenInput.value = '';
                branchHiddenInput.value = '';
                drInfoSection.style.display = 'none';
            }
        });
    });

    // Function to create filtered product options HTML
    function createProductOptions(preserveProductId = null) {
        let optionsHtml = '<option value="">Select Product</option>';
        
        // Available products data
        const availableProducts = [
            @foreach($products as $product)
            {
                id: {{ $product->id }},
                name: "{{ $product->name }}",
                price: "{{ $product->price }}"
            },
            @endforeach
        ];
        
        availableProducts.forEach(function(product) {
            // Include if not selected by others, or if we need to preserve this specific product
            if (!selectedProducts.has(product.id) || product.id == preserveProductId) {
                optionsHtml += `<option value="${product.id}" data-price="${product.price}">${product.name}</option>`;
            }
        });
        
        return optionsHtml;
    }

    // Function to refresh all product selects with current filtered options
    function refreshProductSelects(currentSelect) {
        const rows = document.querySelectorAll('.form-row.mb-3');
        rows.forEach(function(row) {
            const productSelect = row.querySelector('select[name*="product_id"]');
            if (productSelect && productSelect !== currentSelect) {
                const currentValue = parseInt(productSelect.value) || null;
                
                // Create options while preserving the current selection
                productSelect.innerHTML = createProductOptions(currentValue);
                
                // Restore the current value
                if (currentValue) {
                    productSelect.value = currentValue;
                }
            }
        });
    }

    // Function to update selected products when a product is changed
    function updateSelectedProduct(select) {
        const productId = parseInt(select.value) || null;
        const oldId = select.dataset.oldValue ? parseInt(select.dataset.oldValue) : null;
        
        console.log('Updating product selection:', {
            oldId: oldId,
            newId: productId,
            selectedProducts: Array.from(selectedProducts)
        });
        
        // Remove old selection if it exists and is different
        if (oldId && oldId !== productId) {
            selectedProducts.delete(oldId);
        }
        
        // Add new selection if valid
        if (productId && productId > 0) {
            selectedProducts.add(productId);
        }
        
        // Update the old value tracker
        select.dataset.oldValue = productId || '';
        
        console.log('After update:', Array.from(selectedProducts));
        
        // Refresh other dropdowns
        refreshProductSelects(select);
        calculateTotalAmount();
    }

    function addItemRow() {
        const container = document.getElementById('items-container');
        const row = document.createElement('div');
        row.className = 'form-row mb-3';
        row.id = 'item-row-' + itemIndex;
        row.innerHTML = `
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label>Product <span class="text-danger">*</span></label>
                        <select class="form-control" name="product_items[${itemIndex}][product_id]" required>
                            ${createProductOptions()}
                        </select>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label>Quantity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="product_items[${itemIndex}][quantity]" min="1" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-danger" onclick="removeItemRow(${itemIndex})">
                            <i class="fas fa-trash mr-1"></i>Remove
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(row);
        
        // Add event listeners for real-time calculation and product selection
        const newProductSelect = row.querySelector('select[name*="product_id"]');
        const newQuantityInput = row.querySelector('input[name*="quantity"]');
        if (newProductSelect) {
            newProductSelect.addEventListener('change', function() {
                updateSelectedProduct(this);
            });
            newProductSelect.addEventListener('change', calculateTotalAmount);
        }
        if (newQuantityInput) {
            newQuantityInput.addEventListener('input', calculateTotalAmount);
        }
        
        itemIndex++;
    }

    function removeItemRow(index) {
        const row = document.getElementById('item-row-' + index);
        if (row) {
            const productSelect = row.querySelector('select[name*="product_id"]');
            if (productSelect && productSelect.value) {
                selectedProducts.delete(parseInt(productSelect.value));
            }
            row.remove();
            refreshProductSelects();
            calculateTotalAmount();
        }
    }

    // Auto-calculate total amount from product items
    function calculateTotalAmount() {
        let total = 0;
        const rows = document.querySelectorAll('.form-row.mb-3');
        rows.forEach(function(row) {
            const productSelect = row.querySelector('select[name*="product_id"]');
            const quantityInput = row.querySelector('input[name*="quantity"]');
            if (productSelect && quantityInput && productSelect.value) {
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                const price = parseFloat(selectedOption.dataset.price) || 0;
                const quantity = parseFloat(quantityInput.value) || 0;
                if (price > 0 && quantity > 0) {
                    total += price * quantity;
                }
            }
        });
        document.getElementById('amount').value = total.toFixed(2);
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        selectedProducts.clear();
        
        // Add event listeners to existing rows if any
        const existingRows = document.querySelectorAll('.form-row.mb-3');
        existingRows.forEach(function(row) {
            const productSelect = row.querySelector('select[name*="product_id"]');
            const quantityInput = row.querySelector('input[name*="quantity"]');
            
            if (productSelect) {
                // Set initial old value tracker
                if (productSelect.value) {
                    const productId = parseInt(productSelect.value);
                    selectedProducts.add(productId);
                    productSelect.dataset.oldValue = productId;
                }
                
                productSelect.addEventListener('change', function() {
                    updateSelectedProduct(this);
                });
                productSelect.addEventListener('change', calculateTotalAmount);
            }
            
            if (quantityInput) {
                quantityInput.addEventListener('input', calculateTotalAmount);
            }
        });
        
        // Initial refresh to update all dropdowns based on existing selections
        refreshProductSelects();
        
        console.log('Initialization complete. Selected products:', Array.from(selectedProducts));
    });
    </script>
    @stack('scripts')
</body>
</html>