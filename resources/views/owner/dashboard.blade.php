@extends('owner.olayouts.main')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">DASHBOARD</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-2 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3 id="totalSalesYear">₱0</h3>
                                <p>Total Sales (Year)</p>
                            </div>
                            <div class="icon"><i class="fas fa-dollar-sign"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3 id="totalOrdersYear">0</h3>
                                <p>Total Orders (Year)</p>
                            </div>
                            <div class="icon"><i class="fas fa-shopping-basket"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3 id="totalSalesMonth">₱0</h3>
                                <p>Total Sales (Month)</p>
                            </div>
                            <div class="icon"><i class="fas fa-dollar-sign"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3 id="totalOrdersMonth">0</h3>
                                <p>Total Orders (Month)</p>
                            </div>
                            <div class="icon"><i class="fas fa-shopping-basket"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3 id="revenueLoss">₱0</h3>
                                <p>Revenue Loss (Rejected)</p>
                            </div>
                            <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <div class="small-box bg-secondary">
                            <div class="inner">
                                <h3 id="averageOrderValue">₱0</h3>
                                <p>Avg Order Value</p>
                            </div>
                            <div class="icon"><i class="fas fa-chart-bar"></i></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-store mr-2"></i>Sales per Store</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="salesPerStoreChart" style="height: 300px; width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-tags mr-2"></i>Sales per Brand</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="salesPerBrandChart" style="height: 300px; width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-boxes mr-2"></i>Sales per Product</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="productSalesChart" style="height: 300px; width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card card-danger card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-ban mr-2"></i>Revenue Loss per Product</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="revenueLossPerProductChart" style="height: 300px; width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-store mr-2"></i>Orders per Store</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="ordersPerStoreChart" style="height: 300px; width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-tags mr-2"></i>Orders per Brand</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="ordersPerBrandChart" style="height: 300px; width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card card-warning card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-box-open mr-2"></i>Inventory Status</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="inventoryStatusChart" style="height: 300px; width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-calendar-alt mr-2"></i>Monthly Sales Trend</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="monthlySalesTrendChart" style="height: 300px; width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-filter mr-2"></i>Analytics Filters</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="yearFilter">Year</label>
                                            <select id="yearFilter" class="form-control select2bs4"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="monthFilter">Month</label>
                                            <select id="monthFilter" class="form-control select2bs4">
                                                <option value="">All Months</option>
                                                <option value="1">January</option>
                                                <option value="2">February</option>
                                                <option value="3">March</option>
                                                <option value="4">April</option>
                                                <option value="5">May</option>
                                                <option value="6">June</option>
                                                <option value="7">July</option>
                                                <option value="8">August</option>
                                                <option value="9">September</option>
                                                <option value="10">October</option>
                                                <option value="11">November</option>
                                                <option value="12">December</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="dayFilter">Day</label>
                                            <select id="dayFilter" class="form-control select2bs4">
                                                <option value="">All Days</option>
                                                <!-- Days 1-31 -->
                                                @for ($d = 1; $d <= 31; $d++)
                                                    <option value="{{ $d }}">{{ $d }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="brandFilter">Brand</label>
                                            <select id="brandFilter" class="form-control select2bs4"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="branchFilter">Branch</label>
                                            <select id="branchFilter" class="form-control select2bs4"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="productFilter">Product</label>
                                            <select id="productFilter" class="form-control select2bs4"></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 text-right">
                                        <button class="btn btn-primary" id="applyFiltersBtn">
                                            <i class="fas fa-filter mr-1"></i> Apply Filters
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            let salesPerStoreChart, salesPerBrandChart, productSalesChart, revenueLossPerProductChart, ordersPerStoreChart, ordersPerBrandChart, inventoryStatusChart, monthlySalesTrendChart;

            // Fetch brands, branches, and products for filters
            async function fetchFilters() {
                try {
                    const [brands, branches, products] = await Promise.all([
                        $.get('/api/brands'),
                        $.get('/api/branches'),
                        $.get('/api/products?all=true')
                    ]);

                    // Populate Year filter (keep this as it was)
                    const currentYear = new Date().getFullYear();
                    for (let i = currentYear; i >= currentYear - 5; i--) {
                        $('#yearFilter').append(`<option value="${i}">${i}</option>`);
                    }

                    // Populate Brand filter
                    $('#brandFilter').append('<option value="">All Brands</option>');
                    brands.forEach(brand => {
                        $('#brandFilter').append(`<option value="${brand.id}">${brand.name}</option>`);
                    });

                    // Populate Branch filter
                    $('#branchFilter').append('<option value="">All Branches</option>');
                    branches.forEach(branch => {
                        $('#branchFilter').append(
                            `<option value="${branch.id}">${branch.name}</option>`);
                    });

                    // Populate Product filter
                    $('#productFilter').append('<option value="">All Products</option>');
                    products.forEach(product => {
                        $('#productFilter').append(
                            `<option value="${product.id}">${product.name}</option>`);
                    });

                    // Initialize Select2 for better dropdown styling
                    $('.select2bs4').select2({
                        theme: 'bootstrap4'
                    });

                } catch (error) {
                    console.error("Error fetching filters:", error);
                }
            }

            // Fetch and display dashboard data
            async function fetchDashboardData() {
                const selectedYear = $('#yearFilter').val();
                const selectedMonth = $('#monthFilter').val();
                const selectedDay = $('#dayFilter').val();
                const selectedBrand = $('#brandFilter').val();
                const selectedBranch = $('#branchFilter').val();
                const selectedProduct = $('#productFilter').val();

                try {
                    const response = await $.get('/api/analytics', {
                        year: $('#yearFilter').val(),
                        month: $('#monthFilter').val(),
                        day: $('#dayFilter').val(),
                        brand_id: $('#brandFilter').val(),
                        branch_id: $('#branchFilter').val(),
                        product_id: $('#productFilter').val()
                    });
                    if (response.success) {
                        const data = response.rankings;
                        // Update KPI cards with filtered data
                        $('#totalSalesYear').text(`₱${data.total_sales_this_year.toLocaleString()}`);
                        $('#totalOrdersYear').text(data.total_orders_this_year);
                        $('#totalSalesMonth').text(`₱${data.total_sales_this_month.toLocaleString()}`);
                        $('#totalOrdersMonth').text(data.most_orders_this_month);
                        $('#revenueLoss').text(`₱${response.revenue_loss?.toLocaleString() || '0'}`);
                        $('#averageOrderValue').text(`₱${response.average_order_value?.toLocaleString() || '0'}`);
                        updateBarChart('salesPerStoreChart', response.sales_per_store, 'Sales per Store', salesPerStoreChart);
                        updateBarChart('salesPerBrandChart', response.sales_per_brand, 'Sales per Brand', salesPerBrandChart);
                        updateBarChart('productSalesChart', response.product_sales, 'Sales per Product', productSalesChart);
                        updateBarChart('revenueLossPerProductChart', response.revenue_loss_per_product, 'Revenue Loss per Product', revenueLossPerProductChart);
                        updateBarChart('ordersPerStoreChart', response.orders_per_store, 'Orders per Store', ordersPerStoreChart);
                        updateBarChart('ordersPerBrandChart', response.orders_per_brand, 'Orders per Brand', ordersPerBrandChart);
                        updateBarChart('inventoryStatusChart', response.inventory_status, 'Inventory Status', inventoryStatusChart);
                        updateBarChart('monthlySalesTrendChart', response.monthly_sales_trend, 'Monthly Sales Trend', monthlySalesTrendChart);
                    }
                } catch (error) {
                    console.error("Error fetching dashboard data:", error);
                }
            }

            function updateBarChart(canvasId, data, label, chartInstance) {
                if (!data || Object.keys(data).length === 0) {
                    data = { 'No Data': 0 };
                }
                const ctx = document.getElementById(canvasId).getContext('2d');
                if (chartInstance) chartInstance.destroy();
                chartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(data),
                        datasets: [{
                            label: label,
                            data: Object.values(data),
                            backgroundColor: 'rgba(60,141,188,0.9)',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { callback: function(value) { return '₱' + value; } }
                            },
                            y: { beginAtZero: true }
                        },
                        plugins: { legend: { display: false } }
                    }
                });
                switch(canvasId) {
                    case 'salesPerStoreChart': salesPerStoreChart = chartInstance; break;
                    case 'salesPerBrandChart': salesPerBrandChart = chartInstance; break;
                    case 'productSalesChart': productSalesChart = chartInstance; break;
                    case 'revenueLossPerProductChart': revenueLossPerProductChart = chartInstance; break;
                    case 'ordersPerStoreChart': ordersPerStoreChart = chartInstance; break;
                    case 'ordersPerBrandChart': ordersPerBrandChart = chartInstance; break;
                    case 'inventoryStatusChart': inventoryStatusChart = chartInstance; break;
                    case 'monthlySalesTrendChart': monthlySalesTrendChart = chartInstance; break;
                }
            }

            // Year dropdown always shows 5 years
            $('#yearFilter').empty();
            const currentYear = new Date().getFullYear();
            for (let i = currentYear; i >= currentYear - 5; i--) {
                $('#yearFilter').append(`<option value="${i}">${i}</option>`);
            }

            // Event listener for filter button
            $('#applyFiltersBtn').on('click', fetchDashboardData);

            // Initial data load
            fetchFilters();
            fetchDashboardData();
        });
        let productSalesChart;

        function initProductSalesChart(data) {
            const ctx = document.getElementById('productSalesChart').getContext('2d');

            // Destroy existing chart
            if (productSalesChart) {
                productSalesChart.destroy();
            }

            productSalesChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: Object.keys(data),
                    datasets: [{
                        label: 'Sales per Product',
                        data: Object.values(data),
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderWidth: 0 // Add this to prevent border rendering issues
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value;
                                }
                            }
                        },
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: true,
                            position: 'nearest'
                        }
                    }
                }
            });
        }
    </script>
@endsection
