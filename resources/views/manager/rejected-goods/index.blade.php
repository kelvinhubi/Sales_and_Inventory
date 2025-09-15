<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rejected Goods - Manager Dashboard</title>
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
        @include('manager.olayouts.header')
        @include('manager.olayouts.sidebar')
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Rejected Goods</h1>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-sm-right">
                                <a href="{{ route('manager.rejected-goods.create') }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-plus mr-2"></i>Create New
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
                                        <i class="fas fa-list mr-2 text-primary"></i>Rejected Goods List
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover text-nowrap">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Brand</th>
                                                    <th>Branch</th>
                                                    <th>DR No</th>
                                                    <th>Amount</th>
                                                    <th>Reason Summary</th>
                                                    <th>Items Count</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($rejectedGoods as $rejectedGood)
                                                <tr>
                                                    <td>{{ $rejectedGood->date->format('Y-m-d') }}</td>
                                                    <td>{{ $rejectedGood->brand->name ?? '' }}</td>
                                                    <td>{{ $rejectedGood->branch->name ?? '' }}</td>
                                                    <td>{{ $rejectedGood->dr_no }}</td>
                                                    <td class="order-amount">₱{{ number_format($rejectedGood->amount, 2) }}</td>
                                                    <td>{{ Str::limit($rejectedGood->reason, 50) }}</td>
                                                    <td>{{ $rejectedGood->items->count() }}</td>
                                                    <td>
                                                        <a href="{{ route('manager.rejected-goods.show', $rejectedGood) }}" class="btn btn-info btn-sm">
                                                            <i class="fas fa-eye mr-1"></i>View
                                                        </a>
                                                        <form method="POST" action="{{ route('manager.rejected-goods.destroy', $rejectedGood) }}" style="display: inline;" onsubmit="return confirm('Are you sure?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                <i class="fas fa-trash mr-1"></i>Delete
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted py-5">
                                                        <div class="empty-state">
                                                            <i class="fas fa-inbox fa-3x mb-3" style="color: #d1d5db;"></i>
                                                            <h5>No Rejected Goods Found</h5>
                                                            <p class="mb-0">Start by creating your first rejected goods record.</p>
                                                            <a href="{{ route('manager.rejected-goods.create') }}" class="btn btn-primary mt-3">
                                                                <i class="fas fa-plus mr-2"></i>Create First Record
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="float-right">
                                        {{ $rejectedGoods->links('pagination::bootstrap-4') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        @include('manager.olayouts.footer')
    </div>
    @stack('scripts')
</body>
</html>