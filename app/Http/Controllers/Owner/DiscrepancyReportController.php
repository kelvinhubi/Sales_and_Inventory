<?php

namespace App\Http\Controllers\Owner;

use App\Exports\DiscrepancyReportExport;
use App\Http\Controllers\Controller;
use App\Models\PastOrder;
use App\Models\RejectedGood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class DiscrepancyReportController extends Controller
{
    public function index()
    {
        if (! Auth::check()) {
            return redirect()->route('Login');
        }

        // Get all DR numbers that exist in either past orders or rejected goods
        $drNumbers = collect();

        // Get DR numbers from past orders
        $pastOrderDRs = PastOrder::whereNotNull('dr_number')
            ->pluck('dr_number')
            ->unique();

        // Get DR numbers from rejected goods
        $rejectedGoodsDRs = RejectedGood::whereNotNull('dr_no')
            ->pluck('dr_no')
            ->unique();

        // Combine and sort
        $drNumbers = $pastOrderDRs->merge($rejectedGoodsDRs)
            ->unique()
            ->sort()
            ->values();

        return view('owner.discrepancy-report.index', compact('drNumbers'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'dr_numbers' => 'nullable|array',
            'dr_numbers.*' => 'string',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $selectedDRs = $request->dr_numbers;

        // Build query for past orders
        $pastOrdersQuery = PastOrder::with(['brand', 'branch', 'items.product'])
            ->whereNotNull('dr_number');

        if ($startDate) {
            $pastOrdersQuery->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $pastOrdersQuery->whereDate('created_at', '<=', $endDate);
        }
        if ($selectedDRs && count($selectedDRs) > 0) {
            $pastOrdersQuery->whereIn('dr_number', $selectedDRs);
        }

        $pastOrders = $pastOrdersQuery->get();

        // Build query for rejected goods
        $rejectedGoodsQuery = RejectedGood::with(['brand', 'branch', 'items.product'])
            ->whereNotNull('dr_no');

        if ($startDate) {
            $rejectedGoodsQuery->whereDate('date', '>=', $startDate);
        }
        if ($endDate) {
            $rejectedGoodsQuery->whereDate('date', '<=', $endDate);
        }
        if ($selectedDRs && count($selectedDRs) > 0) {
            $rejectedGoodsQuery->whereIn('dr_no', $selectedDRs);
        }

        $rejectedGoods = $rejectedGoodsQuery->get();

        // Prepare data for export
        $reportData = $this->prepareDiscrepancyData($pastOrders, $rejectedGoods);

        return Excel::download(
            new DiscrepancyReportExport($reportData, $startDate, $endDate),
            'discrepancy-report-' . date('Y-m-d-H-i-s') . '.xlsx'
        );
    }

    private function prepareDiscrepancyData($pastOrders, $rejectedGoods)
    {
        $data = [];

        // Debug logging
        \Log::info('Discrepancy Report Debug', [
            'past_orders_count' => $pastOrders->count(),
            'rejected_goods_count' => $rejectedGoods->count(),
            'sample_past_order_dr' => $pastOrders->first() ? $pastOrders->first()->dr_number : 'None',
            'sample_rejected_good_dr' => $rejectedGoods->first() ? $rejectedGoods->first()->dr_no : 'None',
        ]);

        // Group past orders by DR number
        $pastOrdersByDR = $pastOrders->groupBy('dr_number');

        // Group rejected goods by DR number
        $rejectedGoodsByDR = $rejectedGoods->groupBy('dr_no');

        // Get all unique DR numbers
        $allDRs = collect($pastOrdersByDR->keys())
            ->merge($rejectedGoodsByDR->keys())
            ->unique()
            ->sort();

        \Log::info('DR Numbers Found', [
            'past_orders_drs' => $pastOrdersByDR->keys()->toArray(),
            'rejected_goods_drs' => $rejectedGoodsByDR->keys()->toArray(),
            'all_unique_drs' => $allDRs->toArray(),
        ]);

        foreach ($allDRs as $drNumber) {
            $pastOrder = $pastOrdersByDR->get($drNumber)->first();
            $rejectedGoodsItems = $rejectedGoodsByDR->get($drNumber, collect());

            if ($pastOrder && $pastOrder->items) {
                // Create a map of rejected items by product ID for quick lookup
                $rejectedItemsMap = [];
                foreach ($rejectedGoodsItems as $rejectedGood) {
                    if ($rejectedGood->items && $rejectedGood->items->count() > 0) {
                        // Has individual items breakdown
                        foreach ($rejectedGood->items as $rejectedItem) {
                            $productId = $rejectedItem->product_id;
                            $productName = $rejectedItem->product->name ?? 'Unknown Product';

                            if (! isset($rejectedItemsMap[$productId])) {
                                $rejectedItemsMap[$productId] = [
                                    'product_name' => $productName,
                                    'quantity' => 0,
                                    'amount' => 0,
                                    'reasons' => [],
                                ];
                            }

                            // Get the price from the original sales order item for this product
                            $orderItem = $pastOrder->items->where('product_id', $productId)->first();
                            $itemPrice = $orderItem ? $orderItem->price : 0;

                            $rejectedItemsMap[$productId]['quantity'] += $rejectedItem->quantity;
                            $rejectedItemsMap[$productId]['amount'] += $rejectedItem->quantity * $itemPrice;

                            // Collect reasons, including empty ones
                            if ($rejectedGood->reason && trim($rejectedGood->reason) !== '') {
                                $rejectedItemsMap[$productId]['reasons'][] = trim($rejectedGood->reason);
                            }
                        }
                    } else {
                        // No items breakdown, use the main rejected good amount
                        // This is a fallback for older rejected goods without item breakdown
                        if ($rejectedGood->amount > 0) {
                            // We need to distribute this amount across products somehow
                            // For now, let's note it separately
                            $rejectedItemsMap['no_breakdown'] = [
                                'product_name' => 'Multiple Products',
                                'quantity' => 1,
                                'amount' => $rejectedGood->amount,
                                'reasons' => $rejectedGood->reason && trim($rejectedGood->reason) !== '' ? [trim($rejectedGood->reason)] : [],
                            ];
                        }
                    }
                }

                // Process each item in the past order
                foreach ($pastOrder->items as $orderItem) {
                    $productId = $orderItem->product_id;
                    $productName = $orderItem->product->name ?? 'Unknown Product';
                    $salesAmount = $orderItem->quantity * $orderItem->price;

                    // Get rejected info for this specific product ID
                    $rejectedInfo = $rejectedItemsMap[$productId] ?? ['product_name' => $productName, 'quantity' => 0, 'amount' => 0, 'reasons' => []];
                    $rejectedAmount = $rejectedInfo['amount'];
                    $netAmount = $salesAmount - $rejectedAmount;

                    // Build remarks - only show if there are actual reasons
                    $remarks = '';
                    if ($rejectedInfo['quantity'] > 0 && ! empty($rejectedInfo['reasons'])) {
                        $reasons = array_unique(array_filter($rejectedInfo['reasons'])); // Remove empty values
                        $remarks = ! empty($reasons) ? implode(', ', $reasons) : '';
                    }

                    $data[] = [
                        'date' => $pastOrder->created_at->format('m-d-y'),
                        'store' => $pastOrder->branch->name ?? 'N/A',
                        'dr_number' => $drNumber,
                        'product_name' => $productName,
                        'sales_quantity' => $orderItem->quantity,
                        'sales_price' => number_format($orderItem->price, 2),
                        'amount' => $salesAmount,
                        'less' => $rejectedAmount,
                        'net_amount' => $netAmount,
                        'remarks' => $remarks,
                    ];
                }

                // Handle rejected goods without corresponding sales (if any)
                foreach ($rejectedItemsMap as $productId => $rejectedInfo) {
                    // Skip if this product was already processed above
                    $alreadyProcessed = $pastOrder->items->where('product_id', $productId)->count() > 0;
                    if (! $alreadyProcessed && $productId !== 'no_breakdown') {
                        $data[] = [
                            'date' => $pastOrder->created_at->format('m-d-y'),
                            'store' => $pastOrder->branch->name ?? 'N/A',
                            'dr_number' => $drNumber,
                            'product_name' => $rejectedInfo['product_name'],
                            'sales_quantity' => 0,
                            'sales_price' => '0.00',
                            'amount' => 0,
                            'less' => $rejectedInfo['amount'],
                            'net_amount' => -$rejectedInfo['amount'],
                            'remarks' => implode(', ', $rejectedInfo['reasons']),
                        ];
                    }
                }
            } else {
                // Handle cases where there are rejected goods but no past order
                \Log::info('Processing rejected goods without past order for DR: ' . $drNumber);

                foreach ($rejectedGoodsItems as $rejectedGood) {
                    if ($rejectedGood->items && $rejectedGood->items->count() > 0) {
                        foreach ($rejectedGood->items as $rejectedItem) {
                            $productName = $rejectedItem->product->name ?? 'Unknown Product';
                            // Since there's no past order, we can't get the original price
                            // We'll use the product's current price as an estimate
                            $productPrice = $rejectedItem->product->price ?? 0;
                            $rejectedAmount = $rejectedItem->quantity * $productPrice;
                            $remarks = $rejectedGood->reason && trim($rejectedGood->reason) !== '' ? trim($rejectedGood->reason) : '';

                            $data[] = [
                                'date' => $rejectedGood->date ? date('m-d-y', strtotime($rejectedGood->date)) : 'N/A',
                                'store' => $rejectedGood->branch->name ?? 'N/A',
                                'dr_number' => $drNumber,
                                'product_name' => $productName,
                                'sales_quantity' => 0,
                                'sales_price' => number_format($productPrice, 2),
                                'amount' => 0,
                                'less' => $rejectedAmount,
                                'net_amount' => -$rejectedAmount,
                                'remarks' => $remarks,
                            ];
                        }
                    } else {
                        // Fallback for rejected goods without item breakdown
                        $data[] = [
                            'date' => $rejectedGood->date ? date('m-d-y', strtotime($rejectedGood->date)) : 'N/A',
                            'store' => $rejectedGood->branch->name ?? 'N/A',
                            'dr_number' => $drNumber,
                            'product_name' => 'Multiple Products',
                            'sales_quantity' => 0,
                            'sales_price' => '0.00',
                            'amount' => 0,
                            'less' => $rejectedGood->amount ?? 0,
                            'net_amount' => -($rejectedGood->amount ?? 0),
                            'remarks' => $rejectedGood->reason && trim($rejectedGood->reason) !== '' ? trim($rejectedGood->reason) : '',
                        ];
                    }
                }
            }
        }

        // Sort by DR number and product name
        return collect($data)->sortBy(['dr_number', 'product_name'])->values()->all();
    }
}
