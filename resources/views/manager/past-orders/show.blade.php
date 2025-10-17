@extends('manager.olayouts.main')
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .content-wrapper {
            background-color: #f4f4f4;
            margin-left: 260px !important;
            position: relative !important;
            z-index: 1 !important;
        }
        .main-sidebar {
            z-index: 1000 !important;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }
        @media (max-width: 767.98px) {
            .content-wrapper {
                margin-left: 0 !important;
            }
            .table-responsive {
                margin: 0 -15px;
            }
        }
    </style>
    <!-- Content Wrapper -->
    <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Past Order Details</h1>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-sm-right">
                                <a href="{{ route('manager.past-orders.index') }}" class="btn btn-secondary">
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
                                        <i class="fas fa-eye mr-2 text-primary"></i>Past Order Details
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="row mb-3">
                                                <div class="col-sm-3">
                                                    <h6 class="mb-0">Order ID:</h6>
                                                </div>
                                                <div class="col-sm-9 text-secondary">
                                                    {{ $pastOrder->id }}
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-3">
                                                    <h6 class="mb-0">Brand:</h6>
                                                </div>
                                                <div class="col-sm-9 text-secondary">
                                                    {{ $pastOrder->brand->name ?? 'N/A' }}
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-3">
                                                    <h6 class="mb-0">Branch:</h6>
                                                </div>
                                                <div class="col-sm-9 text-secondary">
                                                    {{ $pastOrder->branch->name ?? 'N/A' }}
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-3">
                                                    <h6 class="mb-0">Total Amount:</h6>
                                                </div>
                                                <div class="col-sm-9 text-secondary">
                                                    ₱{{ number_format($pastOrder->total_amount, 2) }}
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-3">
                                                    <h6 class="mb-0">Date:</h6>
                                                </div>
                                                <div class="col-sm-9 text-secondary">
                                                    {{ $pastOrder->created_at->format('Y-m-d H:i:s') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="card-title mb-0">
                                                        <i class="fas fa-list mr-2 text-primary"></i>Order Items
                                                    </h5>
                                                </div>
                                                <div class="card-body">
                                                    @if($pastOrder->items && $pastOrder->items->count() > 0)
                                                    <div class="table-responsive">
                                                        <table class="table table-hover text-nowrap">
                                                            <thead class="bg-light">
                                                                <tr>
                                                                    <th>Product</th>
                                                                    <th>Quantity</th>
                                                                    <th>Price</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($pastOrder->items as $item)
                                                                <tr>
                                                                    <td>{{ $item->product->name ?? 'N/A' }}</td>
                                                                    <td>{{ $item->quantity }}</td>
                                                                    <td>₱{{ number_format($item->price, 2) }}</td>
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
                                                        <p class="text-muted">This past order has no associated items.</p>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <a href="{{ route('manager.past-orders.index') }}" class="btn btn-secondary">
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
    </div>


@endsection