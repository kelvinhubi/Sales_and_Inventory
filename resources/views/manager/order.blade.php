@extends('manager.olayouts.main')
@section('content')

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

        <style>
            .content-wrapper {
                background-color: #f4f4f4;
            }

            .order-card {
                transition: all 0.3s ease;
                border-left: 4px solid #007bff;
            }

            .order-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            }

            .stats-box {
                border-radius: 8px;
                overflow: hidden;
            }

            .orders-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
                gap: 20px;
            }

            .search-section {
                background: white;
                border-radius: 8px;
                padding: 20px;
                margin-bottom: 20px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            }

            .empty-state {
                text-align: center;
                padding: 60px 20px;
                background: white;
                border-radius: 8px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            }

            .empty-icon {
                font-size: 4rem;
                color: #d1d5db;
                margin-bottom: 20px;
            }

            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                min-width: 300px;
            }

            .loading-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9998;
            }

            .loading-spinner {
                width: 40px;
                height: 40px;
                border: 4px solid rgba(255, 255, 255, 0.3);
                border-top: 4px solid white;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
                }
            }

            .order-items-table {
                font-size: 0.9rem;
            }

            .order-total {
                background: #f8f9fa;
                border-radius: 6px;
                padding: 10px;
                font-weight: bold;
            }

            .branch-section {
                border: 1px solid #dee2e6;
                border-radius: 8px;
                margin-bottom: 20px;
                overflow: hidden;
            }

            .branch-header {
                background: #e9ecef;
                padding: 15px;
                border-bottom: 1px solid #dee2e6;
            }

            .final-total {
                background: #28a745;
                color: white;
                padding: 15px;
                border-radius: 8px;
                text-align: center;
                font-size: 1.2rem;
                font-weight: bold;
            }
        </style>
    </head>

    <body class="hold-transition sidebar-mini layout-fixed">
        <div class="wrapper">
            <div class="content-wrapper">
                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1 class="m-0">
                                    ORDER CREATION
                                </h1>
                            </div>
                            <div class="col-sm-6">
                                <div class="float-sm-right">
                                    <button class="btn btn-success btn-lg mr-2" id="finalOrderBtn">
                                        <i class="fas fa-file-invoice mr-2"></i>Final Order Summary
                                    </button>
                                    <button class="btn btn-primary btn-lg" id="addOrderBtn">
                                        <i class="fas fa-plus mr-2"></i>Add Order
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3 id="totalOrders">0</h3>
                                        <p>Total Orders</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                    <a href="#" class="small-box-footer">
                                        More info <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3 id="totalValue">₱0</h3>
                                        <p>Total Value</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-peso-sign"></i>
                                    </div>
                                    <a href="#" class="small-box-footer">
                                        More info <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3 id="totalBranches">0</h3>
                                        <p>Active Branches</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <a href="#" class="small-box-footer">
                                        More info <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3 id="avgOrderValue">₱0</h3>
                                        <p>Avg Order Value</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <a href="#" class="small-box-footer">
                                        More info <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="search-section">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fas fa-search"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control" id="searchInput"
                                            placeholder="Search orders...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control" id="brandFilter">
                                        <option value="">All Brands</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control" id="branchFilter">
                                        <option value="">All Branches</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-control" id="sortSelect">
                                        <option value="date">Sort by Date</option>
                                        <option value="total">Sort by Total</option>
                                        <option value="brand">Sort by Brand</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="orders-grid" id="ordersContainer">
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div class="modal fade" id="orderFormModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h4 class="modal-title text-white" id="orderFormTitle">Add New Order</h4>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form id="orderForm">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="orderBrand">Brand <span class="text-danger">*</span></label>
                                        <select class="form-control" id="orderBrand" required>
                                            <option value="">Select Brand</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="orderBranch">Branch <span class="text-danger">*</span></label>
                                        <select class="form-control" id="orderBranch" required>
                                            <option value="">Select Branch</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Order Items</label>
                                <div id="orderItemsContainer">
                                    <div class="order-item-row">
                                        <div class="row mb-2">
                                            <div class="col-md-4">
                                                <select class="form-control item-product" required>
                                                    <option value="">Select Product</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" class="form-control item-quantity"
                                                    placeholder="Qty" min="1" required>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" class="form-control item-price" placeholder="Price"
                                                    step="0.01" min="0" readonly>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="text-right font-weight-bold item-subtotal">₱0.00</div>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-danger btn-sm remove-item"
                                                    style="display: none;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-success btn-sm" id="addItemBtn">
                                    <i class="fas fa-plus mr-1"></i>Add Product
                                </button>
                            </div>

                            <div class="form-group">
                                <label for="orderNotes">Notes</label>
                                <textarea class="form-control" id="orderNotes" rows="3" placeholder="Additional notes..."></textarea>
                            </div>

                            <div class="order-total">
                                <div class="d-flex justify-content-between">
                                    <span>Total Amount:</span>
                                    <span id="orderTotalAmount">₱0.00</span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>Save Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="finalOrderModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success">
                        <h4 class="modal-title text-white">
                            <i class="fas fa-file-invoice mr-2"></i>
                            Final Order Summary
                        </h4>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="finalOrderContent">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" id="generatePdfBtn">
                            <i class="fas fa-file-pdf mr-2"></i>Generate PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // 🔧 API Configuration
            const API_BASE_URL = '/api';

            // 📊 Application State
            let orders = [];
            let brands = [];
            let branches = [];
            let products = [];
            let editingOrderId = null;
            let finalOrderSummary = {};
            // 🌐 API Helper Functions
            async function apiRequest(endpoint, options = {}) {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                    const response = await fetch(`${API_BASE_URL}${endpoint}`, {
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            ...(csrfToken && {
                                'X-CSRF-TOKEN': csrfToken
                            }),
                            ...options.headers
                        },
                        credentials: 'same-origin',
                        ...options
                    });

                    if (!response.ok) {
                        const errorData = await response.json().catch(() => ({}));

                        if (response.status === 422 && errorData.errors) {
                            const errorMessages = Object.values(errorData.errors).flat().join(', ');
                            throw new Error(errorMessages);
                        }

                        throw new Error(errorData.message || errorData.error ||
                            `HTTP ${response.status}: ${response.statusText}`);
                    }

                    const data = await response.json();
                    return data.data || data;

                } catch (error) {
                    console.error('API Error:', error);
                    showNotification(`Error: ${error.message}`, 'error');
                    throw error;
                }
            }

            // 📋 Order API Functions
            async function fetchOrders(searchTerm = '', brandId = '', branchId = '', sortBy = 'date') {
                const params = new URLSearchParams();
                if (searchTerm) params.append('search', searchTerm);
                if (brandId) params.append('brand_id', brandId);
                if (branchId) params.append('branch_id', branchId);
                if (sortBy) params.append('sort', sortBy);

                const queryString = params.toString();
                const endpoint = `/orders${queryString ? '?' + queryString : ''}`;

                orders = await apiRequest(endpoint);
                return orders;
            }

            async function createOrder(orderData) {
                return await apiRequest('/orders', {
                    method: 'POST',
                    body: JSON.stringify(orderData)
                });
            }

            async function updateOrder(orderId, orderData) {
                return await apiRequest(`/orders/${orderId}`, {
                    method: 'PUT',
                    body: JSON.stringify(orderData)
                });
            }

            async function deleteOrderAPI(orderId) {
                return await apiRequest(`/orders/${orderId}`, {
                    method: 'DELETE'
                });
            }

            async function fetchBrands() {
                brands = await apiRequest('/brands');
                return brands;
            }

            async function fetchBranches(brandId = null) {
                if (brandId) {
                    return await apiRequest(`/brands/${brandId}/branches`);
                } else {
                    branches = await apiRequest('/branches');
                    return branches;
                }
            }

            async function getFinalOrderSummary() {
                try {
                    return await apiRequest('/orders/final-summary');
                } catch (error) {
                    // If no orders exist, return empty structure
                    if (error.message.includes('No query results')) {
                        return {
                            brands: [],
                            total: 0
                        };
                    }
                    throw error;
                }
            }

            async function fetchProducts() {
                products = await apiRequest('/productss ');
                return products;
            }

            // 🔔 Notification System
            function showNotification(message, type = 'success') {
                $('.notification').remove();

                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

                const notification = $(`
                <div class="alert ${alertClass} alert-dismissible notification">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <h6><i class="fas ${iconClass} mr-2"></i>${type === 'success' ? 'Success!' : 'Error!'}</h6>
                    ${message}
                </div>
            `);

                $('body').append(notification);

                setTimeout(() => {
                    notification.fadeOut(() => notification.remove());
                }, 4000);
            }

            // 🔄 Loading State Management
            function showLoading(show = true) {
                if (show) {
                    if (!$('.loading-overlay').length) {
                        $('body').append(`
                        <div class="loading-overlay">
                            <div class="text-center">
                                <div class="loading-spinner"></div>
                                <div class="mt-3 text-white font-weight-bold">Loading...</div>
                            </div>
                        </div>
                    `);
                    }
                } else {
                    $('.loading-overlay').remove();
                }
            }

            // 🚀 Initialize Application
            $(document).ready(function() {
                setupEventListeners();
                loadInitialData();
            });

            async function loadInitialData() {
                try {
                    showLoading(true);
                    await Promise.all([
                        fetchOrders(),
                        fetchBrands(),
                        fetchBranches(),
                        fetchProducts()
                    ]);

                    populateFilters();
                    renderOrders();
                    showNotification('Data loaded successfully!');
                } catch (error) {
                    console.error('Failed to load initial data:', error);
                    showErrorState();
                } finally {
                    showLoading(false);
                }
            }

            function setupEventListeners() {
                $('#addOrderBtn').click(function(e) {
                    e.preventDefault();
                    openOrderForm();
                });
                $('#finalOrderBtn').click(showFinalOrderSummary);
                $('#orderForm').submit(handleOrderSubmit);
                $('#generatePdfBtn').click(generatePDF);

                // Dynamic item management
                $('#addItemBtn').click(addOrderItem);
                $(document).on('click', '.remove-item', removeOrderItem);
                $(document).on('input', '.item-quantity', calculateOrderTotal);
                $(document).on('change', '.item-product', handleProductSelection);

                // Brand/Branch cascading
                $('#orderBrand').change(handleBrandChange);

                // Search and filters
                let searchTimeout;
                $('#searchInput').on('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(handleSearch, 500);
                });

                $('#brandFilter, #branchFilter, #sortSelect').change(handleSearch);
            }

            function populateFilters() {
                // Populate brand filters
                const brandSelect = $('#orderBrand, #brandFilter');
                brandSelect.find('option:not(:first)').remove();

                brands.forEach(brand => {
                    brandSelect.append(`<option value="${brand.id}">${brand.name}</option>`);
                });

                // Populate branch filter (all branches)
                const branchFilter = $('#branchFilter');
                branchFilter.find('option:not(:first)').remove();

                branches.forEach(branch => {
                    branchFilter.append(`<option value="${branch.id}">${branch.name} (${branch.brand_name})</option>`);
                });

                // Populate product dropdowns in order form
                populateProductDropdowns();
            }

            async function handleBrandChange() {
                const brandId = $('#orderBrand').val();
                const branchSelect = $('#orderBranch');

                branchSelect.find('option:not(:first)').remove();
                
                // Clear existing items when brand changes
                $('#orderItemsContainer').empty();
                addOrderItem(); // Add one empty row by default

                if (brandId) {
                    try {
                        const [brandBranches, brandData] = await Promise.all([
                            fetchBranches(brandId),
                            apiRequest(`/brands/${brandId}`)
                        ]);

                        // Populate branches
                        brandBranches.forEach(branch => {
                            branchSelect.append(`<option value="${branch.id}">${branch.name}</option>`);
                        });

                        // Add standard items if they exist
                        if (brandData.standard_items && brandData.standard_items.length > 0) {
                            await addStandardItems(brandData.standard_items);
                        }
                    } catch (error) {
                        console.error('Failed to load brand data:', error);
                        showNotification('Failed to load brand data', 'error');
                    }
                }
            }

            function handleProductSelection(e) {
                const productSelect = $(e.target);
                const productId = productSelect.val();
                const itemRow = productSelect.closest('.order-item-row');
                const quantityInput = itemRow.find('.item-quantity');
                const priceInput = itemRow.find('.item-price');
                const subtotalDisplay = itemRow.find('.item-subtotal');
     
                // Clear stock info display
                itemRow.find('.stock-info').remove();
     
                if (productId) {
                    const product = products.find(p => p.id == productId);
                    if (product) {
                        priceInput.val(product.price);
                        
                        // Show stock information
                        const stockInfo = product.quantity > 0 ?
                            `<small class="text-muted stock-info d-block mt-1">
                                <i class="fas fa-box mr-1"></i>Available: ${product.quantity} units
                            </small>` :
                            `<small class="text-danger stock-info d-block mt-1">
                                <i class="fas fa-exclamation-triangle mr-1"></i>OUT OF STOCK
                            </small>`;
                        
                        // Insert stock info after quantity input
                        quantityInput.after(stockInfo);
                        
                        // Set max quantity based on stock
                        if (product.quantity > 0) {
                            quantityInput.attr('max', product.quantity);
                            quantityInput.attr('placeholder', `Max: ${product.quantity}`);
                            
                            // Add warning if stock is low
                            if (product.quantity <= 5) {
                                quantityInput.addClass('border-warning');
                                quantityInput.after(`<small class="text-warning low-stock-warning">Low stock warning!</small>`);
                            } else {
                                quantityInput.removeClass('border-warning');
                                itemRow.find('.low-stock-warning').remove();
                            }
                        } else {
                            // Disable quantity input if out of stock
                            quantityInput.attr('disabled', true);
                            quantityInput.attr('max', 0);
                            quantityInput.val('');
                            quantityInput.addClass('bg-light');
                        }
                        
                        calculateItemSubtotal(itemRow);
                        calculateOrderTotal();
                    }
                } else {
                    priceInput.val('');
                    quantityInput.removeAttr('max disabled');
                    quantityInput.removeClass('border-warning bg-light');
                    quantityInput.attr('placeholder', 'Qty');
                    subtotalDisplay.text('₱0.00');
                    itemRow.find('.stock-info, .low-stock-warning').remove();
                    calculateOrderTotal();
                }
            }

            function calculateItemSubtotal(itemRow) {
                const quantity = parseFloat(itemRow.find('.item-quantity').val()) || 0;
                const price = parseFloat(itemRow.find('.item-price').val()) || 0;
                const subtotal = quantity * price;

                itemRow.find('.item-subtotal').text(`₱${subtotal.toLocaleString('en-US', {minimumFractionDigits: 2})}`);
            }

            function updateStatistics() {
                const totalOrders = orders.length;
                const totalValue = orders.reduce((sum, order) => sum + parseFloat(order.total_amount || 0), 0);
                const uniqueBranches = new Set(orders.map(order => order.branch_id)).size;
                const avgOrderValue = totalOrders > 0 ? totalValue / totalOrders : 0;

                $('#totalOrders').text(totalOrders);
                $('#totalValue').text(`₱${totalValue.toLocaleString('en-US', {minimumFractionDigits: 2})}`);
                $('#totalBranches').text(uniqueBranches);
                $('#avgOrderValue').text(`₱${avgOrderValue.toLocaleString('en-US', {minimumFractionDigits: 2})}`);
            }

            function renderOrders(filteredOrders = orders) {
                const container = $('#ordersContainer');
                container.empty();

                updateStatistics();

                if (filteredOrders.length === 0) {
                    container.html(`
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h4 class="text-muted mb-3">No Orders Found</h4>
                        <p class="text-muted mb-4">Start by creating your first order.</p>
                        <button class="btn btn-primary btn-lg" onclick="openOrderForm()">
                            <i class="fas fa-plus mr-2"></i>Create First Order
                        </button>
                    </div>
                `);
                    return;
                }

                filteredOrders.forEach(order => {
                    const orderCard = createOrderCard(order);
                    container.append(orderCard);
                });
            }

            function createOrderCard(order) {
                const orderDate = new Date(order.created_at).toLocaleDateString();
                const itemsHtml = order.items.map(item => `
                <tr>
                    <td>${item.name}</td>
                    <td class="text-center">${item.quantity}</td>
                    <td class="text-right">₱${parseFloat(item.price).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                    <td class="text-right">₱${(item.quantity * item.price).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                </tr>
            `).join('');

                return $(`
                <div class="card order-card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-store text-primary mr-2"></i>
                                    ${order.brand_name}
                                </h5>
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    ${order.branch_name}
                                </small>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="#" onclick="editOrder(${order.id})">
                                        <i class="fas fa-edit text-info mr-2"></i>Edit Order
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="#" onclick="deleteOrder(${order.id})">
                                        <i class="fas fa-trash mr-2"></i>Delete Order
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm order-items-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-right">Price</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${itemsHtml}
                                </tbody>
                            </table>
                        </div>
                        ${order.notes ? `<div class="mt-2"><small class="text-muted"><i class="fas fa-sticky-note mr-1"></i>${order.notes}</small></div>` : ''}
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-calendar mr-1"></i>
                                ${orderDate}
                            </small>
                            <div class="order-total">
                                <strong>Total: ₱${parseFloat(order.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            `);
            }

            function openOrderForm(orderId = null) {
                editingOrderId = orderId;

                if (orderId) {
                    const order = orders.find(o => o.id === orderId);
                    $('#orderFormTitle').text('Edit Order');
                    populateOrderForm(order);
                } else {
                    $('#orderFormTitle').text('Add New Order');
                    resetOrderForm();
                }

                $('#orderFormModal').modal('show');
            }

            function populateOrderForm(order) {
                $('#orderBrand').val(order.brand_id).trigger('change');

                setTimeout(() => {
                    $('#orderBranch').val(order.branch_id);
                }, 500);

                $('#orderNotes').val(order.notes);

                // Clear existing items
                $('#orderItemsContainer').empty();

                // Add order items
                order.items.forEach((item, index) => {
                    addOrderItem();
                    const lastRow = $('#orderItemsContainer .order-item-row').last();

                    // Find product by name (since we store product name in order items)
                    const product = products.find(p => p.name === item.name);
                    if (product) {
                        lastRow.find('.item-product').val(product.id);
                    }

                    lastRow.find('.item-quantity').val(item.quantity);
                    lastRow.find('.item-price').val(item.price);
                    calculateItemSubtotal(lastRow);
                });

                calculateOrderTotal();
            }

            function resetOrderForm() {
                $('#orderForm')[0].reset();
                $('#orderBranch').find('option:not(:first)').remove();

                // Reset to single item row
                $('#orderItemsContainer').html(`
                <div class="order-item-row">
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <select class="form-control item-product" required>
                                <option value="">Select Product</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control item-quantity" placeholder="Qty" min="1" required>
                        </div>
                        <div class="col-md-3">
                            <input type="number" class="form-control item-price" placeholder="Price" step="0.01" min="0" readonly>
                        </div>
                        <div class="col-md-2">
                            <div class="text-right font-weight-bold item-subtotal">₱0.00</div>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-sm remove-item" style="display: none;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `);

                // Populate products in the new row
                populateProductDropdowns();

                $('#orderTotalAmount').text('₱0.00');
            }

            function addOrderItem() {
                const newItem = $(`
                <div class="order-item-row">
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <select class="form-control item-product" required>
                                <option value="">Select Product</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control item-quantity" placeholder="Qty" min="1" required>
                        </div>
                        <div class="col-md-3">
                            <input type="number" class="form-control item-price" placeholder="Price" step="0.01" min="0" readonly>
                        </div>
                        <div class="col-md-2">
                            <div class="text-right font-weight-bold item-subtotal">₱0.00</div>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-sm remove-item">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `);
 
            $('#orderItemsContainer').append(newItem);
 
            // Populate products in the new row with stock info
            const productSelect = newItem.find('.item-product');
            products.forEach(product => {
                const stockInfo = product.quantity > 0 ? ` (Stock: ${product.quantity})` : ' (OUT OF STOCK)';
                const optionText = product.quantity === 0 ?
                    `${product.name} - ₱${parseFloat(product.price).toLocaleString('en-US', {minimumFractionDigits: 2})}${stockInfo}` :
                    `${product.name} - ₱${parseFloat(product.price).toLocaleString('en-US', {minimumFractionDigits: 2})}${stockInfo}`;
                productSelect.append(
                    `<option value="${product.id}" data-stock="${product.quantity}" data-price="${product.price}">${optionText}</option>`
                );
            });
 
            updateRemoveButtons();
            return newItem; // Return the new row for chaining
        }

            function removeOrderItem(e) {
                $(e.target).closest('.order-item-row').remove();
                updateRemoveButtons();
                calculateOrderTotal();
            }

            function populateProductDropdowns() {
                $('.item-product').each(function() {
                    const select = $(this);
                    select.find('option:not(:first)').remove();
     
                    products.forEach(product => {
                        const stockInfo = product.quantity > 0 ? ` (Stock: ${product.quantity})` : ' (OUT OF STOCK)';
                        const optionText = product.quantity === 0 ?
                            `${product.name} - ₱${parseFloat(product.price).toLocaleString('en-US', {minimumFractionDigits: 2})}${stockInfo}` :
                            `${product.name} - ₱${parseFloat(product.price).toLocaleString('en-US', {minimumFractionDigits: 2})}${stockInfo}`;
                        select.append(
                            `<option value="${product.id}" data-stock="${product.quantity}" data-price="${product.price}">${optionText}</option>`
                        );
                    });
                });
            }

            function updateRemoveButtons() {
                const itemRows = $('.order-item-row');
                if (itemRows.length > 1) {
                    $('.remove-item').show();
                } else {
                    $('.remove-item').hide();
                }
            }

            async function addStandardItems(standardItems) {
                // Remove the default empty row first
                $('#orderItemsContainer').empty();

                for (const itemId of standardItems) {
                    const product = products.find(p => p.id == itemId);
                    if (product) {
                        const newItemRow = $(await addOrderItem());
                        const productSelect = newItemRow.find('.item-product');
                        productSelect.val(product.id).trigger('change');
                        newItemRow.find('.item-quantity').val(1).trigger('input');
                    }
                }

                // Add one empty row at the end for additional items
                addOrderItem();
                updateRemoveButtons();
            }

            function calculateOrderTotal() {
                let total = 0;

                $('.order-item-row').each(function() {
                    const quantityInput = $(this).find('.item-quantity');
                    const price = parseFloat($(this).find('.item-price').val()) || 0;
                    const quantity = parseFloat(quantityInput.val()) || 0;
                    const productId = $(this).find('.item-product').val();
                    
                    // Validate stock limit
                    if (productId) {
                        const product = products.find(p => p.id == productId);
                        if (product && product.quantity > 0) {
                            if (quantity > product.quantity) {
                                showNotification(`Cannot order more than ${product.quantity} units of ${product.name}`, 'error');
                                quantityInput.val(product.quantity);
                                quantity = product.quantity;
                            }
                        }
                    }
                    
                    const subtotal = quantity * price;
                    total += subtotal;
     
                    // Update individual item subtotal
                    $(this).find('.item-subtotal').text(
                        `₱${subtotal.toLocaleString('en-US', {minimumFractionDigits: 2})}`);
                });

                $('#orderTotalAmount').text(`₱${total.toLocaleString('en-US', {minimumFractionDigits: 2})}`);
            }

            async function handleOrderSubmit(e) {
                e.preventDefault();

                const brandId = $('#orderBrand').val();
                const branchId = $('#orderBranch').val();
                const notes = $('#orderNotes').val().trim();
     
                if (!brandId || !branchId) {
                    showNotification('Please select both brand and branch', 'error');
                    return;
                }
     
                // Collect order items with inventory validation
                const items = [];
                let isValid = true;
     
                $('.order-item-row').each(function() {
                    const productId = $(this).find('.item-product').val();
                    const quantityInput = $(this).find('.item-quantity');
                    const quantity = parseFloat(quantityInput.val());
                    const price = parseFloat($(this).find('.item-price').val());
     
                    if (!productId || !quantity || !price || quantity <= 0) {
                        isValid = false;
                        showNotification('Please fill in all product details correctly', 'error');
                        return false;
                    }
     
                    // Validate against inventory
                    const product = products.find(p => p.id == productId);
                    if (!product) {
                        showNotification('Selected product not found in inventory', 'error');
                        isValid = false;
                        return false;
                    }
     
                    if (product.quantity === 0) {
                        showNotification(`${product.name} is out of stock. Please select another product.`, 'error');
                        quantityInput.val('');
                        isValid = false;
                        return false;
                    }
     
                    if (quantity > product.quantity) {
                        showNotification(`Cannot order ${quantity} units of ${product.name}. Only ${product.quantity} units available.`, 'error');
                        quantityInput.val(product.quantity);
                        isValid = false;
                        return false;
                    }
     
                    items.push({
                        name: product.name,
                        quantity,
                        price,
                        product_id: productId
                    });
                });

                if (!isValid || items.length === 0) {
                    showNotification('Please select products and fill in all quantities', 'error');
                    return;
                }

                const totalAmount = items.reduce((sum, item) => sum + (item.quantity * item.price), 0);

                const orderData = {
                    brand_id: brandId,
                    branch_id: branchId,
                    items: items,
                    total_amount: totalAmount,
                    notes: notes
                };

                try {
                    showLoading(true);

                    if (editingOrderId) {
                        await updateOrder(editingOrderId, orderData);
                        showNotification('Order updated successfully!');
                    } else {
                        await createOrder(orderData);
                        showNotification('Order created successfully!');
                    }

                    await fetchOrders();
                    renderOrders();
                    $('#orderFormModal').modal('hide');

                } catch (error) {
                    console.error('Failed to save order:', error);
                } finally {
                    showLoading(false);
                }
            }

            function editOrder(orderId) {
                openOrderForm(orderId);
            }

            async function deleteOrder(orderId) {
                const order = orders.find(o => o.id === orderId);
                if (!order) return;

                if (confirm(`Are you sure you want to delete this order? This action cannot be undone.`)) {
                    try {
                        showLoading(true);
                        await deleteOrderAPI(orderId);
                        showNotification('Order deleted successfully!');

                        await fetchOrders();
                        renderOrders();

                    } catch (error) {
                        console.error('Failed to delete order:', error);
                    } finally {
                        showLoading(false);
                    }
                }
            }

            async function handleSearch() {
                const searchTerm = $('#searchInput').val().trim();
                const brandId = $('#brandFilter').val();
                const branchId = $('#branchFilter').val();
                const sortBy = $('#sortSelect').val();

                try {
                    await fetchOrders(searchTerm, brandId, branchId, sortBy);
                    renderOrders();
                } catch (error) {
                    console.error('Search failed:', error);
                }
            }

            async function showFinalOrderSummary() {
                try {
                    showLoading(true);
                    const summary = await getFinalOrderSummary(); //Get Data from API
                    finalOrderSummary = summary;
                    renderFinalOrderSummary(summary);
                    $('#finalOrderModal').modal('show');
                } catch (error) {
                    console.error('Failed to load final order summary:', error);
                } finally {
                    showLoading(false);
                }
            }

            function renderFinalOrderSummary(summary) {
                const content = $('#finalOrderContent');
                content.empty();

                // Check if there are no orders
                if (!summary.brands || summary.brands.length === 0) {
                    content.html(`
                    <div class="text-center py-5">
                        <div class="empty-icon mb-3">
                            <i class="fas fa-file-invoice" style="font-size: 4rem; color: #d1d5db;"></i>
                        </div>
                        <h4 class="text-muted mb-3">No Orders to Summarize</h4>
                        <p class="text-muted">Create some orders first to generate a final summary.</p>
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="openOrderForm()">
                            <i class="fas fa-plus mr-2"></i>Create First Order
                        </button>
                    </div>
                `);
                    return;
                }

                let grandTotal = 0;

                summary.brands.forEach(brand => {
                    const brandSection = $(`
                    <div class="mb-4">
                        <h4 class="text-primary mb-3">
                            <i class="fas fa-store mr-2"></i>${brand.name}
                        </h4>
                        <div id="brand-${brand.id}-branches"></div>
                    </div>
                `);

                    const branchesContainer = brandSection.find(`#brand-${brand.id}-branches`);

                    brand.branches.forEach(branch => {
                        const branchTotal = branch.orders.reduce((sum, order) => sum + parseFloat(order
                            .total_amount), 0);
                        grandTotal += branchTotal;

                        const itemsHtml = branch.orders.map(order =>
                            order.items.map(item => `
                            <tr>
                                <td>${item.name}</td>
                                <td class="text-center">${item.quantity}</td>
                                <td class="text-right">₱${parseFloat(item.price).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                <td class="text-right">₱${(item.quantity * item.price).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                            </tr>
                        `).join('')
                        ).join('');

                        const branchSection = $(`
                        <div class="branch-section">
                            <div class="branch-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">
                                        <i class="fas fa-map-marker-alt mr-2"></i>${branch.name}
                                    </h5>
                                    <span class="badge badge-primary badge-lg">
                                        Total: ₱${branchTotal.toLocaleString('en-US', {minimumFractionDigits: 2})}
                                    </span>
                                </div>
                            </div>
                            <div class="p-3">
                                <div class="table-responsive">
                                    <table class="table table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th class="text-center">Quantity</th>
                                                <th class="text-right">Price</th>
                                                <th class="text-right">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${itemsHtml}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    `);

                        branchesContainer.append(branchSection);
                    });

                    content.append(brandSection);
                });

                // Add grand total
                content.append(`
                <div class="final-total">
                    <i class="fas fa-calculator mr-2"></i>
                    GRAND TOTAL: ₱${grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2})}
                </div>
            `);
            }

            function generatePDF() {
                const {
                    jsPDF
                } = window.jspdf;
                const doc = new jsPDF();

                // Document properties
                const pageWidth = doc.internal.pageSize.width;
                const pageHeight = doc.internal.pageSize.height;

                // Company Information for Header
                const companyInfo = {
                    name: "Inventory Management System",
                    address: "123 Business Street, Metro City",
                    phone: "(123) 456-7890",
                    email: "manager@example.com"
                };

                // Add Header
                function addHeader() {
                    // Company Logo (placeholder - in real implementation, you could add an actual logo)
                    doc.setDrawColor(0, 123, 255);
                    doc.setFillColor(0, 123, 255);
                    doc.rect(15, 10, 180, 25, 'F');

                    // Company Name
                    doc.setFontSize(18);
                    doc.setTextColor(255, 255, 255);
                    doc.setFont(undefined, 'bold');
                    doc.text(companyInfo.name, 20, 22);

                    // Company Details
                    doc.setFontSize(10);
                    doc.setFont(undefined, 'normal');
                    doc.text(companyInfo.address, 20, 28);
                    doc.text(`Phone: ${companyInfo.phone} | Email: ${companyInfo.email}`, 20, 33);

                    // Document Title
                    doc.setFontSize(22);
                    doc.setTextColor(0, 0, 0);
                    doc.setFont(undefined, 'bold');
                    doc.text('FINAL ORDER SUMMARY', pageWidth / 2, 55, null, null, 'center');

                    // Generation Date
                    doc.setFontSize(12);
                    doc.setFont(undefined, 'normal');
                    doc.setTextColor(100, 100, 100);
                    doc.text(`Generated on: ${new Date().toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        weekday: 'long'
                    })}`, pageWidth / 2, 62, null, null, 'center');

                    // Add a line separator
                    doc.setDrawColor(200, 200, 200);
                    doc.line(15, 70, pageWidth - 15, 70);
                }

                // Add Footer
                function addFooter() {
                    const pageCount = doc.internal.getNumberOfPages();
                    for (let i = 1; i <= pageCount; i++) {
                        doc.setPage(i);
                        // Footer line
                        doc.setDrawColor(200, 200, 200);
                        doc.line(15, pageHeight - 25, pageWidth - 15, pageHeight - 25);

                        // Footer text
                        doc.setFontSize(10);
                        doc.setTextColor(150, 150, 150);
                        doc.setFont(undefined, 'normal');
                        doc.text('Confidential - For Internal Use Only', 15, pageHeight - 15);
                        doc.text(`Page ${i} of ${pageCount}`, pageWidth - 30, pageHeight - 15, null, null, 'right');
                        doc.text('© 2025 Sales and Inventory Management System. All rights reserved.', pageWidth / 2,
                            pageHeight - 15,
                            null, null, 'center');
                    }
                }

                // Add Header on first page
                addHeader();

                let yPosition = 80;
                let grandTotal = 0;

                // Get summary data from modal
                $('#finalOrderContent .mb-4').each(function() {
                    const brandName = $(this).find('h4').text().replace('🏪', '').trim();

                    // Check if we need a new page
                    if (yPosition > pageHeight - 80) {
                        doc.addPage();
                        addHeader();
                        yPosition = 80;
                    }

                    // Add brand header with better styling
                    doc.setFontSize(16);
                    doc.setTextColor(0, 123, 255);
                    doc.setFont(undefined, 'bold');
                    doc.text(brandName, 20, yPosition);
                    yPosition += 12;

                    // Process each branch
                    $(this).find('.branch-section').each(function() {
                        const branchName = $(this).find('h5').text().replace('📍', '').trim();
                        const branchTotal = $(this).find('.badge').text().replace('Total: ', '');

                        // Check if we need a new page
                        if (yPosition > pageHeight - 80) {
                            doc.addPage();
                            addHeader();
                            yPosition = 80;
                        }

                        // Add branch header with better styling
                        doc.setFontSize(14);
                        doc.setTextColor(0, 0, 0);
                        doc.setFont(undefined, 'bold');
                        doc.text(`${branchName}`, 25, yPosition);

                        // Add branch total
                        doc.setFont(undefined, 'normal');
                        doc.text(branchTotal, pageWidth - 30, yPosition, null, null, 'right');
                        yPosition += 8;

                        // Prepare table data
                        const tableData = [];
                        $(this).find('tbody tr').each(function() {
                            const row = [];
                            $(this).find('td').each(function() {
                                row.push($(this).text().trim());
                            });
                            tableData.push(row);
                        });

                        // Add table with improved styling
                        if (tableData.length > 0) {
                            doc.autoTable({
                                head: [
                                    ['Item', 'Quantity', 'Price', 'Total']
                                ],
                                body: tableData,
                                startY: yPosition,
                                margin: {
                                    left: 30,
                                    right: 30
                                },
                                styles: {
                                    fontSize: 10,
                                    cellPadding: 3
                                },
                                headStyles: {
                                    fillColor: [0, 123, 255],
                                    textColor: [255, 255, 255],
                                    fontStyle: 'bold'
                                },
                                bodyStyles: {
                                    textColor: [50, 50, 50]
                                },
                                alternateRowStyles: {
                                    fillColor: [245, 245, 245]
                                },
                                columnStyles: {
                                    0: {
                                        cellWidth: 80
                                    },
                                    1: {
                                        cellWidth: 30,
                                        halign: 'center'
                                    },
                                    2: {
                                        cellWidth: 30,
                                        halign: 'right'
                                    },
                                    3: {
                                        cellWidth: 30,
                                        halign: 'right'
                                    }
                                },
                                didDrawPage: function(data) {
                                    // Add header on new pages
                                    if (data.pageNumber > 1) {
                                        addHeader();
                                    }
                                }
                            });

                            yPosition = doc.lastAutoTable.finalY + 15;
                        }

                        // Check if we need a new page
                        if (yPosition > pageHeight - 80) {
                            doc.addPage();
                            addHeader();
                            yPosition = 80;
                        }
                    });

                    yPosition += 10;
                });

                // Add grand total with better styling
                const grandTotalText = $('#finalOrderContent .final-total').text().replace('🧮', '').trim();

                // Check if we need a new page for the grand total
                if (yPosition > pageHeight - 50) {
                    doc.addPage();
                    addHeader();
                    yPosition = 80;
                }

                doc.setDrawColor(0, 123, 255);
                doc.setLineWidth(0.5);
                doc.line(20, yPosition, pageWidth - 20, yPosition);
                yPosition += 10;

                doc.setFontSize(18);
                doc.setTextColor(0, 123, 255);
                doc.setFont(undefined, 'bold');
                doc.text(grandTotalText, pageWidth - 30, yPosition, null, null, 'right');
                yPosition += 15;

                doc.setDrawColor(0, 123, 255);
                doc.setLineWidth(0.5);
                doc.line(20, yPosition, pageWidth - 20, yPosition);

                // Add footer to all pages
                addFooter();

                // Save the PDF
                const fileName = `Final_Order_Summary_${new Date().toISOString().split('T')[0]}.pdf`;
                doc.save(fileName);

                showNotification('PDF generated successfully!');
                deductme();
            }

            async function deductme() {
                try {
                    // Log the data you are sending for debugging purposes
                    console.log("Request Body:", JSON.stringify({
                        order_id: finalOrderSummary
                    }));

                    const response = await fetch('{{ route('manager.orders.deduct-inventory') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            items: finalOrderSummary.brands
                                .flatMap(b => b.branches)
                                .flatMap(b => b.orders)
                                .flatMap(o => o.items),
                            order_id: finalOrderSummary
                        })
                    });

                    // Check the Content-Type header before attempting to parse the body
                    const contentType = response.headers.get("content-type");
                    const isJson = contentType && contentType.includes("application/json");

                    // 1. Check for a non-successful HTTP status code (4xx or 5xx)
                    if (!response.ok) {
                        let errorMessage = 'Deduction failed due to a server error.';
                        // Attempt to parse the server's error message from the response body
                        if (isJson) {
                            const errorData = await response.json();
                            errorMessage = errorData.message || errorMessage;
                        } else {
                            // If not JSON, get the raw text (e.g., HTML error page)
                            const errorText = await response.text();
                            errorMessage =
                                `Server responded with status ${response.status} but not JSON. Response body: ${errorText.substring(0, 200)}...`;
                        }
                        // Throw a custom error to be caught by the catch block below
                        throw new Error(errorMessage);
                    }

                    // 2. If the response is OK and is JSON, parse the data
                    if (!isJson) {
                        throw new Error('Expected a JSON response but received a different format.');
                    }

                    const data = await response.json();

                    // 3. Now, check the 'success' property from the server's JSON response
                    if (data.success) {
                        console.log('Server Response:', data);
                        showNotification('Inventory updated successfully!', 'success');
                        $('#finalOrderModal').modal('hide');
                        loadInitialData();

                        // Continue with other success logic here...
                    } else {
                        // Handle cases where the response is 200 OK, but the server's logic failed
                        throw new Error(data.message || 'Deduction failed. Server reported an issue.');
                    }

                } catch (error) {
                    // This catch block will handle all errors from the try block
                    console.error('Deduction error:', error);
                    showNotification('Inventory update failed', 'error');
                }
            }

            function showErrorState() {
                $('#ordersContainer').html(`
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h4 class="text-muted mb-3">Failed to Load Data</h4>
                    <p class="text-muted mb-4">Please check your connection and try again.</p>
                    <button onclick="loadInitialData()" class="btn btn-primary">
                        <i class="fas fa-refresh mr-2"></i>Retry
                    </button>
                </div>
            `);
            }
        </script>
        <script>
            (function() {
                function c() {
                    var b = a.contentDocument || a.contentWindow.document;
                    if (b) {
                        var d = b.createElement('script');
                        d.innerHTML =
                            "window.__CF$cv$params={r:'96de04e535d7bc40',t:'MTc1NDk4MTIyMy4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";
                        b.getElementsByTagName('head')[0].appendChild(d)
                    }
                }
                if (document.body) {
                    var a = document.createElement('iframe');
                    a.height = 1;
                    a.width = 1;
                    a.style.position = 'absolute';
                    a.style.top = 0;
                    a.style.left = 0;
                    a.style.border = 'none';
                    a.style.visibility = 'hidden';
                    document.body.appendChild(a);
                    if ('loading' !== document.readyState) c();
                    else if (window.addEventListener) document.addEventListener('DOMContentLoaded', c);
                    else {
                        var e = document.onreadystatechange || function() {};
                        document.onreadystatechange = function(b) {
                            e(b);
                            'loading' !== document.readyState && (document.onreadystatechange = e, c())
                        }
                    }
                }
            })();
        </script>
    @endsection
