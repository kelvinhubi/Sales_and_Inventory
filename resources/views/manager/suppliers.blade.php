@extends('manager.olayouts.main')
@section('content')

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <style>
            .content-wrapper {
                background-color: #f4f4f4;
            }

            .supplier-card {
                transition: all 0.3s ease;
            }

            .supplier-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            .info-box {
                display: flex;
                align-items: center;
                min-height: 90px;
                background: #fff;
                width: 100%;
                box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
                border-radius: .25rem;
                position: relative;
            }

            .info-box .info-box-icon {
                border-radius: .25rem;
                align-items: center;
                display: flex;
                font-size: 1.8rem;
                justify-content: center;
                text-align: center;
                width: 70px;
                background-color: rgba(0, 0, 0, .15);
            }

            .info-box .info-box-content {
                display: flex;
                flex-direction: column;
                justify-content: center;
                line-height: 1.8;
                padding: 5px 10px;
                flex: 1;
            }

            .info-box .info-box-text {
                display: block;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                font-weight: 600;
                color: #6c757d;
            }

            .info-box .info-box-number {
                display: block;
                font-weight: 700;
                font-size: 1.5rem;
            }

            .bg-info {
                background-color: #17a2b8 !important;
            }

            .bg-success {
                background-color: #28a745 !important;
            }

            .bg-warning {
                background-color: #ffc107 !important;
            }

            .status-badge {
                padding: 0.25rem 0.75rem;
                border-radius: 0.25rem;
                font-size: 0.875rem;
                font-weight: 600;
            }

            .status-active {
                background-color: #d4edda;
                color: #155724;
            }

            .status-inactive {
                background-color: #f8d7da;
                color: #721c24;
            }

            .table-responsive {
                border-radius: 0.25rem;
            }

            .card {
                box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
            }

            .btn-group-sm > .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
            }
        </style>
    </head>

    <body>
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Supplier Management</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Suppliers</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Statistics Row -->
                    <div class="row">
                        <div class="col-lg-4 col-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info elevation-1">
                                    <i class="fas fa-truck"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Suppliers</span>
                                    <span class="info-box-number" id="totalSuppliers">0</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success elevation-1">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Active Suppliers</span>
                                    <span class="info-box-number" id="activeSuppliers">0</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning elevation-1">
                                    <i class="fas fa-pause-circle"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Inactive Suppliers</span>
                                    <span class="info-box-number" id="inactiveSuppliers">0</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Suppliers Table Card -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">All Suppliers</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-primary" data-toggle="modal"
                                            data-target="#addSupplierModal">
                                            <i class="fas fa-plus"></i> Add Supplier
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Search and Filter -->
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" id="searchSupplier"
                                                placeholder="Search suppliers...">
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-control" id="filterStatus">
                                                <option value="">All Status</option>
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-control" id="sortSupplier">
                                                <option value="name">Sort by Name</option>
                                                <option value="company">Sort by Company</option>
                                                <option value="recent">Recently Added</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <button class="btn btn-secondary btn-block" id="resetFilters">
                                                <i class="fas fa-redo"></i> Reset
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Suppliers Table -->
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Supplier Name</th>
                                                    <th>Company</th>
                                                    <th>Contact Person</th>
                                                    <th>Email</th>
                                                    <th>Phone</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="suppliersTableBody">
                                                <tr>
                                                    <td colspan="8" class="text-center">Loading suppliers...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Add Supplier Modal -->
        <div class="modal fade" id="addSupplierModal" tabindex="-1" role="dialog" aria-labelledby="addSupplierModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="addSupplierModalLabel">
                            <i class="fas fa-truck"></i> Add New Supplier
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="addSupplierForm">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="add_name">Supplier Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="add_name" name="name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="add_company">Company</label>
                                        <input type="text" class="form-control" id="add_company" name="company">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="add_contact_person">Contact Person</label>
                                        <input type="text" class="form-control" id="add_contact_person" name="contact_person">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="add_email">Email</label>
                                        <input type="email" class="form-control" id="add_email" name="email">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="add_phone">Phone</label>
                                        <input type="text" class="form-control" id="add_phone" name="phone">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="add_status">Status</label>
                                        <select class="form-control" id="add_status" name="status">
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="add_address">Address</label>
                                <textarea class="form-control" id="add_address" name="address" rows="2"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="add_notes">Notes</label>
                                <textarea class="form-control" id="add_notes" name="notes" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Supplier
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Supplier Modal -->
        <div class="modal fade" id="editSupplierModal" tabindex="-1" role="dialog" aria-labelledby="editSupplierModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title" id="editSupplierModalLabel">
                            <i class="fas fa-edit"></i> Edit Supplier
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="editSupplierForm">
                        <input type="hidden" id="edit_supplier_id">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_name">Supplier Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="edit_name" name="name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_company">Company</label>
                                        <input type="text" class="form-control" id="edit_company" name="company">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_contact_person">Contact Person</label>
                                        <input type="text" class="form-control" id="edit_contact_person" name="contact_person">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_email">Email</label>
                                        <input type="email" class="form-control" id="edit_email" name="email">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_phone">Phone</label>
                                        <input type="text" class="form-control" id="edit_phone" name="phone">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_status">Status</label>
                                        <select class="form-control" id="edit_status" name="status">
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="edit_address">Address</label>
                                <textarea class="form-control" id="edit_address" name="address" rows="2"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="edit_notes">Notes</label>
                                <textarea class="form-control" id="edit_notes" name="notes" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-warning text-white">
                                <i class="fas fa-save"></i> Update Supplier
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- View Supplier Details Modal -->
        <div class="modal fade" id="viewSupplierModal" tabindex="-1" role="dialog" aria-labelledby="viewSupplierModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="viewSupplierModalLabel">
                            <i class="fas fa-info-circle"></i> Supplier Details
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Supplier Name:</strong> <span id="view_name"></span></p>
                                <p><strong>Company:</strong> <span id="view_company"></span></p>
                                <p><strong>Contact Person:</strong> <span id="view_contact_person"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Email:</strong> <span id="view_email"></span></p>
                                <p><strong>Phone:</strong> <span id="view_phone"></span></p>
                                <p><strong>Status:</strong> <span id="view_status"></span></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <p><strong>Address:</strong></p>
                                <p id="view_address" class="text-muted"></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <p><strong>Notes:</strong></p>
                                <p id="view_notes" class="text-muted"></p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                // Load suppliers on page load
                loadSuppliers();

                // Search functionality
                $('#searchSupplier').on('keyup', function() {
                    loadSuppliers();
                });

                // Filter by status
                $('#filterStatus').on('change', function() {
                    loadSuppliers();
                });

                // Sort functionality
                $('#sortSupplier').on('change', function() {
                    loadSuppliers();
                });

                // Reset filters
                $('#resetFilters').on('click', function() {
                    $('#searchSupplier').val('');
                    $('#filterStatus').val('');
                    $('#sortSupplier').val('name');
                    loadSuppliers();
                });

                // Add supplier form submission
                $('#addSupplierForm').on('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = {
                        name: $('#add_name').val(),
                        company: $('#add_company').val(),
                        contact_person: $('#add_contact_person').val(),
                        email: $('#add_email').val(),
                        phone: $('#add_phone').val(),
                        address: $('#add_address').val(),
                        notes: $('#add_notes').val(),
                        status: $('#add_status').val()
                    };

                    $.ajax({
                        url: '/api/suppliers',
                        type: 'POST',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $('#addSupplierModal').modal('hide');
                            $('#addSupplierForm')[0].reset();
                            loadSuppliers();
                            showNotification('Success', 'Supplier added successfully!', 'success');
                        },
                        error: function(xhr) {
                            let errorMessage = 'Failed to add supplier';
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                            }
                            showNotification('Error', errorMessage, 'error');
                        }
                    });
                });

                // Edit supplier form submission
                $('#editSupplierForm').on('submit', function(e) {
                    e.preventDefault();
                    
                    const supplierId = $('#edit_supplier_id').val();
                    const formData = {
                        name: $('#edit_name').val(),
                        company: $('#edit_company').val(),
                        contact_person: $('#edit_contact_person').val(),
                        email: $('#edit_email').val(),
                        phone: $('#edit_phone').val(),
                        address: $('#edit_address').val(),
                        notes: $('#edit_notes').val(),
                        status: $('#edit_status').val()
                    };

                    $.ajax({
                        url: `/api/suppliers/${supplierId}`,
                        type: 'PUT',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $('#editSupplierModal').modal('hide');
                            loadSuppliers();
                            showNotification('Success', 'Supplier updated successfully!', 'success');
                        },
                        error: function(xhr) {
                            let errorMessage = 'Failed to update supplier';
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                            }
                            showNotification('Error', errorMessage, 'error');
                        }
                    });
                });
            });

            // Load suppliers function
            function loadSuppliers() {
                const search = $('#searchSupplier').val();
                const status = $('#filterStatus').val();
                const sort = $('#sortSupplier').val();

                $.ajax({
                    url: '/api/suppliers',
                    type: 'GET',
                    data: { search, status, sort },
                    success: function(response) {
                        displaySuppliers(response);
                        updateStatistics(response);
                    },
                    error: function() {
                        $('#suppliersTableBody').html(
                            '<tr><td colspan="8" class="text-center text-danger">Failed to load suppliers</td></tr>'
                        );
                    }
                });
            }

            // Display suppliers in table
            function displaySuppliers(suppliers) {
                const tbody = $('#suppliersTableBody');
                tbody.empty();

                if (suppliers.length === 0) {
                    tbody.html('<tr><td colspan="8" class="text-center">No suppliers found</td></tr>');
                    return;
                }

                suppliers.forEach(supplier => {
                    const statusBadge = supplier.status === 'active' 
                        ? '<span class="status-badge status-active">Active</span>' 
                        : '<span class="status-badge status-inactive">Inactive</span>';

                    const row = `
                        <tr>
                            <td>${supplier.id}</td>
                            <td>${supplier.name || '-'}</td>
                            <td>${supplier.company || '-'}</td>
                            <td>${supplier.contact_person || '-'}</td>
                            <td>${supplier.email || '-'}</td>
                            <td>${supplier.phone || '-'}</td>
                            <td>${statusBadge}</td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button class="btn btn-info" onclick="viewSupplier(${supplier.id})" title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-warning" onclick="editSupplier(${supplier.id})" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger" onclick="deleteSupplier(${supplier.id})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            }

            // Update statistics
            function updateStatistics(suppliers) {
                const total = suppliers.length;
                const active = suppliers.filter(s => s.status === 'active').length;
                const inactive = suppliers.filter(s => s.status === 'inactive').length;

                $('#totalSuppliers').text(total);
                $('#activeSuppliers').text(active);
                $('#inactiveSuppliers').text(inactive);
            }

            // View supplier details
            function viewSupplier(id) {
                $.ajax({
                    url: `/api/suppliers/${id}`,
                    type: 'GET',
                    success: function(supplier) {
                        $('#view_name').text(supplier.name || '-');
                        $('#view_company').text(supplier.company || '-');
                        $('#view_contact_person').text(supplier.contact_person || '-');
                        $('#view_email').text(supplier.email || '-');
                        $('#view_phone').text(supplier.phone || '-');
                        $('#view_status').html(
                            supplier.status === 'active' 
                                ? '<span class="status-badge status-active">Active</span>' 
                                : '<span class="status-badge status-inactive">Inactive</span>'
                        );
                        $('#view_address').text(supplier.address || 'No address provided');
                        $('#view_notes').text(supplier.notes || 'No notes');
                        $('#viewSupplierModal').modal('show');
                    },
                    error: function() {
                        showNotification('Error', 'Failed to load supplier details', 'error');
                    }
                });
            }

            // Edit supplier
            function editSupplier(id) {
                $.ajax({
                    url: `/api/suppliers/${id}`,
                    type: 'GET',
                    success: function(supplier) {
                        $('#edit_supplier_id').val(supplier.id);
                        $('#edit_name').val(supplier.name);
                        $('#edit_company').val(supplier.company);
                        $('#edit_contact_person').val(supplier.contact_person);
                        $('#edit_email').val(supplier.email);
                        $('#edit_phone').val(supplier.phone);
                        $('#edit_address').val(supplier.address);
                        $('#edit_notes').val(supplier.notes);
                        $('#edit_status').val(supplier.status);
                        $('#editSupplierModal').modal('show');
                    },
                    error: function() {
                        showNotification('Error', 'Failed to load supplier data', 'error');
                    }
                });
            }

            // Delete supplier
            function deleteSupplier(id) {
                if (confirm('Are you sure you want to delete this supplier? This action cannot be undone.')) {
                    $.ajax({
                        url: `/api/suppliers/${id}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function() {
                            loadSuppliers();
                            showNotification('Success', 'Supplier deleted successfully!', 'success');
                        },
                        error: function() {
                            showNotification('Error', 'Failed to delete supplier', 'error');
                        }
                    });
                }
            }

            // Show notification (using SweetAlert2 or native alert as fallback)
            function showNotification(title, message, type) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: title,
                        html: message,
                        icon: type,
                        timer: 3000,
                        showConfirmButton: false
                    });
                } else {
                    alert(`${title}: ${message}`);
                }
            }
        </script>
    </body>
@endsection
