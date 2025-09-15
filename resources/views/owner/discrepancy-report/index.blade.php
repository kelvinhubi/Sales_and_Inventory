<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Discrepancy Report - Sales vs Rejected Goods</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">   
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="{{ asset('js/heartbeat.js') }}"></script>
    <style>
        .content-wrapper {
            background-color: #f4f4f4;
        }
        .report-card {
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .filter-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px 10px 0 0;
        }
        .dr-selection {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
        }
        .dr-checkbox {
            margin: 5px 0;
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
        @include('owner.olayouts.header')
        @include('owner.olayouts.sidebar')
        
        <div class="content-wrapper" style="margin-left: 260px; position: relative; z-index: 1;">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">
                                <i class="fas fa-chart-line mr-2 text-primary"></i>
                                Discrepancy Report
                            </h1>
                            <small class="text-muted">Sales vs Rejected Goods Analysis</small>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-sm-right">
                                <a href="{{ route('owner.past-orders.index') }}" class="btn btn-secondary mr-2">
                                    <i class="fas fa-list mr-2"></i>Past Orders
                                </a>
                                <a href="{{ route('owner.rejected-goods.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>Rejected Goods
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
                            <div class="card report-card">
                                <div class="card-header filter-section">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-filter mr-2"></i>Generate Discrepancy Report
                                    </h5>
                                </div>
                                
                                <form method="GET" action="{{ route('owner.discrepancy-report.generate') }}" id="report-form">
                                    <div class="card-body">
                                        <!-- Date Range Filter -->
                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="start_date">
                                                        <i class="fas fa-calendar-alt mr-2"></i>Start Date
                                                    </label>
                                                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="end_date">
                                                        <i class="fas fa-calendar-alt mr-2"></i>End Date
                                                    </label>
                                                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- DR Number Selection -->
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>
                                                        <i class="fas fa-receipt mr-2"></i>DR Numbers (Optional)
                                                    </label>
                                                    <small class="text-muted d-block mb-2">Select specific DR numbers to include in the report, or leave empty for all DRs</small>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-2">
                                                                <button type="button" class="btn btn-sm btn-outline-primary" id="select-all-dr">
                                                                    <i class="fas fa-check-square mr-1"></i>Select All
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-outline-secondary ml-2" id="clear-all-dr">
                                                                    <i class="fas fa-square mr-1"></i>Clear All
                                                                </button>
                                                            </div>
                                                            <div class="dr-selection">
                                                                @if($drNumbers->count() > 0)
                                                                    @foreach($drNumbers as $drNumber)
                                                                        <div class="dr-checkbox">
                                                                            <div class="form-check">
                                                                                <input class="form-check-input dr-item" type="checkbox" name="dr_numbers[]" value="{{ $drNumber }}" id="dr_{{ $loop->index }}">
                                                                                <label class="form-check-label" for="dr_{{ $loop->index }}">
                                                                                    {{ $drNumber }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                @else
                                                                    <p class="text-muted text-center">
                                                                        <i class="fas fa-info-circle mr-2"></i>
                                                                        No DR numbers found. Please create some past orders or rejected goods first.
                                                                    </p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="alert alert-info">
                                                                <h6><i class="fas fa-lightbulb mr-2"></i>Report Features:</h6>
                                                                <ul class="mb-0">
                                                                    <li>Per-item breakdown of sales vs rejected goods</li>
                                                                    <li>Track quantity sold, price, and amounts per product</li>
                                                                    <li>Show rejection reasons and quantities per item</li>
                                                                    <li>Calculate net amounts after rejections per product</li>
                                                                    <li>Export detailed Excel report with professional formatting</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card-footer text-right">
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fas fa-file-excel mr-2"></i>
                                            Generate Excel Report
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Summary Stats Card -->
                            @if($drNumbers->count() > 0)
                            <div class="row mt-4">
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h4>{{ $drNumbers->count() }}</h4>
                                                    <p class="mb-0">Total DR Numbers</p>
                                                </div>
                                                <div class="align-self-center">
                                                    <i class="fas fa-receipt fa-2x"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h4>{{ \App\Models\PastOrder::whereNotNull('dr_number')->count() }}</h4>
                                                    <p class="mb-0">Past Orders</p>
                                                </div>
                                                <div class="align-self-center">
                                                    <i class="fas fa-shopping-cart fa-2x"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h4>{{ \App\Models\RejectedGood::count() }}</h4>
                                                    <p class="mb-0">Rejected Goods</p>
                                                </div>
                                                <div class="align-self-center">
                                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h4>{{ number_format(\App\Models\PastOrder::whereNotNull('dr_number')->sum('total_amount'), 2) }}</h4>
                                                    <p class="mb-0">Total Sales</p>
                                                </div>
                                                <div class="align-self-center">
                                                    <i class="fas fa-dollar-sign fa-2x"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
        </div>
        @include('owner.olayouts.footer')
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        // Select All DR Numbers
        $('#select-all-dr').click(function() {
            $('.dr-item').prop('checked', true);
        });

        // Clear All DR Numbers
        $('#clear-all-dr').click(function() {
            $('.dr-item').prop('checked', false);
        });

        // Form validation
        $('#report-form').submit(function(e) {
            const checkedDRs = $('.dr-item:checked').length;
            const totalDRs = $('.dr-item').length;
            
            if (totalDRs === 0) {
                alert('No DR numbers available to generate report.');
                e.preventDefault();
                return false;
            }

            // Show loading state
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Generating Report...');
            submitBtn.prop('disabled', true);

            // Reset button after 10 seconds (in case of any issues)
            setTimeout(function() {
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
            }, 10000);
        });
    });
    </script>
</body>
</html>