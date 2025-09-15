<table>
    <thead>
        <tr>
            <th colspan="6" style="text-align: center; font-size: 18px; font-weight: bold; background-color: #4472C4; color: white;">PAST ORDERS SUMMARY REPORT</th>
        </tr>
        <tr><td colspan="6"></td></tr>
        <tr>
            <th style="font-weight: bold; background-color: #E8F1FF;">Order ID</th>
            <th style="font-weight: bold; background-color: #E8F1FF;">Brand</th>
            <th style="font-weight: bold; background-color: #E8F1FF;">Branch</th>
            <th style="font-weight: bold; background-color: #E8F1FF;">Total Items</th>
            <th style="font-weight: bold; background-color: #E8F1FF;">Total Amount</th>
            <th style="font-weight: bold; background-color: #E8F1FF;">Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pastOrders as $pastOrder)
        <tr>
            <td>{{ $pastOrder->id }}</td>
            <td>{{ $pastOrder->brand->name ?? 'N/A' }}</td>
            <td>{{ $pastOrder->branch->name ?? 'N/A' }}</td>
            <td style="text-align: center;">{{ $pastOrder->items->sum('quantity') }}</td>
            <td style="text-align: right;">PHP {{ number_format($pastOrder->total_amount, 2) }}</td>
            <td>{{ $pastOrder->created_at->format('Y-m-d H:i:s') }}</td>
        </tr>
        @endforeach
        
        <tr><td colspan="6"></td></tr>
        <tr>
            <th colspan="3" style="text-align: right; font-weight: bold; background-color: #F0F0F0;">GRAND TOTAL ITEMS:</th>
            <th style="font-weight: bold; background-color: #F0F0F0; text-align: center;">{{ $totalItems }}</th>
            <th style="font-weight: bold; background-color: #F0F0F0; text-align: right;">PHP {{ number_format($totalAmount, 2) }}</th>
            <th style="background-color: #F0F0F0;"></th>
        </tr>
        <tr>
            <th colspan="3" style="text-align: right; font-weight: bold; background-color: #F0F0F0;">TOTAL ORDERS:</th>
            <th style="font-weight: bold; background-color: #F0F0F0; text-align: center;">{{ count($pastOrders) }}</th>
            <th colspan="2" style="background-color: #F0F0F0;"></th>
        </tr>
        <tr><td colspan="6"></td></tr>
        <tr>
            <td colspan="6" style="text-align: center; font-style: italic; font-size: 12px;">
                Generated on: {{ $exportDate }} | Sales & Inventory System
            </td>
        </tr>
        <tr>
            <td colspan="6" style="text-align: center; font-style: italic; font-size: 11px;">
                Note: Detailed information for each order is available in separate sheets
            </td>
        </tr>
    </tbody>
</table>