@extends('manager.olayouts.main')
@section('content')
    <style>
        .order-amount {
            font-weight: 600;
            color: #28a745;
            font-size: 1.1rem;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }
        @media (max-width: 767.98px) {
            .info-box, .small-box, .card {
                margin-bottom: 1rem;
            }
            .table-responsive {
                overflow-x: auto;
            }
            .card-header .card-title {
                font-size: 1rem;
            }
            .input-group-text, .form-control {
                font-size: 0.95rem;
            }
            .btn {
                font-size: 0.95rem;
                padding: 0.4rem 0.7rem;
            }
        }
    </style>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">PAST ORDERS</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Past Orders</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <!-- Info boxes -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $stats['total_orders'] ?? 0 }}</h3>
                                <p>Total Orders</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-list"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>₱{{ number_format($stats['total_amount'] ?? 0, 2) }}</h3>
                                <p>Total Amount</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $stats['orders_today'] ?? 0 }}</h3>
                                <p>Orders Today</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{ $stats['orders_this_month'] ?? 0 }}</h3>
                                <p>Orders This Month</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Search and Filter Section -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-search mr-1"></i> Search & Filter</h3>
                    </div>
                    <div class="card-body">
                        <form class="row g-2" method="GET" action="{{ route('manager.past-orders.index') }}">
                            <div class="col-12 col-md-4 mb-2">
                                <label class="form-label text-muted small mb-1">Search:</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" name="search" class="form-control" placeholder="Search by brand, branch, or order ID..." value="{{ request('search', '') }}">
                                </div>
                            </div>
                            <div class="col-6 col-md-2 mb-2">
                                <label class="form-label text-muted small mb-1">From Date:</label>
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" max="{{ date('Y-m-d') }}" title="Cannot select future dates">
                                <small class="text-muted">Max: Today</small>
                            </div>
                            <div class="col-6 col-md-2 mb-2">
                                <label class="form-label text-muted small mb-1">To Date:</label>
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" max="{{ date('Y-m-d') }}" title="Cannot select future dates">
                                <small class="text-muted">Max: Today</small>
                            </div>
                            <div class="col-6 col-md-2 mb-2">
                                <label class="form-label text-muted small mb-1">Sort Order:</label>
                                <select name="sort_direction" class="form-control">
                                    <option value="desc" {{ request('sort_direction', 'desc') == 'desc' ? 'selected' : '' }}>Newest First</option>
                                    <option value="asc" {{ request('sort_direction') == 'asc' ? 'selected' : '' }}>Oldest First</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-2 mb-2">
                                <label class="form-label text-muted small mb-1">Branch:</label>
                                <select name="branch_search" class="form-control">
                                    <option value="">All Branches</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ request('branch_search') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-2 mb-2">
                                <label class="form-label text-muted small mb-1">Brand:</label>
                                <select name="brand_search" class="form-control">
                                    <option value="">All Brands</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ request('brand_search') == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-1 mb-2">
                                <label class="form-label text-muted small mb-1">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter mr-1"></i>Filter</button>
                            </div>
                            <div class="col-6 col-md-1 mb-2">
                                <label class="form-label text-muted small mb-1">&nbsp;</label>
                                <a href="{{ route('manager.past-orders.index') }}" class="btn btn-secondary w-100"><i class="fas fa-times mr-1"></i>Clear</a>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Orders Table -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-list mr-1"></i> Past Orders List</h3>
                        <div class="card-tools">
                            <button type="button" id="deleteSelectedBtn" class="btn btn-danger btn-sm" style="display: none;">
                                <i class="fas fa-trash mr-1"></i>Delete Selected (<span id="selectedCount">0</span>)
                            </button>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAllOrders"></th>
                                    <th>Order ID</th>
                                    <th>DR Number</th>
                                    <th>Brand</th>
                                    <th>Branch</th>
                                    <th>Total Amount</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pastOrders as $pastOrder)
                                <tr>
                                    <td><input type="checkbox" class="order-checkbox" value="{{ $pastOrder->id }}" data-amount="{{ $pastOrder->total_amount }}"></td>
                                    <td>{{ $pastOrder->id }}</td>
                                    <td>
                                        <span class="badge badge-primary">{{ $pastOrder->dr_number ?? 'No DR' }}</span>
                                    </td>
                                    <td>{{ $pastOrder->brand->name ?? 'N/A' }}</td>
                                    <td>{{ $pastOrder->branch->name ?? 'N/A' }}</td>
                                    <td class="order-amount">₱{{ number_format($pastOrder->total_amount, 2) }}</td>
                                    <td>{{ $pastOrder->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('manager.past-orders.show', $pastOrder) }}" class="btn btn-info btn-sm"><i class="fas fa-eye mr-1"></i>View</a>
                                        <form method="POST" action="{{ route('manager.past-orders.destroy', $pastOrder) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this past order?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash mr-1"></i>Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-inbox fa-3x mb-3" style="color: #d1d5db;"></i>
                                            <h5>No Past Orders Found</h5>
                                            <p class="mb-0">No past orders available at the moment.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer clearfix">
                        <div class="float-left">
                            <span class="text-muted">
                                Showing <span id="showingStart">{{ $showingStart ?? 0 }}</span> to <span id="showingEnd">{{ $showingEnd ?? 0 }}</span>
                                of <span id="totalEntries">{{ $totalEntries ?? 0 }}</span> entries
                            </span>
                            <div class="mt-2">
                                <span class="text-info">
                                    <strong>Selected: <span id="selectedCount">0</span> orders</strong> | 
                                    <strong>Total Amount: ₱<span id="selectedTotal">0.00</span></strong>
                                </span>
                            </div>
                        </div>
                        <div class="float-right">
                            <div class="mb-2">
                                <form id="exportSelectedForm" method="GET" action="{{ route('manager.past-orders.exportSelected') }}" style="display:inline;" class="mr-2">
                                    <input type="hidden" name="selected_orders" id="selectedOrdersInput">
                                    <input type="hidden" name="start_date" id="exportStartDateInput">
                                    <input type="hidden" name="end_date" id="exportEndDateInput">
                                    <input type="hidden" name="search" id="exportSearchInput">
                                    <input type="hidden" name="branch_search" id="exportBranchSearchInput">
                                    <input type="hidden" name="brand_search" id="exportBrandSearchInput">
                                    <button type="submit" class="btn btn-info" id="exportSelectedBtn" disabled><i class="fas fa-file-excel mr-1"></i>Export Selected to Excel</button>
                                </form>
                                <form id="summaryReportForm" method="GET" action="{{ route('manager.past-orders.summaryReport') }}" style="display:inline;">
                                    <input type="hidden" name="selected_orders" id="summarySelectedOrdersInput">
                                    <input type="hidden" name="start_date" id="summaryStartDateInput">
                                    <input type="hidden" name="end_date" id="summaryEndDateInput">
                                    <input type="hidden" name="search" id="summarySearchInput">
                                    <input type="hidden" name="branch_search" id="summaryBranchSearchInput">
                                    <input type="hidden" name="brand_search" id="summaryBrandSearchInput">
                                    <button type="submit" class="btn btn-success" id="summaryReportBtn" disabled><i class="fas fa-chart-bar mr-1"></i>Summary Report</button>
                                </form>
                                <form id="deleteSelectedForm" method="POST" action="{{ route('manager.past-orders.deleteSelected') }}" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="selected_orders" id="deleteSelectedOrdersInput">
                                </form>
                            </div>
                            {{ $pastOrders->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Date validation - prevent future dates
    const today = new Date().toISOString().split('T')[0];
    const startDateInput = document.querySelector('input[name="start_date"]');
    const endDateInput = document.querySelector('input[name="end_date"]');
    
    if (startDateInput) {
        startDateInput.addEventListener('change', function() {
            if (this.value > today) {
                alert('Start date cannot be in the future. Setting to today.');
                this.value = today;
            }
            // Ensure end date is not before start date
            if (endDateInput && endDateInput.value && this.value > endDateInput.value) {
                endDateInput.value = this.value;
            }
        });
    }
    
    if (endDateInput) {
        endDateInput.addEventListener('change', function() {
            if (this.value > today) {
                alert('End date cannot be in the future. Setting to today.');
                this.value = today;
            }
            // Ensure end date is not before start date
            if (startDateInput && startDateInput.value && this.value < startDateInput.value) {
                alert('End date cannot be before start date. Adjusting start date.');
                startDateInput.value = this.value;
            }
        });
    }

    const selectAllCheckbox = document.getElementById('selectAllOrders');
    const orderCheckboxes = document.querySelectorAll('.order-checkbox');
    const selectedCountSpan = document.getElementById('selectedCount');
    const selectedTotalSpan = document.getElementById('selectedTotal');
    const exportSelectedBtn = document.getElementById('exportSelectedBtn');
    const exportSelectedForm = document.getElementById('exportSelectedForm');
    const selectedOrdersInput = document.getElementById('selectedOrdersInput');
    const summaryReportBtn = document.getElementById('summaryReportBtn');
    const summaryReportForm = document.getElementById('summaryReportForm');
    const summarySelectedOrdersInput = document.getElementById('summarySelectedOrdersInput');
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
    const deleteSelectedForm = document.getElementById('deleteSelectedForm');
    const deleteSelectedOrdersInput = document.getElementById('deleteSelectedOrdersInput');

    // Function to update selection display
    function updateSelectionDisplay() {
        const checkedBoxes = document.querySelectorAll('.order-checkbox:checked');
        const count = checkedBoxes.length;
        let total = 0;

        checkedBoxes.forEach(checkbox => {
            total += parseFloat(checkbox.dataset.amount || 0);
        });

        selectedCountSpan.textContent = count;
        selectedTotalSpan.textContent = total.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        // Enable/disable buttons based on selection
        const hasSelection = count > 0;
        exportSelectedBtn.disabled = !hasSelection;
        summaryReportBtn.disabled = !hasSelection;

        // Show/hide delete button and update selected count
        if (hasSelection) {
            exportSelectedBtn.classList.remove('btn-secondary');
            exportSelectedBtn.classList.add('btn-info');
            summaryReportBtn.classList.remove('btn-secondary');
            summaryReportBtn.classList.add('btn-success');
            deleteSelectedBtn.style.display = 'inline-block';
            deleteSelectedBtn.querySelector('#selectedCount').textContent = count;
        } else {
            exportSelectedBtn.classList.remove('btn-info');
            exportSelectedBtn.classList.add('btn-secondary');
            summaryReportBtn.classList.remove('btn-success');
            summaryReportBtn.classList.add('btn-secondary');
            deleteSelectedBtn.style.display = 'none';
        }
    }

    // Select/Deselect all functionality
    selectAllCheckbox.addEventListener('change', function() {
        const isChecked = this.checked;
        orderCheckboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
        updateSelectionDisplay();
    });

    // Individual checkbox change handler
    orderCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Update select all checkbox state
            const totalCheckboxes = orderCheckboxes.length;
            const checkedCheckboxes = document.querySelectorAll('.order-checkbox:checked').length;
            
            selectAllCheckbox.indeterminate = checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes;
            selectAllCheckbox.checked = checkedCheckboxes === totalCheckboxes;
            
            updateSelectionDisplay();
        });
    });

    // Export selected orders to Excel
    exportSelectedForm.addEventListener('submit', function(e) {
        const selectedIds = Array.from(document.querySelectorAll('.order-checkbox:checked'))
                                 .map(cb => cb.value);
        
        if (selectedIds.length === 0) {
            e.preventDefault();
            alert('Please select at least one order to export.');
            return;
        }
        
        selectedOrdersInput.value = selectedIds.join(',');
        
        // Update ALL filter fields from the current filter inputs
        const startDateFilter = document.querySelector('input[name="start_date"]:not(#exportStartDateInput)');
        const endDateFilter = document.querySelector('input[name="end_date"]:not(#exportEndDateInput)');
        const searchFilter = document.querySelector('input[name="search"]');
        const branchSearchFilter = document.querySelector('select[name="branch_search"]');
        const brandSearchFilter = document.querySelector('select[name="brand_search"]');
        
        const exportStartDateInput = document.getElementById('exportStartDateInput');
        const exportEndDateInput = document.getElementById('exportEndDateInput');
        const exportSearchInput = document.getElementById('exportSearchInput');
        const exportBranchSearchInput = document.getElementById('exportBranchSearchInput');
        const exportBrandSearchInput = document.getElementById('exportBrandSearchInput');
        
        // Update date filters
        if (startDateFilter && exportStartDateInput) {
            exportStartDateInput.value = startDateFilter.value || '';
        }
        if (endDateFilter && exportEndDateInput) {
            exportEndDateInput.value = endDateFilter.value || '';
        }
        
        // Update search filter
        if (searchFilter && exportSearchInput) {
            exportSearchInput.value = searchFilter.value || '';
        }
        
        // Update branch filter
        if (branchSearchFilter && exportBranchSearchInput) {
            exportBranchSearchInput.value = branchSearchFilter.value || '';
        }
        
        // Update brand filter
        if (brandSearchFilter && exportBrandSearchInput) {
            exportBrandSearchInput.value = brandSearchFilter.value || '';
        }
        
        console.log('Export form submission with all filters:', {
            start: exportStartDateInput?.value,
            end: exportEndDateInput?.value,
            search: exportSearchInput?.value,
            branch: exportBranchSearchInput?.value,
            brand: exportBrandSearchInput?.value,
            orders: selectedIds.length
        });
    });

    // Summary report form submission
    summaryReportForm.addEventListener('submit', function(e) {
        const selectedIds = Array.from(document.querySelectorAll('.order-checkbox:checked'))
                                 .map(cb => cb.value);
        
        if (selectedIds.length === 0) {
            e.preventDefault();
            alert('Please select at least one order to generate summary report.');
            return;
        }
        
        summarySelectedOrdersInput.value = selectedIds.join(',');
        
        // Update ALL filter fields from the current filter inputs
        const startDateFilter = document.querySelector('input[name="start_date"]:not(#summaryStartDateInput)');
        const endDateFilter = document.querySelector('input[name="end_date"]:not(#summaryEndDateInput)');
        const searchFilter = document.querySelector('input[name="search"]');
        const branchSearchFilter = document.querySelector('select[name="branch_search"]');
        const brandSearchFilter = document.querySelector('select[name="brand_search"]');
        
        const summaryStartDateInput = document.getElementById('summaryStartDateInput');
        const summaryEndDateInput = document.getElementById('summaryEndDateInput');
        const summarySearchInput = document.getElementById('summarySearchInput');
        const summaryBranchSearchInput = document.getElementById('summaryBranchSearchInput');
        const summaryBrandSearchInput = document.getElementById('summaryBrandSearchInput');
        
        // Update date filters
        if (startDateFilter && summaryStartDateInput) {
            summaryStartDateInput.value = startDateFilter.value || '';
        }
        if (endDateFilter && summaryEndDateInput) {
            summaryEndDateInput.value = endDateFilter.value || '';
        }
        
        // Update search filter
        if (searchFilter && summarySearchInput) {
            summarySearchInput.value = searchFilter.value || '';
        }
        
        // Update branch filter
        if (branchSearchFilter && summaryBranchSearchInput) {
            summaryBranchSearchInput.value = branchSearchFilter.value || '';
        }
        
        // Update brand filter
        if (brandSearchFilter && summaryBrandSearchInput) {
            summaryBrandSearchInput.value = brandSearchFilter.value || '';
        }
        
        console.log('Summary form submission with all filters:', {
            start: summaryStartDateInput?.value,
            end: summaryEndDateInput?.value,
            search: summarySearchInput?.value,
            branch: summaryBranchSearchInput?.value,
            brand: summaryBrandSearchInput?.value,
            orders: selectedIds.length
        });
    });

    // Delete selected orders
    deleteSelectedBtn.addEventListener('click', function(e) {
        const selectedIds = Array.from(document.querySelectorAll('.order-checkbox:checked'))
                                 .map(cb => cb.value);
        
        console.log('Selected IDs:', selectedIds);
        console.log('Delete form action:', deleteSelectedForm.action);
        
        if (selectedIds.length === 0) {
            alert('Please select at least one order to delete.');
            return;
        }
        
        const confirmMessage = `Are you sure you want to delete ${selectedIds.length} selected past order${selectedIds.length > 1 ? 's' : ''}? This action cannot be undone.`;
        
        if (confirm(confirmMessage)) {
            deleteSelectedOrdersInput.value = selectedIds.join(',');
            console.log('Submitting form with IDs:', deleteSelectedOrdersInput.value);
            deleteSelectedForm.submit();
        }
    });

    // Initialize display
    updateSelectionDisplay();
});
</script>
@endsection
