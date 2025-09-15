<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PastOrdersSummaryReport implements WithMultipleSheets
{
    protected $pastOrders;
    protected $filterCriteria;

    public function __construct($pastOrders, $filterCriteria = null)
    {
        $this->pastOrders = $pastOrders;
        $this->filterCriteria = $filterCriteria ?? [];
    }

    public function sheets(): array
    {
        $sheets = [];

        // Get all unique brands from the orders
        $brands = $this->pastOrders->pluck('brand')->filter()->unique('id')->sortBy('name');

        // If no brands found, create a default sheet with all orders
        if ($brands->isEmpty()) {
            // Create a fallback sheet for orders without brands or empty result set
            $defaultBrand = (object) ['id' => null, 'name' => $this->pastOrders->isEmpty() ? 'No Data Found' : 'All Products'];
            $sheets[] = new BrandSummarySheet($this->pastOrders, $defaultBrand, $this->filterCriteria);
        } else {
            // Create a sheet for each brand
            foreach ($brands as $brand) {
                $brandOrders = $this->pastOrders->where('brand_id', $brand->id);
                if ($brandOrders->isNotEmpty()) {
                    $sheets[] = new BrandSummarySheet($brandOrders, $brand, $this->filterCriteria);
                }
            }
        }

        return $sheets;
    }
}

class BrandSummarySheet implements FromArray, WithHeadings, WithTitle
{
    protected $brandOrders;
    protected $brand;
    protected $filterCriteria;
    protected $branches;
    protected $products;
    protected $summaryData;

    public function __construct($brandOrders, $brand, $filterCriteria = null)
    {
        $this->brandOrders = $brandOrders;
        $this->brand = $brand;
        $this->filterCriteria = $filterCriteria ?? [];
        $this->prepareSummaryData();
    }

    protected function prepareSummaryData()
    {
        // Ensure we have a collection
        if (!$this->brandOrders) {
            $this->brandOrders = collect();
        }

        // Get all unique branches from the brand orders with null safety
        $this->branches = $this->brandOrders->map(function($order) {
            return $order ? $order->branch : null;
        })->filter()->unique('id')->sortBy('name')->values();
        
        // Get all unique products from the brand order items with null safety
        $productIds = $this->brandOrders->flatMap(function($order) {
            if (!$order || !$order->items) {
                return collect();
            }
            return $order->items->map(function($item) {
                return $item ? $item->product_id : null;
            })->filter();
        })->filter()->unique()->values();
        
        // Handle case where no products found
        if ($productIds->isEmpty()) {
            $this->products = collect();
        } else {
            $this->products = \App\Models\Product::whereIn('id', $productIds)->orderBy('name')->get();
        }

        // Create summary matrix: products vs branches for this brand
        $this->summaryData = [];
        
        foreach ($this->products as $product) {
            $row = ['product_name' => $product->name ?? 'Unknown Product'];
            $productTotal = 0;
            
            foreach ($this->branches as $branch) {
                // Calculate total quantity for this product in this branch for this brand
                $totalQuantity = $this->brandOrders
                    ->filter(function($order) use ($branch) {
                        return $order && $order->branch_id == $branch->id;
                    })
                    ->flatMap(function($order) {
                        return $order && $order->items ? $order->items : collect();
                    })
                    ->filter(function($item) use ($product) {
                        return $item && $item->product_id == $product->id;
                    })
                    ->sum('quantity');
                
                $quantity = $totalQuantity > 0 ? $totalQuantity : 0;
                $row['branch_' . $branch->id] = $quantity;
                $productTotal += $quantity;
            }
            
            // Add total for this product
            $row['product_total'] = $productTotal;
            
            $this->summaryData[] = $row;
        }
    }

