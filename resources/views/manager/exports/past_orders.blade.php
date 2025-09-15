<table>
    <thead>
        <tr>
            <th colspan="5" style="text-align: center; font-size: 16px; font-weight: bold;">PAST ORDERS EXPORT REPORT</th>
        </tr>
        <tr><td></td></tr>
        <tr>
            <th style="font-weight: bold;">Order ID</th>
            <th style="font-weight: bold;">Brand</th>
            <th style="font-weight: bold;">Branch</th>
            <th style="font-weight: bold;">Total Amount</th>
            <th style="font-weight: bold;">Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pastOrders as $pastOrder)
        <tr>
            <td>{{ $pastOrder->id }}</td>
            <td>{{ $pastOrder->brand->name ?? 'N/A' }}</td>
            <td>{{ $pastOrder->branch->name ?? 'N/A' }}</td>
            <td>{{ number_format($pastOrder->total_amount, 2) }}</td>
            <td>{{ $pastOrder->created_at->format('Y-m-d') }}</td>
        </tr>
        @endforeach
        <tr><td></td></tr>
        <tr>
            <th colspan="3" style="text-align: right; font-weight: bold;">TOTAL AMOUNT:</th>
            <th style="font-weight: bold;">₱{{ number_format($totalAmount, 2) }}</th>
            <th></th>
        </tr>
        <tr>
            <th colspan="3" style="text-align: right; font-weight: bold;">TOTAL ORDERS:</th>
            <th style="font-weight: bold;">{{ count($pastOrders) }}</th>
            <th></th>
        </tr>
        <tr><td></td></tr>
        <tr>
            <td colspan="5" style="text-align: center; font-style: italic;">
                Generated on: {{ $exportDate }}
            </td>
        </tr>
    </tbody>
</table>