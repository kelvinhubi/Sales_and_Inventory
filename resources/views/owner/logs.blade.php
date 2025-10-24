@extends('owner.olayouts.main')
@section('content')

<head>
    <style>
        .log-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .log-card:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .log-card.severity-low {
            border-left-color: #17a2b8;
        }

        .log-card.severity-medium {
            border-left-color: #ffc107;
        }

        .log-card.severity-high {
            border-left-color: #fd7e14;
        }

        .log-card.severity-critical {
            border-left-color: #dc3545;
        }

        .badge-action {
            padding: 6px 12px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 12px;
        }

        .timeline-item {
            padding-left: 30px;
            border-left: 2px solid #e9ecef;
            position: relative;
            padding-bottom: 20px;
        }

        .timeline-item:last-child {
            border-left: none;
        }

        .timeline-marker {
            position: absolute;
            left: -8px;
            top: 0;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #fff;
            border: 3px solid #007bff;
        }

        .timeline-marker.critical {
            border-color: #dc3545;
        }

        .timeline-marker.high {
            border-color: #fd7e14;
        }

        .timeline-marker.medium {
            border-color: #ffc107;
        }

        .timeline-marker.low {
            border-color: #17a2b8;
        }

        .stat-card {
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card h3 {
            font-size: 32px;
            font-weight: bold;
            margin: 10px 0;
        }

        .stat-card p {
            margin: 0;
            opacity: 0.9;
        }

        .filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .log-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
            font-size: 13px;
        }

        .log-details pre {
            background: #fff;
            padding: 10px;
            border-radius: 5px;
            margin: 0;
            font-size: 12px;
        }
    </style>
