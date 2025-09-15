<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rejected Goods Details - Owner Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">   
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="{{ asset('js/heartbeat.js') }}"></script>
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
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        @include('owner.olayouts.header')
        @include('owner.olayouts.sidebar')
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Rejected Goods Details</h1>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-sm-right">
                                <a href="{{ route('owner.rejected-goods.index') }}" class="btn btn-secondary">
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
                                        <i class="fas fa-eye mr-2 text-primary"></i>Rejected Goods Details
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="row mb-3">
                                                <div class="col-sm-3">
                                                    <h6 class="mb-0">Date:</h6>
                                                </div>
                                                <div class="col-sm-9 text-secondary">
                                                    {{ $rejectedGood->date->format('Y-m-d') }}
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-3">
                                                    <h6 class="mb-0">Brand:</h6>
                                                </div>
                                                <div class="col-sm-9 text-secondary">
                                                    {{ $rejectedGood->brand->name ?? 'N/A' }}
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-3">
                                                    <h6 class="mb-0">Branch:</h6>
                                                </div>
                                                <div class="col-sm-9 text-secondary">
                                                    {{ $rejectedGood->branch->name ?? 'N/A' }}
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-3">
                                                    <h6 class="mb-0">DR No:</h6>
                                                </div>
                                                <div class="col-sm-9 text-secondary">
                                                    {{ $rejectedGood->dr_no }}
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-3">
                                                    <h6 class="mb-0">Amount:</h6>
                                                </div>
                                                <div class="col-sm-9 text-secondary">
                                                    ₱{{ number_format($rejectedGood->amount, 2) }}
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-3">
                                                    <h6 class="mb-0">Reason:</h6>
                                                </div>
                                                <div class="col-sm-9 text-secondary">
                                                    {{ $rejectedGood->reason }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="card-title mb-0">
                                                        <i class="fas fa-list mr-2 text-primary"></i>Items
                                                    </h5>
                                                </div>
                                                <div class="card-body">
                                                    @if($rejectedGood->items->count() > 0)
                                                    <div class="table-responsive">
                                                        <table class="table table-hover text-nowrap">
                                                            <thead class="bg-light">
                                                                <tr>
                                                                    <th>Product</th>
                                                                    <th>Quantity</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($rejectedGood->items as $item)
                                                                <tr>
                                                                    <td>{{ $item->product->name ?? 'N/A' }}</td>
                                                                    <td>{{ $item->quantity }}</td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    @else
                                                    <div class="empty-state">
                                                        <div class="empty-icon">
                                                            <i class="fas fa-box-open fa-3x" style="color: #d1d5db;"></i>
                                                        </div>
                                                        <h5 class="text-muted mb-3">No Items Found</h5>
                                                        <p class="text-muted">This rejected goods record has no associated items.</p>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <a href="{{ route('owner.rejected-goods.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left mr-2"></i>Back to List
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        @include('owner.olayouts.footer')
    </div>
    @stack('scripts')
</body>
</html>