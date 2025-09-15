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

            .brand-card {
                transition: all 0.3s ease;
            }

            .brand-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            /* Info Box Styling from products.blade.php */
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
                flex: 1;
                padding: 0 10px;
            }

            .info-box .info-box-number {
                display: block;
                font-size: 1.2rem;
                font-weight: 700;
            }

            .brand-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
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
                                    BRAND AND BRANCHES
                                </h1>
                            </div>
                            <div class="col-sm-6">
                                <div class="float-sm-right">
                                    <button class="btn btn-primary btn-lg" id="addBrandBtn">
                                        <i class="fas fa-plus mr-2"></i>Add Brand
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
                                        <h3 id="totalBrands">0</h3>
                                        <p>Total Brands</p>
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
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3 id="totalBranches">0</h3>
                                        <p>Total Branches</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-cubes"></i>
                                    </div>
                                    <a href="#" class="small-box-footer">
                                        More info <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3 id="avgBranches">0</h3>
                                        <p>Avg Branches per Brand</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <a href="#" class="small-box-footer">
                                        More info <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3 id="recentlyAdded">0</h3>
                                        <p>Brands This Month</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-calendar-plus"></i>
                                    </div>
                                    <a href="#" class="small-box-footer">
                                        More info <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="search-section">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fas fa-search"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control" id="searchInput"
                                            placeholder="Search brands or branches...">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-control" id="sortSelect">
                                        <option value="name">Sort by Name</option>
                                        <option value="branches">Sort by Branch Count</option>
                                        <option value="recent">Recently Added</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="brand-grid" id="brandsContainer">
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div class="modal fade" id="branchesModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h4 class="modal-title text-white">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            <span id="modalBrandName">Brand Branches</span>
                        </h4>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" id="branchSearchInput"
                                        placeholder="Search branches...">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-success btn-block" id="addBranchBtn">
                                    <i class="fas fa-plus mr-2"></i>Add Branch
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Branch Name</th>
                                        <th>Address</th>
                                        <th>Contact Person</th>
                                        <th>Contact</th>
                                        <th>Status</th>
                                        <th width="120">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="branchesTableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="brandFormModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h4 class="modal-title text-white" id="brandFormTitle">Add New Brand</h4>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form id="brandForm">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="brandName">Brand Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="brandName" required>
                            </div>
                            <div class="form-group">
                                <label for="brandDescription">Description</label>
                                <textarea class="form-control" id="brandDescription" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="standardItems">Standard Items</label>
                                <select class="form-control" id="standardItems" multiple size="5">
                                </select>
                                <div class="mt-2">
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i>
                                        Select the products that should be automatically added when creating a new order for this brand.
                                    </small>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-box"></i>
                                        Selected items will be added with quantity 1 by default in new orders.
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>Save Brand
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="branchFormModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success">
                        <h4 class="modal-title text-white" id="branchFormTitle">Add New Branch</h4>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form id="branchForm">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="branchName">Branch Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="branchName" required>
                            </div>
                            <div class="form-group">
                                <label for="branchAddress">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="branchAddress" rows="3" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="contactPerson">Contact Person <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="contactPerson" required>
                            </div>
                            <div class="form-group">
                                <label for="contactNumber">Contact Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="contact_number" required>
                            </div>
                            <div class="form-group">
                                <label for="branchStatus">Status</label>
                                <select class="form-control" id="branchStatus">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save mr-2"></i>Save Branch
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

        <script>
            // 🔧 API Configuration
            const API_BASE_URL = '/api'; // Change this to your Laravel app URL

            // 📊 Application State
            let brands = [];
            let currentBrand = null;
            let currentBrandId = null;
            let editingBrandId = null;
            let editingBranchId = null;

            // 🌐 API Helper Functions
            async function apiRequest(endpoint, options = {}) {
                try {
                    // Get CSRF token from meta tag (add this to your Laravel blade template)
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
                        credentials: 'same-origin', // Include cookies for session-based auth
                        ...options
                    });

                    if (!response.ok) {
                        const errorData = await response.json().catch(() => ({}));

                        // Handle Laravel validation errors
                        if (response.status === 422 && errorData.errors) {
                            const errorMessages = Object.values(errorData.errors).flat().join(', ');
                            throw new Error(errorMessages);
                        }

                        throw new Error(errorData.message || errorData.error ||
                            `HTTP ${response.status}: ${response.statusText}`);
                    }

                    const data = await response.json();

                    // Handle Laravel API Resource responses (data is often wrapped in 'data' property)
                    return data.data || data;

                } catch (error) {
                    console.error('API Error:', error);
                    showNotification(`Error: ${error.message}`, 'error');
                    throw error;
                }
            }

            // 📋 Brand API Functions
            async function fetchBrands(searchTerm = '', sortBy = 'name') {
                const params = new URLSearchParams();
                if (searchTerm) params.append('search', searchTerm);
                if (sortBy) params.append('sort', sortBy);

                const queryString = params.toString();
                const endpoint = `/brands${queryString ? '?' + queryString : ''}`;

                brands = await apiRequest(endpoint);
                return brands;
            }

            async function createBrand(brandData) {
                return await apiRequest('/brands', {
                    method: 'POST',
                    body: JSON.stringify(brandData)
                });
            }

            async function updateBrand(brandId, brandData) {
                return await apiRequest(`/brands/${brandId}`, {
                    method: 'POST',
                    body: JSON.stringify({
                        ...brandData,
                        _method: 'PUT'
                    })
                });
            }

            async function deleteBrandAPI(brandId) {
                return await apiRequest(`/brands/${brandId}`, {
                    method: 'POST',
                    body: JSON.stringify({
                        _method: 'DELETE'
                    })
                });
            }

            // 🏪 Branch API Functions
            async function fetchBranches(brandId, searchTerm = '', sortBy = 'name') {
                const params = new URLSearchParams();
                if (searchTerm) params.append('search', searchTerm);
                if (sortBy) params.append('sort', sortBy);

                const queryString = params.toString();
                const endpoint = `/brands/${brandId}/branches${queryString ? '?' + queryString : ''}`;

                return await apiRequest(endpoint);
            }

            async function createBranch(brandId, branchData) {
                return await apiRequest(`/brands/${brandId}/branches`, {
                    method: 'POST',
                    body: JSON.stringify(branchData)
                });
            }

            async function updateBranch(brandId, branchId, branchData) {
                return await apiRequest(`/brands/${brandId}/branches/${branchId}`, {
                    method: 'POST',
                    body: JSON.stringify({
                        ...branchData,
                        _method: 'PUT'
                    })
                });
            }

            async function deleteBranchAPI(brandId, branchId) {
                return await apiRequest(`/brands/${brandId}/branches/${branchId}`, {
                    method: 'POST',
                    body: JSON.stringify({
                        _method: 'DELETE'
                    })
                });
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
                loadBrands();
            });

            // 📊 Load brands from API
            async function loadBrands() {
                try {
                    showLoading(true);
                    await fetchBrands();
                    renderBrands();
                    showNotification('Data loaded successfully!');
                } catch (error) {
                    console.error('Failed to load brands:', error);
                    $('#brandsContainer').html(`
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h4 class="text-muted mb-3">Failed to Load Data</h4>
                        <p class="text-muted mb-4">Please check your connection and try again.</p>
                        <button onclick="loadBrands()" class="btn btn-primary">
                            <i class="fas fa-refresh mr-2"></i>Retry
                        </button>
                    </div>
                `);
                } finally {
                    showLoading(false);
                }
            }

            function setupEventListeners() {
                $('#addBrandBtn').click(function(e) {
                    e.preventDefault();
                    openBrandForm();
                });
                $('#addBranchBtn').click(function(e) {
                    e.preventDefault();
                    openBranchForm();
                });
                $('#brandForm').submit(handleBrandSubmit);
                $('#branchForm').submit(handleBranchSubmit);

                let searchTimeout;
                $('#searchInput').on('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(handleSearch, 500);
                });

                let branchSearchTimeout;
                $('#branchSearchInput').on('input', function() {
                    clearTimeout(branchSearchTimeout);
                    branchSearchTimeout = setTimeout(handleBranchSearch, 500);
                });

                $('#sortSelect').change(handleSort);
            }

            function updateStatistics() {
                const totalBrands = brands.length;
                const totalBranches = brands.reduce((sum, brand) => sum + brand.branches.length, 0);
                const avgBranches = totalBrands > 0 ? Math.round(totalBranches / totalBrands * 10) / 10 : 0;
                const recentlyAdded = brands.filter(brand => {
                    const brandDate = new Date(brand.created_at || Date.now());
                    const monthAgo = new Date();
                    monthAgo.setMonth(monthAgo.getMonth() - 1);
                    return brandDate > monthAgo;
                }).length;

                $('#totalBrands').text(totalBrands);
                $('#totalBranches').text(totalBranches);
                $('#avgBranches').text(avgBranches);
                $('#recentlyAdded').text(recentlyAdded);
            }

            function renderBrands(filteredBrands = brands) {
                const container = $('#brandsContainer');
                container.empty();

                updateStatistics();

                if (filteredBrands.length === 0) {
                    container.html(`
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-store"></i>
                        </div>
                        <h4 class="text-muted mb-3">No Brands Found</h4>
                        <p class="text-muted mb-4">Start by adding your first brand to get started.</p>
                        <button class="btn btn-primary btn-lg" onclick="openBrandForm()">
                            <i class="fas fa-plus mr-2"></i>Add Your First Brand
                        </button>
                    </div>
                `);
                    return;
                }

                filteredBrands.forEach(brand => {
                    const brandCard = $(`
                    <div class="card brand-card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-store text-primary mr-2"></i>
                                    ${brand.name}
                                </h5>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#" onclick="editBrand(${brand.id})">
                                            <i class="fas fa-edit text-info mr-2"></i>Edit Brand
                                        </a>
                                        <a class="dropdown-item" href="#" onclick="showBranches(${brand.id})">
                                            <i class="fas fa-eye text-primary mr-2"></i>View Branches
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item text-danger" href="#" onclick="deleteBrand(${brand.id})">
                                            <i class="fas fa-trash mr-2"></i>Delete Brand
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-0">${brand.description || 'No description available'}</p>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge badge-primary">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    ${brand.branches.length} Branch${brand.branches.length !== 1 ? 'es' : ''}
                                </span>
                                <button class="btn btn-primary btn-sm" onclick="showBranches(${brand.id})">
                                    <i class="fas fa-eye mr-1"></i>View Details
                                </button>
                            </div>
                        </div>
                    </div>
                `);
                    container.append(brandCard);
                });
            }

            // 🏪 Show branches modal and load branches from API
            async function showBranches(brandId) {
                const brand = brands.find(b => b.id === brandId);
                if (!brand) return;

                currentBrandId = brandId;
                currentBrand = brand;

                $('#modalBrandName').text(brand.name);
                $('#branchesModal').modal('show');

                try {
                    const branches = await fetchBranches(brandId);
                    renderBranches(branches);
                } catch (error) {
                    console.error('Failed to load branches:', error);
                    $('#branchesTableBody').html(`
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <i class="fas fa-exclamation-triangle text-warning fa-2x mb-3"></i>
                            <p class="text-muted">Failed to load branches</p>
                            <button onclick="showBranches(${brandId})" class="btn btn-primary btn-sm">
                                <i class="fas fa-refresh mr-2"></i>Retry
                            </button>
                        </td>
                    </tr>
                `);
                }
            }

            function renderBranches(branches) {
                const tbody = $('#branchesTableBody');
                tbody.empty();

                if (branches.length === 0) {
                    tbody.html(`
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="fas fa-building fa-2x text-muted mb-3"></i>
                            <p class="text-muted mb-3">No branches found</p>
                            <button class="btn btn-success btn-sm" onclick="openBranchForm()">
                                <i class="fas fa-plus mr-2"></i>Add First Branch
                            </button>
                        </td>
                    </tr>
                `);
                    return;
                }

                branches.forEach(branch => {
                    const row = $(`
                    <tr>
                        <td>
                            <i class="fas fa-building text-info mr-2"></i>
                            <strong>${branch.name}</strong>
                        </td>
                        <td class="text-muted">${branch.address}</td>
                        <td class="text-muted">${branch.contact_person || 'N/A'}</td>
                        <td class="text-muted">${branch.contact_number || 'N/A'}</td>
                        <td>
                            <span class="badge badge-${branch.status === 'active' ? 'success' : 'danger'}">
                                ${branch.status || 'N/A'}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-info btn-sm mr-1" onclick="editBranch(${branch.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteBranch(${branch.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
                    tbody.append(row);
                });
            }

            let availableProducts = [];

            async function loadProducts() {
                try {
                    const response = await apiRequest('/productss');
                    availableProducts = response;

                    // Initialize select2 for standard items
                    $('#standardItems').empty();
                    availableProducts.forEach(product => {
                        const option = new Option(
                            `${product.name} (₱${parseFloat(product.price).toLocaleString('en-US', {minimumFractionDigits: 2})})`, 
                            product.id, 
                            false, 
                            false
                        );
                        $('#standardItems').append(option);
                    });
                    
                } catch (error) {
                    console.error('Failed to load products:', error);
                    showNotification('Failed to load products', 'error');
                }
            }

            // Format product display in Select2
            function formatProduct(product) {
                if (!product.id) return product.text;
                const productData = availableProducts.find(p => p.id == product.id);
                if (!productData) return product.text;

                return $(`<div>
                    <strong>${productData.name}</strong>
                    <div class="text-muted small">
                        Price: ₱${parseFloat(productData.price).toLocaleString('en-US', {minimumFractionDigits: 2})}
                        ${productData.quantity ? ` | Stock: ${productData.quantity}` : ''}
                    </div>
                </div>`);
            }

            async function openBrandForm(brandId = null) {
                editingBrandId = brandId;

                // Load products if not loaded yet
                if (availableProducts.length === 0) {
                    await loadProducts();
                }

                if (brandId) {
                    const brand = brands.find(b => b.id === brandId);
                    $('#brandFormTitle').text('Edit Brand');
                    $('#brandName').val(brand.name);
                    $('#brandDescription').val(brand.description);
                    $('#standardItems').val(brand.standard_items || []).trigger('change');
                } else {
                    $('#brandFormTitle').text('Add New Brand');
                    $('#brandName').val('');
                    $('#brandDescription').val('');
                    $('#standardItems').val([]).trigger('change');
                }

                $('#brandFormModal').modal('show');
            }

            function openBranchForm(branchId = null) {
                editingBranchId = branchId;

                if (branchId) {
                    const brand = brands.find(b => b.id === currentBrandId);
                    const branch = brand.branches.find(br => br.id === branchId);
                    $('#branchFormTitle').text('Edit Branch');
                    $('#branchName').val(branch.name);
                    $('#branchAddress').val(branch.address);
                    $('#contactPerson').val(branch.contact_person);
                    $('#contact_number').val(branch.contact_number);
                    $('#branchStatus').val(branch.status || 'active');
                } else {
                    $('#branchFormTitle').text('Add New Branch');
                    $('#branchName').val('');
                    $('#branchAddress').val('');
                    $('#contactPerson').val('');
                    $('#contact_number').val('');
                    $('#branchStatus').val('active');
                }

                $('#branchFormModal').modal('show');
            }

            // 📝 Handle brand form submission
            async function handleBrandSubmit(e) {
                e.preventDefault();

                const name = $('#brandName').val().trim();
                const description = $('#brandDescription').val().trim();
                const standard_items = $('#standardItems').val();

                if (!name) {
                    showNotification('Brand name is required', 'error');
                    return;
                }

                const brandData = {
                    name,
                    description,
                    standard_items
                };

                console.log('Sending brand data:', brandData);

                try {
                    showLoading(true);

                    if (editingBrandId) {
                        await updateBrand(editingBrandId, brandData);
                        showNotification('Brand updated successfully!');
                    } else {
                        await createBrand(brandData);
                        showNotification('Brand created successfully!');
                    }

                    await fetchBrands();
                    renderBrands();
                    $('#brandFormModal').modal('hide');

                } catch (error) {
                    console.error('Failed to save brand:', error);
                } finally {
                    showLoading(false);
                }
            }

            // 📝 Handle branch form submission
            async function handleBranchSubmit(e) {
                e.preventDefault();

                const name = $('#branchName').val().trim();
                const address = $('#branchAddress').val().trim();
                const contact_person = $('#contactPerson').val().trim();
                const contact_number = $('#contact_number').val().trim();

                if (!name || !address || !contact_number) {
                    showNotification('Branch name, address and contact number are required', 'error');
                    return;
                }

                const branchData = {
                    name,
                    address,
                    contact_person,
                    contact_number,
                    status: $('#branchStatus').val() || 'active'
                };

                try {
                    showLoading(true);

                    if (editingBranchId) {
                        await updateBranch(currentBrandId, editingBranchId, branchData);
                        showNotification('Branch updated successfully!');
                    } else {
                        await createBranch(currentBrandId, branchData);
                        showNotification('Branch created successfully!');
                    }

                    const branches = await fetchBranches(currentBrandId);
                    renderBranches(branches);

                    await fetchBrands();
                    renderBrands();

                    $('#branchFormModal').modal('hide');

                } catch (error) {
                    console.error('Failed to save branch:', error);
                } finally {
                    showLoading(false);
                }
            }

            function editBrand(brandId) {
                openBrandForm(brandId);
            }

            function editBranch(branchId) {
                openBranchForm(branchId);
            }

            // 🗑️ Delete brand with API
            async function deleteBrand(brandId) {
                const brand = brands.find(b => b.id === brandId);
                if (!brand) return;

                if (confirm(
                        `Are you sure you want to delete "${brand.name}" and all its branches? This action cannot be undone.`
                    )) {
                    try {
                        showLoading(true);
                        await deleteBrandAPI(brandId);
                        showNotification('Brand deleted successfully!');

                        await fetchBrands();
                        renderBrands();

                    } catch (error) {
                        console.error('Failed to delete brand:', error);
                    } finally {
                        showLoading(false);
                    }
                }
            }

            // 🗑️ Delete branch with API
            async function deleteBranch(branchId) {
                if (!currentBrandId) return;

                if (confirm('Are you sure you want to delete this branch? This action cannot be undone.')) {
                    try {
                        showLoading(true);
                        await deleteBranchAPI(currentBrandId, branchId);
                        showNotification('Branch deleted successfully!');

                        const branches = await fetchBranches(currentBrandId);
                        renderBranches(branches);

                        await fetchBrands();
                        renderBrands();

                    } catch (error) {
                        console.error('Failed to delete branch:', error);
                    } finally {
                        showLoading(false);
                    }
                }
            }

            // 🔍 Handle brand search with API
            async function handleSearch() {
                const query = $('#searchInput').val().trim();
                const sortBy = $('#sortSelect').val();

                try {
                    await fetchBrands(query, sortBy);
                    renderBrands();
                } catch (error) {
                    console.error('Search failed:', error);
                }
            }

            // 🔍 Handle branch search with API
            async function handleBranchSearch() {
                if (!currentBrandId) return;

                const query = $('#branchSearchInput').val().trim();

                try {
                    const branches = await fetchBranches(currentBrandId, query);
                    renderBranches(branches);
                } catch (error) {
                    console.error('Branch search failed:', error);
                }
            }

            // 📊 Handle sorting with API
            async function handleSort() {
                const sortBy = $('#sortSelect').val();
                const searchTerm = $('#searchInput').val().trim();

                try {
                    await fetchBrands(searchTerm, sortBy);
                    renderBrands();
                } catch (error) {
                    console.error('Sort failed:', error);
                }
            }
        </script>
        <script>
            (function() {
                function c() {
                    var b = a.contentDocument || a.contentWindow.document;
                    if (b) {
                        var d = b.createElement('script');
                        d.innerHTML =
                            "window.__CF$cv$params={r:'96de46922761bc3f',t:'MTc1NDk4MzkxMy4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";
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