</head>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-clipboard-list"></i> Activity Logs
                    </h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <button class="btn btn-success" id="exportLogsBtn">
                            <i class="fas fa-file-export"></i> Export to CSV
                        </button>
                        <button class="btn btn-primary" id="refreshLogsBtn">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Statistics Row -->
            <div class="row" id="statsContainer">
                <div class="col-lg-3 col-6">
                    <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="fas fa-clipboard-list fa-2x"></i>
                        <h3 id="totalActivities">0</h3>
                        <p>Total Activities</p>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <i class="fas fa-calendar-day fa-2x"></i>
                        <h3 id="todayActivities">0</h3>
                        <p>Today's Activities</p>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <i class="fas fa-calendar-week fa-2x"></i>
                        <h3 id="weekActivities">0</h3>
                        <p>This Week</p>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="stat-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                        <h3 id="criticalActivities">0</h3>
                        <p>Critical Alerts</p>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter"></i> Filters
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Action Type</label>
                                <select class="form-control" id="filterActionType">
                                    <option value="">All Actions</option>
                                    <option value="login">Login</option>
                                    <option value="logout">Logout</option>
                                    <option value="create">Create</option>
                                    <option value="update">Update</option>
                                    <option value="delete">Delete</option>
                                    <option value="password_change">Password Change</option>
                                    <option value="password_reset">Password Reset</option>
                                    <option value="failed_login">Failed Login</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Module</label>
                                <select class="form-control" id="filterModule">
                                    <option value="">All Modules</option>
                                    <option value="authentication">Authentication</option>
                                    <option value="products">Products</option>
                                    <option value="orders">Orders</option>
                                    <option value="brands">Brands</option>
                                    <option value="branches">Branches</option>
                                    <option value="users">Users</option>
                                    <option value="suppliers">Suppliers</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Severity</label>
                                <select class="form-control" id="filterSeverity">
                                    <option value="">All Severity</option>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="critical">Critical</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Search</label>
                                <input type="text" class="form-control" id="searchLogs" placeholder="Search logs...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="date" class="form-control" id="filterStartDate">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="date" class="form-control" id="filterEndDate">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button class="btn btn-primary" id="applyFiltersBtn">
                                        <i class="fas fa-filter"></i> Apply Filters
                                    </button>
                                    <button class="btn btn-secondary" id="clearFiltersBtn">
                                        <i class="fas fa-times"></i> Clear
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Logs -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i> Activity Timeline
                    </h3>
                </div>
                <div class="card-body">
                    <div id="logsContainer">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p class="mt-2">Loading activity logs...</p>
                        </div>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-4" id="paginationContainer"></div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    const API_BASE_URL = '/api';
    let currentPage = 1;
    let currentFilters = {};

    $(document).ready(function() {
        loadStatistics();
        loadLogs();

        $('#applyFiltersBtn').click(applyFilters);
        $('#clearFiltersBtn').click(clearFilters);
        $('#refreshLogsBtn').click(() => loadLogs());
        $('#exportLogsBtn').click(exportLogs);

        // Search with delay
        let searchTimeout;
        $('#searchLogs').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => applyFilters(), 500);
        });
    });

    async function loadStatistics() {
        try {
            const response = await fetch(`${API_BASE_URL}/activity-logs/statistics`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();
            if (result.success) {
                const stats = result.data;
                $('#totalActivities').text(stats.total_activities);
                $('#todayActivities').text(stats.today_activities);
                $('#weekActivities').text(stats.this_week_activities);
                $('#criticalActivities').text(stats.critical_activities);
            }
        } catch (error) {
            console.error('Failed to load statistics:', error);
        }
    }

    async function loadLogs(page = 1) {
        try {
            const params = new URLSearchParams({
                page: page,
                per_page: 20,
                ...currentFilters
            });

            const response = await fetch(`${API_BASE_URL}/activity-logs?${params}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();
            if (result.success) {
                renderLogs(result.data.data);
                renderPagination(result.data);
            }
        } catch (error) {
            console.error('Failed to load logs:', error);
            $('#logsContainer').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> Failed to load activity logs.
                </div>
            `);
        }
    }

    function renderLogs(logs) {
        const container = $('#logsContainer');
        
        if (logs.length === 0) {
            container.html(`
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No activity logs found</p>
                </div>
            `);
            return;
        }

        let html = '<div class="timeline">';
        
        logs.forEach(log => {
            const date = new Date(log.created_at);
            const formattedDate = date.toLocaleString();
            const severityClass = log.severity || 'low';
            const actionColor = getActionColor(log.action_type);
            
            html += `
                <div class="timeline-item">
                    <div class="timeline-marker ${severityClass}"></div>
                    <div class="log-card card severity-${severityClass} mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge badge-${actionColor} badge-action mr-2">
                                            ${log.action_type.toUpperCase().replace('_', ' ')}
                                        </span>
                                        <span class="badge badge-secondary mr-2">
                                            <i class="fas fa-cube"></i> ${log.module}
                                        </span>
                                        <span class="badge badge-${getSeverityColor(severityClass)}">
                                            <i class="fas fa-exclamation-circle"></i> ${severityClass.toUpperCase()}
                                        </span>
                                    </div>
                                    <h5 class="mb-2">${log.description}</h5>
                                    <p class="mb-1">
                                        <i class="fas fa-user"></i> <strong>${log.user_name}</strong> (${log.user_role})
                                    </p>
                                    <p class="mb-1 text-muted">
                                        <i class="fas fa-clock"></i> ${formattedDate}
                                    </p>
                                </div>
                                <div class="col-md-4 text-right">
                                    <p class="mb-1 text-muted">
                                        <i class="fas fa-network-wired"></i> ${log.ip_address || 'N/A'}
                                    </p>
                                    ${log.details ? `
                                        <button class="btn btn-sm btn-outline-info mt-2" onclick="toggleDetails(${log.id})">
                                            <i class="fas fa-info-circle"></i> View Details
                                        </button>
                                    ` : ''}
                                </div>
                            </div>
                            ${log.details ? `
                                <div id="details-${log.id}" class="log-details" style="display: none;">
                                    <strong>Additional Details:</strong>
                                    <pre>${JSON.stringify(JSON.parse(log.details), null, 2)}</pre>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        container.html(html);
    }

    function toggleDetails(logId) {
        $(`#details-${logId}`).slideToggle();
    }

    function getActionColor(actionType) {
        const colors = {
            'login': 'success',
            'logout': 'secondary',
            'create': 'primary',
            'update': 'info',
            'delete': 'danger',
            'password_change': 'warning',
            'password_reset': 'warning',
            'failed_login': 'danger'
        };
        return colors[actionType] || 'secondary';
    }

    function getSeverityColor(severity) {
        const colors = {
            'low': 'info',
            'medium': 'warning',
            'high': 'orange',
            'critical': 'danger'
        };
        return colors[severity] || 'secondary';
    }

    function renderPagination(data) {
        const container = $('#paginationContainer');
        
        if (data.last_page <= 1) {
            container.html('');
            return;
        }

        let html = '<nav><ul class="pagination justify-content-center">';
        
        // Previous button
        html += `
            <li class="page-item ${data.current_page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="loadLogs(${data.current_page - 1}); return false;">
                    Previous
                </a>
            </li>
        `;

        // Page numbers
        for (let i = 1; i <= data.last_page; i++) {
            if (i === 1 || i === data.last_page || (i >= data.current_page - 2 && i <= data.current_page + 2)) {
                html += `
                    <li class="page-item ${i === data.current_page ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="loadLogs(${i}); return false;">${i}</a>
                    </li>
                `;
            } else if (i === data.current_page - 3 || i === data.current_page + 3) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        // Next button
        html += `
            <li class="page-item ${data.current_page === data.last_page ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="loadLogs(${data.current_page + 1}); return false;">
                    Next
                </a>
            </li>
        `;

        html += '</ul></nav>';
        container.html(html);
    }

    function applyFilters() {
        currentFilters = {
            action_type: $('#filterActionType').val(),
            module: $('#filterModule').val(),
            severity: $('#filterSeverity').val(),
            search: $('#searchLogs').val(),
            start_date: $('#filterStartDate').val(),
            end_date: $('#filterEndDate').val()
        };

        // Remove empty filters
        Object.keys(currentFilters).forEach(key => {
            if (!currentFilters[key]) delete currentFilters[key];
        });

        loadLogs(1);
        loadStatistics();
    }

    function clearFilters() {
        $('#filterActionType').val('');
        $('#filterModule').val('');
        $('#filterSeverity').val('');
        $('#searchLogs').val('');
        $('#filterStartDate').val('');
        $('#filterEndDate').val('');
        currentFilters = {};
        loadLogs(1);
        loadStatistics();
    }

    function exportLogs() {
        const params = new URLSearchParams(currentFilters);
        window.location.href = `${API_BASE_URL}/activity-logs/export?${params}`;
    }
</script>
@endsection
