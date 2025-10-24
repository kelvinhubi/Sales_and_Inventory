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

            /* Inventory Badge Styles */
            .badge-lg {
                font-size: 0.9rem;
                padding: 0.4rem 0.6rem;
            }

            .table th {
                font-weight: 600;
                background-color: #f8f9fa;
            }

            .table td .badge {
                min-width: 50px;
                display: inline-block;
            }

            /* Dashboard Summary Styles */
            .dashboard-brand-section {
                margin-bottom: 15px;
                border-left: 3px solid #28a745;
                padding-left: 10px;
            }

            .dashboard-brand-title {
                font-size: 1rem;
                font-weight: bold;
                color: #28a745;
                margin-bottom: 8px;
            }

            .dashboard-branch-item {
                background: #f8f9fa;
                padding: 8px 10px;
                margin-bottom: 5px;
                border-radius: 4px;
                font-size: 0.85rem;
            }

            .dashboard-branch-name {
                font-weight: 600;
                color: #495057;
            }

            .dashboard-branch-total {
                color: #28a745;
                font-weight: bold;
                float: right;
            }

            .dashboard-grand-total {
                background: #28a745;
                color: white;
                padding: 12px;
                text-align: center;
                font-size: 1.1rem;
                font-weight: bold;
                border-radius: 6px;
                margin-top: 15px;
            }

            .summary-item-count {
                font-size: 0.75rem;
                color: #6c757d;
                display: block;
                margin-top: 2px;
            }

            #dashboardSummaryContent::-webkit-scrollbar {
                width: 6px;
            }

            #dashboardSummaryContent::-webkit-scrollbar-track {
                background: #f1f1f1;
            }

            #dashboardSummaryContent::-webkit-scrollbar-thumb {
                background: #28a745;
                border-radius: 3px;
            }

            #dashboardSummaryContent::-webkit-scrollbar-thumb:hover {
                background: #218838;
            }

            /* Kiosk Style Order Modal */
            .kiosk-modal .modal-dialog {
                max-width: 95%;
                height: 90vh;
            }

            .kiosk-modal .modal-content {
                height: 100%;
                display: flex;
                flex-direction: column;
            }

            .kiosk-modal .modal-header {
                flex-shrink: 0;
                z-index: 10;
            }

            .kiosk-modal .modal-body {
                flex: 1;
                overflow-y: auto;
                overflow-x: hidden;
                padding: 20px;
                background: white;
            }

            .kiosk-modal .modal-footer {
                position: relative;
                flex-shrink: 0;
                background: white;
                border-top: 2px solid #dee2e6;
                z-index: 10;
            }

            .kiosk-products-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 15px;
                margin-bottom: 20px;
                min-height: 200px;
            }

            .kiosk-product-card {
                background: white;
                border: 2px solid #e0e0e0;
                border-radius: 10px;
                padding: 15px;
                cursor: pointer;
                transition: all 0.3s ease;
                text-align: center;
                position: relative;
                height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }

            .kiosk-product-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
                border-color: #007bff;
            }

            .kiosk-product-card.selected {
                border-color: #28a745;
                background: #f0fff4;
                box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
            }

            .kiosk-product-card.out-of-stock {
                opacity: 0.5;
                cursor: not-allowed;
                background: #f8f9fa;
            }

            .kiosk-product-card.out-of-stock:hover {
                transform: none;
                box-shadow: none;
            }

            .kiosk-product-image {
                width: 80px;
                height: 80px;
                margin: 0 auto 10px;
                background: #f8f9fa;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 2.5rem;
                color: #6c757d;
            }

            .kiosk-product-name {
                font-weight: 600;
                font-size: 0.95rem;
                margin-bottom: 8px;
                color: #333;
                min-height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .kiosk-product-price {
                font-size: 1.1rem;
                color: #28a745;
                font-weight: bold;
                margin-bottom: 10px;
            }

            .kiosk-product-stock {
                font-size: 0.8rem;
                color: #6c757d;
                margin-bottom: 10px;
            }

            .kiosk-product-stock.low-stock {
                color: #ffc107;
                font-weight: 600;
            }

            .kiosk-quantity-controls {
                display: none;
                margin-top: 10px;
            }

            .kiosk-product-card.selected .kiosk-quantity-controls {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
            }

            .kiosk-qty-btn {
                width: 35px;
                height: 35px;
                border-radius: 50%;
                border: 2px solid #28a745;
                background: white;
                color: #28a745;
                font-size: 1.2rem;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.2s;
            }

            .kiosk-qty-btn:hover {
                background: #28a745;
                color: white;
            }

            .kiosk-qty-input {
                width: 60px;
                text-align: center;
                font-size: 1.1rem;
                font-weight: bold;
                border: 2px solid #28a745;
                border-radius: 5px;
                padding: 5px;
            }

            .kiosk-selected-badge {
                position: absolute;
                top: 10px;
                right: 10px;
                background: #28a745;
                color: white;
                width: 30px;
                height: 30px;
                border-radius: 50%;
                display: none;
                align-items: center;
                justify-content: center;
                font-size: 1rem;
            }

            .kiosk-product-card.selected .kiosk-selected-badge {
                display: flex;
            }

            .kiosk-cart-summary {
                background: white;
                border: 2px solid #28a745;
                border-radius: 10px;
                padding: 20px;
                margin-top: 20px;
                margin-bottom: 20px;
            }

            .kiosk-notes-section {
                background: #f8f9fa;
                padding: 20px;
                border-radius: 10px;
                margin-top: 20px;
                margin-bottom: 0;
            }

            .kiosk-notes-section textarea {
                resize: vertical;
                min-height: 80px;
                background: white;
            }

            .kiosk-cart-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px;
                border-bottom: 1px solid #e0e0e0;
                margin-bottom: 8px;
            }

            .kiosk-cart-item:last-child {
                border-bottom: none;
                margin-bottom: 0;
            }

            .kiosk-cart-item-name {
                font-weight: 600;
                flex: 1;
            }

            .kiosk-cart-item-qty {
                color: #6c757d;
                margin: 0 15px;
            }

            .kiosk-cart-item-subtotal {
                font-weight: bold;
                color: #28a745;
            }

            .kiosk-cart-total {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding-top: 15px;
                border-top: 2px solid #28a745;
                font-size: 1.3rem;
                font-weight: bold;
            }

            .kiosk-search-box {
                margin-bottom: 20px;
            }

            .kiosk-search-input {
                width: 100%;
                padding: 12px 20px;
                font-size: 1.1rem;
                border: 2px solid #e0e0e0;
                border-radius: 10px;
                transition: all 0.3s;
            }

            #manualAddBtn {
                font-weight: 600;
                box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);
            }

            #manualAddBtn:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(255, 193, 7, 0.4);
            }

            .kiosk-search-input:focus {
                border-color: #007bff;
                outline: none;
                box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
            }

            .out-of-stock-badge {
                position: absolute;
                top: 10px;
                left: 10px;
                background: #dc3545;
                color: white;
                padding: 3px 8px;
                border-radius: 4px;
                font-size: 0.75rem;
                font-weight: bold;
            }

            .standard-item-badge {
                position: absolute;
                top: 10px;
                left: 10px;
                background: #007bff;
                color: white;
                padding: 3px 8px;
                border-radius: 4px;
                font-size: 0.75rem;
                font-weight: bold;
                z-index: 1;
            }

            .kiosk-product-card.standard-item {
                border-color: #007bff;
            }

            .kiosk-product-card.standard-item:not(.selected):hover {
                border-color: #0056b3;
                box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
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

                        <div class="row">
                            <!-- Left Column: Orders List -->
                            <div class="col-lg-8">
                                <div class="search-section">
                                    <div class="row">
                                        <div class="col-md-5">
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
                                        <div class="col-md-2">
                                            <select class="form-control" id="branchFilter">
                                                <option value="">All Branches</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <select class="form-control" id="sortSelect">
                                                <option value="date">By Date</option>
                                                <option value="total">By Total</option>
                                                <option value="brand">By Brand</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="orders-grid" id="ordersContainer">
                                </div>
                            </div>

                            <!-- Right Column: Order Summary Dashboard -->
                            <div class="col-lg-4">
                                <div class="card card-success sticky-top" style="top: 20px;">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-file-invoice mr-2"></i>
                                            Final Order Summary
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" id="refreshSummaryBtn">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body" id="dashboardSummaryContent" style="max-height: 600px; overflow-y: auto;">
                                        <div class="text-center py-5">
                                            <i class="fas fa-shopping-cart" style="font-size: 3rem; color: #d1d5db;"></i>
                                            <p class="text-muted mt-3">Loading summary...</p>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="button" class="btn btn-success btn-block" id="processOrderBtn">
                                            <i class="fas fa-check-circle mr-2"></i>Order to Process
                                        </button>
                                        <button type="button" class="btn btn-info btn-block btn-sm mt-2" id="viewFullSummaryBtn">
                                            <i class="fas fa-expand mr-2"></i>View Full Summary
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div class="modal fade kiosk-modal" id="orderFormModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h4 class="modal-title text-white" id="orderFormTitle">
                            <i class="fas fa-shopping-cart mr-2"></i>Add New Order
                        </h4>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form id="orderForm">
                        <div class="modal-body">
                            <!-- Brand and Branch Selection -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="orderBrand" class="font-weight-bold">
                                            <i class="fas fa-store mr-1"></i>Brand <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control form-control-lg" id="orderBrand" required>
                                            <option value="">Select Brand</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="orderBranch" class="font-weight-bold">
                                            <i class="fas fa-map-marker-alt mr-1"></i>Branch <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control form-control-lg" id="orderBranch" required>
                                            <option value="">Select Branch</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Search Box -->
                            <div class="kiosk-search-box">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <input type="text" class="kiosk-search-input" id="kioskSearchInput" 
                                               placeholder="🔍 Search products...">
                                    </div>
                                    <div class="col-md-3">
                                        <button type="button" class="btn btn-info btn-lg btn-block" id="toggleProductsBtn" onclick="toggleShowAllProducts()">
                                            <i class="fas fa-filter mr-2"></i><span id="toggleBtnText">Show All</span>
                                        </button>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="button" class="btn btn-warning btn-lg btn-block" id="manualAddBtn">
                                            <i class="fas fa-plus-circle mr-2"></i>Manual Add
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Products Grid -->
                            <div id="kioskProductsGrid" class="kiosk-products-grid">
                                <!-- Products will be dynamically loaded here -->
                            </div>

                            <!-- Cart Summary -->
                            <div class="kiosk-cart-summary" id="kioskCartSummary" style="display: none;">
                                <h5 class="mb-3">
                                    <i class="fas fa-shopping-basket mr-2"></i>Selected Items
                                </h5>
                                <div id="kioskCartItems">
                                    <!-- Cart items will be displayed here -->
                                </div>
                                <div class="kiosk-cart-total">
                                    <span>Total:</span>
                                    <span id="kioskTotalAmount">₱0.00</span>
                                </div>
                            </div>

                            <!-- Notes Section - Fixed at bottom of content -->
                            <div class="kiosk-notes-section">
                                <label for="orderNotes" class="font-weight-bold">
                                    <i class="fas fa-sticky-note mr-1"></i>Notes
                                </label>
                                <textarea class="form-control" id="orderNotes" rows="2" 
                                          placeholder="Additional notes (optional)..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save mr-2"></i>Save Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Manual Add Product Modal -->
        <div class="modal fade" id="manualAddModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h4 class="modal-title text-white">
                            <i class="fas fa-plus-circle mr-2"></i>Manually Add Product
                        </h4>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form id="manualAddForm">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="manualProductSelect" class="font-weight-bold">
                                    <i class="fas fa-box mr-1"></i>Select Product <span class="text-danger">*</span>
                                </label>
                                <select class="form-control form-control-lg" id="manualProductSelect" required>
                                    <option value="">Choose a product...</option>
                                </select>
                                <small class="form-text text-muted">Select a product that's not in the standard items</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="manualQuantity" class="font-weight-bold">
                                            <i class="fas fa-sort-numeric-up mr-1"></i>Quantity <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control form-control-lg" id="manualQuantity" 
                                               min="1" value="1" required>
                                        <small class="form-text text-muted" id="manualStockInfo"></small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="manualPrice" class="font-weight-bold">
                                            <i class="fas fa-tag mr-1"></i>Price
                                        </label>
                                        <input type="text" class="form-control form-control-lg" id="manualPrice" 
                                               readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Tip:</strong> This allows you to add products that aren't in the standard items list.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-plus mr-2"></i>Add to Cart
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
            let kioskCart = {}; // Store selected products: { productId: { product, quantity } }
            let standardItemIds = []; // Store IDs of standard items for current brand
            let showAllProducts = false; // Toggle to show all products or only standard items

            // 🛒 Kiosk-Style Functions
            function renderKioskProducts() {
                const grid = $('#kioskProductsGrid');
                grid.empty();

                if (products.length === 0) {
                    grid.html(`
                        <div class="col-12 text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No products available. Please select a brand first.</p>
                        </div>
                    `);
                    return;
                }

                // Filter products based on showAllProducts flag
                let displayProducts = products;
                if (!showAllProducts && standardItemIds.length > 0) {
                    // Show only standard items
                    displayProducts = products.filter(p => standardItemIds.includes(p.id));
                }

                if (displayProducts.length === 0 && standardItemIds.length > 0) {
                    grid.html(`
                        <div class="col-12 text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No standard items available.</p>
                            <button type="button" class="btn btn-warning mt-3" onclick="toggleShowAllProducts()">
                                <i class="fas fa-eye mr-2"></i>Show All Products
                            </button>
                        </div>
                    `);
                    return;
                }

                displayProducts.forEach(product => {
                    const isOutOfStock = product.quantity <= 0;
                    const isLowStock = product.quantity > 0 && product.quantity <= 10;
                    const isSelected = kioskCart[product.id];
                    const quantity = isSelected ? kioskCart[product.id].quantity : 0;
                    const isStandardItem = standardItemIds.includes(product.id);

                    const productCard = $(`
                        <div class="kiosk-product-card ${isSelected ? 'selected' : ''} ${isOutOfStock ? 'out-of-stock' : ''} ${isStandardItem ? 'standard-item' : ''}" 
                             data-product-id="${product.id}"
                             data-product-name="${product.name.toLowerCase()}">
                            ${isOutOfStock ? '<div class="out-of-stock-badge">OUT OF STOCK</div>' : ''}
                            ${isStandardItem ? '<div class="standard-item-badge">STANDARD</div>' : ''}
                            <div class="kiosk-selected-badge">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="kiosk-product-image">
                                <i class="fas fa-box"></i>
                            </div>
                            <div class="kiosk-product-name">${product.name}</div>
                            <div class="kiosk-product-price">₱${parseFloat(product.price).toLocaleString('en-US', {minimumFractionDigits: 2})}</div>
                            <div class="kiosk-product-stock ${isLowStock ? 'low-stock' : ''}">
                                ${isOutOfStock ? 'Out of Stock' : `Stock: ${product.quantity}`}
                            </div>
                            <div class="kiosk-quantity-controls">
                                <button type="button" class="kiosk-qty-btn kiosk-qty-minus" data-product-id="${product.id}">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" class="kiosk-qty-input" value="${quantity}" min="1" max="${product.quantity}" 
                                       data-product-id="${product.id}">
                                <button type="button" class="kiosk-qty-btn kiosk-qty-plus" data-product-id="${product.id}">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    `);

                    if (!isOutOfStock) {
                        productCard.click(function(e) {
                            if (!$(e.target).closest('.kiosk-quantity-controls').length && 
                                !$(e.target).hasClass('kiosk-qty-btn') && 
                                !$(e.target).closest('.kiosk-qty-btn').length) {
                                toggleKioskProduct(product.id);
                            }
                        });
                    }

                    grid.append(productCard);
                });

                // Attach quantity control events (after all cards are appended)
                $('.kiosk-qty-plus').off('click').on('click', function(e) {
                    e.stopPropagation();
                    const productId = $(this).data('product-id');
                    updateKioskQuantity(productId, 1);
                });

                $('.kiosk-qty-minus').off('click').on('click', function(e) {
                    e.stopPropagation();
                    const productId = $(this).data('product-id');
                    updateKioskQuantity(productId, -1);
                });

                $('.kiosk-qty-input').off('input blur').on('input blur', function(e) {
                    const productId = $(this).data('product-id');
                    let newQty = parseInt($(this).val());
                    
                    // Validate input
                    if (isNaN(newQty) || newQty < 1) {
                        newQty = 1;
                        $(this).val(1);
                    }
                    
                    setKioskQuantity(productId, newQty);
                });
            }

            function toggleKioskProduct(productId) {
                const product = products.find(p => p.id == productId);
                if (!product || product.quantity <= 0) return;

                if (kioskCart[productId]) {
                    // Remove from cart
                    delete kioskCart[productId];
                } else {
                    // Add to cart with quantity 1
                    kioskCart[productId] = {
                        product: product,
                        quantity: 1
                    };
                }

                renderKioskProducts();
                updateKioskCart();
            }

            function updateKioskQuantity(productId, delta) {
                if (!kioskCart[productId]) return;

                const product = products.find(p => p.id == productId);
                const newQuantity = kioskCart[productId].quantity + delta;

                if (newQuantity <= 0) {
                    delete kioskCart[productId];
                    renderKioskProducts();
                } else if (newQuantity <= product.quantity) {
                    kioskCart[productId].quantity = newQuantity;
                    // Update just the quantity display without re-rendering
                    $(`.kiosk-qty-input[data-product-id="${productId}"]`).val(newQuantity);
                } else {
                    showNotification(`Maximum available quantity is ${product.quantity}`, 'warning');
                    return;
                }

                updateKioskCart();
            }

            function setKioskQuantity(productId, quantity) {
                if (!kioskCart[productId]) return;

                const product = products.find(p => p.id == productId);
                
                if (quantity <= 0) {
                    delete kioskCart[productId];
                    renderKioskProducts();
                } else if (quantity <= product.quantity) {
                    kioskCart[productId].quantity = quantity;
                    // Update just the quantity display without re-rendering
                    $(`.kiosk-qty-input[data-product-id="${productId}"]`).val(quantity);
                } else {
                    showNotification(`Maximum available quantity is ${product.quantity}`, 'warning');
                    kioskCart[productId].quantity = product.quantity;
                    $(`.kiosk-qty-input[data-product-id="${productId}"]`).val(product.quantity);
                }

                updateKioskCart();
            }

            function updateKioskCart() {
                const cartItems = $('#kioskCartItems');
                const cartSummary = $('#kioskCartSummary');
                
                cartItems.empty();
                
                if (Object.keys(kioskCart).length === 0) {
                    cartSummary.hide();
                    $('#kioskTotalAmount').text('₱0.00');
                    return;
                }

                cartSummary.show();
                let total = 0;

                Object.values(kioskCart).forEach(item => {
                    const subtotal = item.quantity * parseFloat(item.product.price);
                    total += subtotal;

                    cartItems.append(`
                        <div class="kiosk-cart-item">
                            <span class="kiosk-cart-item-name">${item.product.name}</span>
                            <span class="kiosk-cart-item-qty">x${item.quantity}</span>
                            <span class="kiosk-cart-item-subtotal">₱${subtotal.toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                        </div>
                    `);
                });

                $('#kioskTotalAmount').text(`₱${total.toLocaleString('en-US', {minimumFractionDigits: 2})}`);
            }

            function filterKioskProducts(searchTerm) {
                const term = searchTerm.toLowerCase();
                $('.kiosk-product-card').each(function() {
                    const productName = $(this).data('product-name');
                    if (productName.includes(term)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }

            function resetKioskCart() {
                kioskCart = {};
                standardItemIds = [];
                showAllProducts = false;
                updateKioskCart();
                renderKioskProducts();
            }

            function toggleShowAllProducts() {
                showAllProducts = !showAllProducts;
                
                // Update button text
                const btnText = $('#toggleBtnText');
                if (showAllProducts) {
                    btnText.text('Standard Only');
                    showNotification('Showing all products', 'info');
                } else {
                    btnText.text('Show All');
                    showNotification('Showing standard items only', 'info');
                }
                
                renderKioskProducts();
            }

            // 🔧 Manual Add Functions
            function openManualAddModal() {
                const brandId = $('#orderBrand').val();
                
                if (!brandId) {
                    showNotification('Please select a brand first', 'warning');
                    return;
                }

                // Populate product dropdown with all products
                const productSelect = $('#manualProductSelect');
                productSelect.find('option:not(:first)').remove();
                
                products.forEach(product => {
                    const inStock = product.quantity > 0;
                    const stockText = inStock ? ` (Stock: ${product.quantity})` : ' (Out of Stock)';
                    productSelect.append(
                        `<option value="${product.id}" ${!inStock ? 'disabled' : ''}>
                            ${product.name} - ₱${parseFloat(product.price).toLocaleString('en-US', {minimumFractionDigits: 2})}${stockText}
                        </option>`
                    );
                });

                // Reset form
                $('#manualAddForm')[0].reset();
                $('#manualPrice').val('');
                $('#manualStockInfo').text('');
                
                $('#manualAddModal').modal('show');
            }

            function handleManualProductChange() {
                const productId = $('#manualProductSelect').val();
                const quantityInput = $('#manualQuantity');
                const priceInput = $('#manualPrice');
                const stockInfo = $('#manualStockInfo');

                if (productId) {
                    const product = products.find(p => p.id == productId);
                    if (product) {
                        priceInput.val(`₱${parseFloat(product.price).toLocaleString('en-US', {minimumFractionDigits: 2})}`);
                        quantityInput.attr('max', product.quantity);
                        stockInfo.text(`Available stock: ${product.quantity} units`);
                        stockInfo.removeClass('text-danger').addClass('text-success');
                        
                        if (product.quantity <= 10) {
                            stockInfo.removeClass('text-success').addClass('text-warning');
                        }
                    }
                } else {
                    priceInput.val('');
                    quantityInput.removeAttr('max');
                    stockInfo.text('');
                }
            }

            function handleManualAdd(e) {
                e.preventDefault();

                const productId = $('#manualProductSelect').val();
                const quantity = parseInt($('#manualQuantity').val());

                if (!productId || !quantity || quantity <= 0) {
                    showNotification('Please select a product and enter a valid quantity', 'error');
                    return;
                }

                const product = products.find(p => p.id == productId);
                
                if (!product) {
                    showNotification('Product not found', 'error');
                    return;
                }

                if (quantity > product.quantity) {
                    showNotification(`Only ${product.quantity} units available`, 'error');
                    return;
                }

                // Add to kiosk cart
                if (kioskCart[productId]) {
                    // Update existing quantity
                    const newQuantity = kioskCart[productId].quantity + quantity;
                    if (newQuantity > product.quantity) {
                        showNotification(`Cannot add ${quantity} more. Only ${product.quantity - kioskCart[productId].quantity} units available`, 'error');
                        return;
                    }
                    kioskCart[productId].quantity = newQuantity;
                } else {
                    // Add new item
                    kioskCart[productId] = {
                        product: product,
                        quantity: quantity
                    };
                }

                renderKioskProducts();
                updateKioskCart();
                $('#manualAddModal').modal('hide');
                showNotification(`${product.name} added to cart!`, 'success');
            }
            // 🌐 API Helper Functions
            async function apiRequest(endpoint, options = {}) {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                    const fetchOptions = {
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
                    };

                    // Use POST with _method for PUT/DELETE to ensure compatibility with InfinityFree
                    if (options.method === 'PUT' || options.method === 'DELETE') {
                        const body = options.body ? JSON.parse(options.body) : {};
                        body._method = options.method;
                        fetchOptions.body = JSON.stringify(body);
                        fetchOptions.method = 'POST';
                    }

                    // Add timeout for InfinityFree (30 seconds)
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 30000);
                    fetchOptions.signal = controller.signal;

                    try {
                        const response = await fetch(`${API_BASE_URL}${endpoint}`, fetchOptions);
                        clearTimeout(timeoutId);

                        if (!response.ok) {
                            const errorData = await response.json().catch(() => ({}));
                            let errorMessage = errorData.message || `HTTP ${response.status}: ${response.statusText}`;
                            if (response.status === 422 && errorData.errors) {
                                const errorMessages = Object.values(errorData.errors).flat();
                                errorMessage = errorMessages.join(' ');
                            }
                            throw new Error(errorMessage);
                        }

                        const responseText = await response.text();
                        const data = responseText ? JSON.parse(responseText) : {};
                        return data.data || data;

                    } catch (fetchError) {
                        clearTimeout(timeoutId);
                        if (fetchError.name === 'AbortError') {
                            throw new Error('Request timeout - please try again');
                        }
                        throw fetchError;
                    }

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
                    await loadDashboardSummary(); // Load dashboard summary automatically
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

                // Dashboard buttons
                $('#processOrderBtn').click(handleProcessOrder);
                $('#refreshSummaryBtn').click(loadDashboardSummary);
                $('#viewFullSummaryBtn').click(showFinalOrderSummary);

                // Kiosk search
                $('#kioskSearchInput').on('input', function() {
                    filterKioskProducts($(this).val());
                });

                // Manual Add
                $('#manualAddBtn').click(openManualAddModal);
                $('#manualAddForm').submit(handleManualAdd);
                $('#manualProductSelect').change(handleManualProductChange);

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
                
                // Reset kiosk cart when brand changes
                resetKioskCart();

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

                        // Load standard items into kiosk cart
                        if (brandData.standard_items && brandData.standard_items.length > 0) {
                            await loadStandardItemsToKiosk(brandData.standard_items);
                        }

                        // Render kiosk products
                        renderKioskProducts();
                    } catch (error) {
                        console.error('Failed to load brand data:', error);
                        showNotification('Failed to load brand data', 'error');
                    }
                } else {
                    // Show empty state
                    renderKioskProducts();
                }
            }

            async function loadStandardItemsToKiosk(standardItems) {
                // Reset and store standard item IDs
                standardItemIds = [];
                
                // standardItems is an array of product IDs like [1, 3, 5]
                if (!Array.isArray(standardItems)) {
                    console.warn('Standard items is not an array:', standardItems);
                    return;
                }
                
                standardItems.forEach(productId => {
                    const product = products.find(p => p.id == productId);
                    if (product) {
                        standardItemIds.push(product.id);
                        
                        // Only add to cart if in stock
                        if (product.quantity > 0) {
                            kioskCart[product.id] = {
                                product: product,
                                quantity: 1 // Default quantity is 1
                            };
                        }
                    }
                });
                
                updateKioskCart();
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
                        
                        // Set max quantity based on stock (but don't show it)
                        if (product.quantity > 0) {
                            quantityInput.attr('max', product.quantity);
                            quantityInput.attr('placeholder', 'Qty');
                            quantityInput.removeAttr('disabled');
                            quantityInput.removeClass('bg-light border-warning');
                        } else {
                            // Disable quantity input if out of stock
                            quantityInput.attr('disabled', true);
                            quantityInput.attr('max', 0);
                            quantityInput.val('');
                            quantityInput.addClass('bg-light');
                            quantityInput.attr('placeholder', 'Out of Stock');
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
                    $('#orderFormTitle').html('<i class="fas fa-edit mr-2"></i>Edit Order');
                    populateOrderForm(order);
                } else {
                    $('#orderFormTitle').html('<i class="fas fa-shopping-cart mr-2"></i>Add New Order');
                    resetOrderForm();
                }

                $('#orderFormModal').modal('show');
            }

            function populateOrderForm(order) {
                $('#orderBrand').val(order.brand_id).trigger('change');

                setTimeout(() => {
                    $('#orderBranch').val(order.branch_id);
                    
                    // Populate kiosk cart from order items
                    kioskCart = {};
                    order.items.forEach(item => {
                        const product = products.find(p => p.name === item.name);
                        if (product) {
                            kioskCart[product.id] = {
                                product: product,
                                quantity: parseInt(item.quantity)
                            };
                        }
                    });
                    
                    renderKioskProducts();
                    updateKioskCart();
                }, 500);

                $('#orderNotes').val(order.notes);
            }

            function resetOrderForm() {
                $('#orderForm')[0].reset();
                $('#orderBranch').find('option:not(:first)').remove();
                resetKioskCart();
            }

            function addOrderItem() {
                const newItem = $(`
                <div class="order-item-row">
                    <div class="row mb-2 align-items-center">
                        <div class="col-md-auto px-2">
                            <input type="checkbox" class="item-checkbox" style="width: 18px; height: 18px;">
                        </div>
                        <div class="col-md-4">
                            <select class="form-control item-product" required>
                                <option value="">Select Product</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control item-quantity" placeholder="Qty" min="1" required>
                        </div>
                        <div class="col-md-2">
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
 
            // Populate products in the new row
            const productSelect = newItem.find('.item-product');
            products.forEach(product => {
                const optionText = `${product.name} - ₱${parseFloat(product.price).toLocaleString('en-US', {minimumFractionDigits: 2})}`;
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
                        const optionText = `${product.name} - ₱${parseFloat(product.price).toLocaleString('en-US', {minimumFractionDigits: 2})}`;
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

            // Mass delete functionality
            function toggleMassDeleteButton() {
                const checkedItems = $('.item-checkbox:checked').length;
                if (checkedItems > 0) {
                    $('#massDeleteBtn').show();
                } else {
                    $('#massDeleteBtn').hide();
                }
            }

            function handleMassDelete() {
                const checkedItems = $('.item-checkbox:checked');
                const totalItems = $('.order-item-row').length;
                
                if (checkedItems.length === 0) {
                    showNotification('Please select items to delete', 'error');
                    return;
                }

                // Prevent deleting all items if it would leave the form empty
                if (checkedItems.length >= totalItems) {
                    if (!confirm('This will delete all items. Are you sure?')) {
                        return;
                    }
                }

                // Remove checked items
                checkedItems.closest('.order-item-row').remove();
                
                // Ensure at least one item row exists
                if ($('.order-item-row').length === 0) {
                    addOrderItem();
                }
                
                // Update UI
                updateRemoveButtons();
                toggleMassDeleteButton();
                calculateOrderTotal();
                $('#selectAllItems').prop('checked', false);
                
                showNotification(`${checkedItems.length} item(s) deleted successfully`, 'success');
            }

            function handleSelectAll() {
                const isChecked = $('#selectAllItems').is(':checked');
                $('.item-checkbox').prop('checked', isChecked);
                toggleMassDeleteButton();
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
     
                // Collect order items from kiosk cart
                const items = [];
                let isValid = true;
     
                // Convert kiosk cart to order items
                Object.entries(kioskCart).forEach(([productId, cartItem]) => {
                    const product = cartItem.product;
                    const quantity = cartItem.quantity;
                    
                    if (!product || !quantity || quantity <= 0) {
                        isValid = false;
                        return;
                    }
     
                    // Validate against inventory
                    if (product.quantity === 0) {
                        showNotification(`${product.name} is out of stock. Please remove it from your cart.`, 'error');
                        isValid = false;
                        return;
                    }
     
                    if (quantity > product.quantity) {
                        showNotification(`Cannot order ${quantity} units of ${product.name}. Only ${product.quantity} units available.`, 'error');
                        isValid = false;
                        return;
                    }
     
                    items.push({
                        name: product.name,
                        quantity: quantity,
                        price: parseFloat(product.price),
                        product_id: productId
                    });
                });

                if (!isValid || items.length === 0) {
                    showNotification('Please select at least one product for your order', 'error');
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
                    await loadDashboardSummary(); // Refresh dashboard
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
                        await loadDashboardSummary(); // Refresh dashboard

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

            // Dashboard Summary Functions
            async function loadDashboardSummary() {
                try {
                    const summary = await getFinalOrderSummary();
                    finalOrderSummary = summary;
                    renderDashboardSummary(summary);
                } catch (error) {
                    console.error('Failed to load dashboard summary:', error);
                    $('#dashboardSummaryContent').html(`
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-circle text-danger" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2">Failed to load summary</p>
                            <button class="btn btn-sm btn-primary" onclick="loadDashboardSummary()">
                                <i class="fas fa-sync-alt mr-1"></i>Retry
                            </button>
                        </div>
                    `);
                }
            }

            function renderDashboardSummary(summary) {
                const content = $('#dashboardSummaryContent');
                content.empty();

                // Check if there are no orders
                if (!summary.brands || summary.brands.length === 0) {
                    content.html(`
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-cart" style="font-size: 2.5rem; color: #d1d5db;"></i>
                            <p class="text-muted mt-3 mb-0">No orders yet</p>
                            <small class="text-muted">Create orders to see summary</small>
                        </div>
                    `);
                    $('#processOrderBtn').prop('disabled', true);
                    return;
                }

                let grandTotal = 0;

                // Render each brand
                summary.brands.forEach(brand => {
                    const brandSection = $(`<div class="dashboard-brand-section"></div>`);
                    brandSection.append(`<div class="dashboard-brand-title"><i class="fas fa-store mr-1"></i>${brand.name}</div>`);

                    brand.branches.forEach(branch => {
                        const branchTotal = branch.orders.reduce((sum, order) => sum + parseFloat(order.total_amount), 0);
                        grandTotal += branchTotal;

                        const totalItems = branch.orders.reduce((sum, order) => sum + order.items.length, 0);

                        const branchItem = $(`
                            <div class="dashboard-branch-item">
                                <div class="dashboard-branch-name">
                                    <i class="fas fa-map-marker-alt mr-1"></i>${branch.name}
                                </div>
                                <div class="dashboard-branch-total">
                                    ₱${branchTotal.toLocaleString('en-US', {minimumFractionDigits: 2})}
                                </div>
                                <span class="summary-item-count">${totalItems} items</span>
                            </div>
                        `);

                        brandSection.append(branchItem);
                    });

                    content.append(brandSection);
                });

                // Add grand total
                content.append(`
                    <div class="dashboard-grand-total">
                        <i class="fas fa-calculator mr-2"></i>
                        GRAND TOTAL<br>
                        ₱${grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2})}
                    </div>
                `);

                $('#processOrderBtn').prop('disabled', false);
            }

            async function handleProcessOrder() {
                if (!finalOrderSummary || !finalOrderSummary.brands || finalOrderSummary.brands.length === 0) {
                    showNotification('No orders to process', 'error');
                    return;
                }

                if (confirm('Are you sure you want to process these orders? This will generate a PDF and update inventory.')) {
                    generatePDF();
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
                                <td class="text-center">
                                    <span class="badge ${item.current_stock < item.deduction_amount ? 'badge-danger' : 'badge-success'}">
                                        ${item.current_stock}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-warning">${item.deduction_amount}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge ${item.after_deduction === 0 ? 'badge-danger' : (item.after_deduction <= 10 ? 'badge-warning' : 'badge-info')}">
                                        ${item.after_deduction}
                                    </span>
                                </td>
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
                                                <th class="text-center">Current Stock</th>
                                                <th class="text-center">To Deduct</th>
                                                <th class="text-center">After Deduction</th>
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
                    email: "Owner@example.com"
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
                                    ['Item', 'Qty', 'Price', 'Total', 'Stock', 'Deduct', 'After']
                                ],
                                body: tableData,
                                startY: yPosition,
                                margin: {
                                    left: 15,
                                    right: 15
                                },
                                styles: {
                                    fontSize: 9,
                                    cellPadding: 2
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
                                        cellWidth: 50
                                    },
                                    1: {
                                        cellWidth: 18,
                                        halign: 'center'
                                    },
                                    2: {
                                        cellWidth: 25,
                                        halign: 'right'
                                    },
                                    3: {
                                        cellWidth: 25,
                                        halign: 'right'
                                    },
                                    4: {
                                        cellWidth: 20,
                                        halign: 'center'
                                    },
                                    5: {
                                        cellWidth: 20,
                                        halign: 'center'
                                    },
                                    6: {
                                        cellWidth: 20,
                                        halign: 'center'
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

                    const response = await fetch('{{ route('owner.orders.deduct-inventory') }}', {
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
