<!DOCTYPE html>
<html>
<head>
    <title>Past Orders - Print</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 18px;
            margin-bottom: 10px;
        }
        .print-date {
            font-size: 12px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-section {
            margin-top: 20px;
            border-top: 2px solid #333;
            padding-top: 10px;
        }
        .total-row {
            font-weight: bold;
            font-size: 14px;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <div class="company-name">Sales & Inventory System</div>
        <div class="report-title">Past Orders Report</div>
        <div class="print-date">Generated on: {{ now()->format('F j, Y \a\t g:i A') }}</div>
    </div>

    @foreach($pastOrders as $pastOrder)
    <div style="margin-bottom: 30px; page-break-after: always;">
        <h3 style="border-bottom: 2px solid #333; padding-bottom: 5px;">Order #{{ $pastOrder->id }}</h3>
        
        <table style="margin-bottom: 15px;">
            <tr>
                <td style="width: 25%; font-weight: bold;">Brand:</td>
                <td style="width: 25%;">{{ $pastOrder->brand->name ?? 'N/A' }}</td>
                <td style="width: 25%; font-weight: bold;">Branch:</td>
                <td style="width: 25%;">{{ $pastOrder->branch->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Order Date:</td>
                <td>{{ $pastOrder->created_at->format('Y-m-d H:i:s') }}</td>
                <td style="font-weight: bold;">Total Amount:</td>
                <td style="font-weight: bold;">₱{{ number_format($pastOrder->total_amount, 2) }}</td>
            </tr>
        </table>

        <h4>Order Items:</h4>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 40%;">Product Name</th>
                    <th class="text-center" style="width: 15%;">Quantity</th>
                    <th class="text-right" style="width: 20%;">Unit Price</th>
                    <th class="text-right" style="width: 20%;">Total Price</th>
                </tr>
            </thead>
            <tbody>
                @php $itemNumber = 1; @endphp
                @foreach($pastOrder->items as $item)
                <tr>
                    <td>{{ $itemNumber++ }}</td>
                    <td>{{ $item->product->name ?? 'Unknown Product' }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">₱{{ number_format($item->price, 2) }}</td>
                    <td class="text-right">₱{{ number_format($item->quantity * $item->price, 2) }}</td>
                </tr>
                @endforeach
                
                @if($pastOrder->items->isEmpty())
                <tr>
                    <td colspan="5" class="text-center" style="font-style: italic;">No items found for this order</td>
                </tr>
                @endif
            </tbody>
            <tfoot>
                <tr style="border-top: 2px solid #333;">
                    <th colspan="2" class="text-right">Total Items:</th>
                    <th class="text-center">{{ $pastOrder->items->sum('quantity') }}</th>
                    <th class="text-right">Total Amount:</th>
                    <th class="text-right">₱{{ number_format($pastOrder->total_amount, 2) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
    @endforeach

    <div class="total-section" style="page-break-inside: avoid;">
        <h3>Summary Report</h3>
        <table>
            <tr class="total-row">
                <td class="text-right" style="width: 70%;">Total Orders:</td>
                <td class="text-center" style="width: 30%;">{{ $totalOrders }}</td>
            </tr>
            <tr class="total-row">
                <td class="text-right">Total Items across all orders:</td>
                <td class="text-center">{{ $pastOrders->sum(function($order) { return $order->items->sum('quantity'); }) }}</td>
            </tr>
            <tr class="total-row">
                <td class="text-right">Grand Total Amount:</td>
                <td class="text-center">₱{{ number_format($totalAmount, 2) }}</td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 40px; text-align: center; font-size: 12px; color: #666;">
        This is a computer-generated report. No signature required.
    </div>
</body>
</html>