    public function array(): array
    {
        $data = [];
        
        // Ensure branches is not null
        if (!$this->branches) {
            $this->branches = collect();
        }

        // If no data available, return minimal structure
        if ($this->branches->isEmpty() && empty($this->summaryData)) {
            $filterInfo = $this->getFilterInfoText();
            return [
                ['No orders found for the selected criteria'],
                [$filterInfo],
                ['Brand/Category: ' . ($this->brand->name ?? 'All Products')],
                ['Report Generated: ' . now()->format('Y-m-d H:i:s')],
                [''],
                ['Please try adjusting your filters or selection criteria.']
            ];
        }
        
        // Add filter info header in the first row (0,0 index)
        $filterInfoText = $this->getFilterInfoText();
        $headerRow = [$filterInfoText];
        
        // Fill the rest of the header row with empty values for alignment
        foreach ($this->branches as $branch) {
            $headerRow[] = '';
        }
        $headerRow[] = ''; // For Total column
        $data[] = $headerRow;
        
        // Add empty row for spacing
        $emptyRow = [''];
        foreach ($this->branches as $branch) {
            $emptyRow[] = '';
        }
        $emptyRow[] = ''; // For Total column
        $data[] = $emptyRow;
        
        // Add data rows
        if (!empty($this->summaryData)) {
            foreach ($this->summaryData as $row) {
                $dataRow = [$row['product_name'] ?? 'Unknown Product']; // Start with product name
                
                // Add quantities for each branch
                foreach ($this->branches as $branch) {
                    $dataRow[] = $row['branch_' . $branch->id] ?? 0;
                }
                
                // Add total for this product
                $dataRow[] = $row['product_total'] ?? 0;
                
                $data[] = $dataRow;
            }
        }
        
        // Add totals row
        if (!empty($this->summaryData) && !empty($this->branches)) {
            $totalsRow = ['TOTAL'];
            $grandTotal = 0;
            
            foreach ($this->branches as $branch) {
                $branchTotal = collect($this->summaryData)->sum(function($row) use ($branch) {
                    return $row['branch_' . $branch->id] ?? 0;
                });
                $totalsRow[] = $branchTotal;
                $grandTotal += $branchTotal;
            }
            
            // Add grand total
            $totalsRow[] = $grandTotal;
            
            $data[] = $totalsRow;
        } else {
            // Add empty totals row if no data
            $totalsRow = ['No Data Available'];
            foreach ($this->branches as $branch) {
                $totalsRow[] = 0;
            }
            $totalsRow[] = 0; // For total column
            $data[] = $totalsRow;
        }
        
        return $data;
    }

    public function headings(): array
    {
        // If no data available, return simple heading
        if ($this->branches->isEmpty() && empty($this->summaryData)) {
            return ['Information'];
        }

        $headings = ['Product Name'];
        
        // Add branch names as column headers
        foreach ($this->branches as $branch) {
            $headings[] = $branch->name ?? 'Unknown Branch';
        }
        
        // Add Total column
        $headings[] = 'Total';
        
        return $headings;
    }

    public function title(): string
    {
        return $this->brand->name ?? 'Unknown Brand';
    }

    protected function getFilterInfoText()
    {
        $info = [];
        
        // Add date range info
        $startDate = $this->filterCriteria['start_date'] ?? null;
        $endDate = $this->filterCriteria['end_date'] ?? null;
        
        if ($startDate || $endDate) {
            if ($startDate && $endDate) {
                $start = \Carbon\Carbon::parse($startDate)->format('M j, Y');
                $end = \Carbon\Carbon::parse($endDate)->format('M j, Y');
                if ($start === $end) {
                    $info[] = "Date: {$start}";
                } else {
                    $info[] = "Date Range: {$start} to {$end}";
                }
            } elseif ($startDate) {
                $start = \Carbon\Carbon::parse($startDate)->format('M j, Y');
                $info[] = "From: {$start}";
            } elseif ($endDate) {
                $end = \Carbon\Carbon::parse($endDate)->format('M j, Y');
                $info[] = "Up to: {$end}";
            }
        }
        
        // Add search info
        if (!empty($this->filterCriteria['search'])) {
            $info[] = "Search: '" . $this->filterCriteria['search'] . "'";
        }
        
        // Add selection info
        $selectedCount = $this->filterCriteria['selected_count'] ?? 0;
        $filteredCount = $this->filterCriteria['filtered_count'] ?? 0;
        
        if ($selectedCount > 0) {
            if ($filteredCount < $selectedCount) {
                $info[] = "Showing: {$filteredCount} of {$selectedCount} selected orders";
            } else {
                $info[] = "Orders: {$selectedCount} selected";
            }
        }
        
        return "Summary Report - " . (empty($info) ? "All Orders" : implode(', ', $info));
    }
}
