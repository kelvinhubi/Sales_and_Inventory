<table>
    <thead>
        <tr>
            <th colspan="6" style="text-align: center; font-size: 18px; font-weight: bold; background-color: #4472C4; color: white;">PAST ORDER DETAILED REPORT</th>
        </tr>
        <tr><td colspan="6"></td></tr>
        <tr>
            <td style="font-weight: bold;">Order ID:</td>
            <td>{{ $pastOrder->id }}</td>
            <td style="font-weight: bold;">Date:</td>
            <td>{{ $pastOrder->created_at->format('Y-m-d H:i:s') }}</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Brand:</td>
            <td>{{ $pastOrder->brand->name ?? 'N/A' }}</td>
            <td style="font-weight: bold;">Branch:</td>
            <td>{{ $pastOrder->branch->name ?? 'N/A' }}</td>
            <td></td>
            <td></td>
        </tr>
        <tr><td colspan="6"></td></tr>
        <tr>
            <th style="font-weight: bold; background-color: #E8F1FF;">Item #</th>
            <th style="font-weight: bold; background-color: #E8F1FF;">Product Name</th>
            <th style="font-weight: bold; background-color: #E8F1FF;">Quantity</th>
            <th style="font-weight: bold; background-color: #E8F1FF;">Unit Price</th>
            <th style="font-weight: bold; background-color: #E8F1FF;">Total Price</th>
            <th style="font-weight: bold; background-color: #E8F1FF;">Notes</th>
        </tr>
    </thead>
    <tbody>
        @php $itemNumber = 1; @endphp
        @foreach($pastOrder->items as $item)
        <tr>
            <td>{{ $itemNumber++ }}</td>
            <td>{{ $item->product->name ?? 'Unknown Product' }}</td>
            <td style="text-align: center;">{{ $item->quantity }}</td>
            <td style="text-align: right;">PHP {{ number_format($item->price, 2) }}</td>
            <td style="text-align: right;">PHP {{ number_format($item->quantity * $item->price, 2) }}</td>
            <td>{{ $item->product->description ?? '' }}</td>
        </tr>
        @endforeach
        
        @if($pastOrder->items->isEmpty())
        <tr>
            <td colspan="6" style="text-align: center; font-style: italic;">No items found for this order</td>
        </tr>
        @endif
        
        <tr><td colspan="6"></td></tr>
        <tr>
            <th colspan="4" style="text-align: right; font-weight: bold; background-color: #F0F0F0;">TOTAL ITEMS:</th>
            <th style="font-weight: bold; background-color: #F0F0F0; text-align: center;">{{ $pastOrder->items->sum('quantity') }}</th>
            <th></th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: right; font-weight: bold; background-color: #F0F0F0;">TOTAL AMOUNT:</th>
            <th style="font-weight: bold; background-color: #F0F0F0; text-align: right;">PHP {{ number_format($pastOrder->total_amount, 2) }}</th>
            <th></th>
        </tr>
        <tr><td colspan="6"></td></tr>
        <tr>
            <td colspan="6" style="text-align: center; font-style: italic; font-size: 12px;">
                Generated on: {{ $exportDate }} | Sales & Inventory System
            </td>
        </tr>
    </tbody>
</table>