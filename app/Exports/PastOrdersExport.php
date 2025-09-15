<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PastOrdersExport implements WithMultipleSheets
{
    protected $pastOrders;
    protected $filterCriteria;

    public function __construct($pastOrders, $filterCriteria = null)
    {
        $this->pastOrders = $pastOrders;
        $this->filterCriteria = $filterCriteria;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Add filter info sheet first (if filter criteria provided)
        // Temporarily disabled for testing
        // if ($this->filterCriteria) {
        //     $sheets[] = new FilterInfoSheet($this->filterCriteria, $this->pastOrders);
        // }

        // Add individual order sheets
        foreach ($this->pastOrders as $pastOrder) {
            $sheets[] = new PastOrderDetailSheet($pastOrder);
        }

        return $sheets;
    }
}

class PastOrderSummarySheet implements FromArray, WithTitle, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $pastOrders;

    public function __construct($pastOrders)
    {
        $this->pastOrders = $pastOrders;
    }

    public function array(): array
    {
        $data = [];
        
        // Add title row
        $data[] = ['PAST ORDERS SUMMARY REPORT', '', '', '', '', ''];
        $data[] = ['', '', '', '', '', '']; // Empty row
        
        // Add data rows that match the headings
        foreach ($this->pastOrders as $pastOrder) {
            $data[] = [
                $pastOrder->id,
                $pastOrder->brand->name ?? 'N/A',
                $pastOrder->branch->name ?? 'N/A',
                $pastOrder->items->sum('quantity'),
                'PHP ' . number_format($pastOrder->total_amount, 2),
                $pastOrder->created_at->format('Y-m-d H:i:s')
            ];
        }
        
        // Add totals
        $data[] = ['', '', '', '', '', '']; // Empty row
        $data[] = [
            '', '', 'GRAND TOTAL ITEMS:',
            $this->pastOrders->sum(function($order) { 
                return $order->items->sum('quantity'); 
            }),
            'PHP ' . number_format($this->pastOrders->sum('total_amount'), 2),
            ''
        ];
        $data[] = [
            '', '', 'TOTAL ORDERS:',
            $this->pastOrders->count(),
            '', ''
        ];
        
        return $data;
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'Brand',
            'Branch', 
            'Total Items',
            'Total Amount',
            'Date'
        ];
    }

    public function title(): string
    {
        return 'Summary';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 16],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ],
            3 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E8F1FF'],
                ],
            ],
        ];
    }
}

class PastOrderDetailSheet implements FromArray, WithTitle, WithColumnWidths, WithStyles, WithEvents
{
    protected $pastOrder;

    public function __construct($pastOrder)
    {
        $this->pastOrder = $pastOrder;
    }

    public function array(): array
    {
        $data = [];
        
        // Row 1: Empty
        $data[] = ['', '', '', '', '', ''];
        
        // Row 2: Empty  
        $data[] = ['', '', '', '', '', ''];
        
        // Row 3: Date (column D) - aligned with price column
        $data[] = ['', '', '', $this->pastOrder->created_at->format('M. d, Y'), '', ''];
        
        // Row 4: Brand Name (column B)
        $data[] = ['', $this->pastOrder->brand->name ?? 'N/A', '', '', '', ''];
        
        // Row 5: Branch Name (column B) - removed "BRANCH" word
        $data[] = ['', $this->pastOrder->branch->name ?? 'N/A', '', '', '', ''];
        
        // Row 6: Empty
        $data[] = ['', '', '', '', '', ''];
        
        // Row 7: Empty (space before items)
        $data[] = ['', '', '', '', '', ''];
        
        // Add items starting from row 8 - NO HEADERS, just data
        foreach ($this->pastOrder->items as $item) {
            $data[] = [
                '', // Column A: empty
                $item->product->name ?? 'Unknown Product', // Column B: Product name
                $item->quantity, // Column C: Quantity  
                $item->price, // Column D: Price (no formatting)
                '', // Column E: empty
                '' // Column F: empty
            ];
        }
        
        // Total right after items - no empty rows above
        $totalAmount = $this->pastOrder->items->sum(function($item) {
            return $item->quantity * $item->price;
        });
        $data[] = ['', '', '', $totalAmount, '', ''];
        
        return $data;
    }

    public function title(): string
    {
        return 'DR FORMULA CK';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 9.89,   // First empty column
            'B' => 16.78,  // Product name column
            'C' => 10.44,  // Quantity column
            'D' => 10.44,  // Price column
            'E' => 10.44,  // Additional column
            'F' => 10.44,  // Additional column
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Apply Century Gothic font size 9 to all cells
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Century Gothic');
        $sheet->getParent()->getDefaultStyle()->getFont()->setSize(11);
        
        // Center align all text
        $sheet->getParent()->getDefaultStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getParent()->getDefaultStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        
        // Set column widths manually (backup method)
        $sheet->getColumnDimension('A')->setWidth(9.89);
        $sheet->getColumnDimension('B')->setWidth(16.78);
        $sheet->getColumnDimension('C')->setWidth(10.44);
        $sheet->getColumnDimension('D')->setWidth(10.44);
        $sheet->getColumnDimension('E')->setWidth(10.44);
        $sheet->getColumnDimension('F')->setWidth(10.44);
        
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Force set column widths after sheet is created
                $sheet = $event->sheet->getDelegate();
                
                $sheet->getColumnDimension('A')->setWidth(9.89);
                $sheet->getColumnDimension('B')->setWidth(16.78);  
                $sheet->getColumnDimension('C')->setWidth(10.44);
                $sheet->getColumnDimension('D')->setWidth(10.44);
                $sheet->getColumnDimension('E')->setWidth(10.44);
                $sheet->getColumnDimension('F')->setWidth(10.44);
                
                // Also set font and alignment for all cells with data
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                
                $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
                    'font' => [
                        'name' => 'Century Gothic',
                        'size' => 11,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);
            },
        ];
    }
}

class FilterInfoSheet implements FromArray, WithTitle, WithStyles, ShouldAutoSize
{
    protected $filterCriteria;
    protected $pastOrders;

    public function __construct($filterCriteria, $pastOrders)
    {
        $this->filterCriteria = $filterCriteria;
        $this->pastOrders = $pastOrders;
    }

    public function array(): array
    {
        $data = [];
        
        // Header
        $data[] = ['EXPORT INFORMATION', '', ''];
        $data[] = ['', '', '']; // Empty row
        $data[] = ['Export Date:', now()->format('Y-m-d H:i:s'), ''];
        $data[] = ['', '', '']; // Empty row
        
        // Filter Information
        $data[] = ['APPLIED FILTERS', '', ''];
        $data[] = ['', '', '']; // Empty row
        
        if (!empty($this->filterCriteria['search'])) {
            $data[] = ['Search Term:', $this->filterCriteria['search'], ''];
        }
        
        if (!empty($this->filterCriteria['start_date'])) {
            $data[] = ['Start Date:', $this->filterCriteria['start_date'], ''];
        }
        
        if (!empty($this->filterCriteria['end_date'])) {
            $data[] = ['End Date:', $this->filterCriteria['end_date'], ''];
        }
        
        if (!empty($this->filterCriteria['branch_search'])) {
            $data[] = ['Branch Filter:', $this->filterCriteria['branch_search'], ''];
        }
        
        if (!empty($this->filterCriteria['brand_search'])) {
            $data[] = ['Brand Filter:', $this->filterCriteria['brand_search'], ''];
        }
        
        // If no filters applied
        $hasFilters = !empty($this->filterCriteria['search']) || 
                     !empty($this->filterCriteria['start_date']) || 
                     !empty($this->filterCriteria['end_date']) || 
                     !empty($this->filterCriteria['branch_search']) || 
                     !empty($this->filterCriteria['brand_search']);
        
        if (!$hasFilters) {
            $data[] = ['No filters applied', '', ''];
        }
        
        $data[] = ['', '', '']; // Empty row
        
        // Selection Information
        $data[] = ['SELECTION INFORMATION', '', ''];
        $data[] = ['', '', '']; // Empty row
        $data[] = ['Originally Selected Orders:', $this->filterCriteria['selected_count'] ?? 0, ''];
        $data[] = ['Orders Matching Filters:', $this->filterCriteria['found_count'] ?? 0, ''];
        $data[] = ['Orders in Export:', $this->pastOrders->count(), ''];
        
        $data[] = ['', '', '']; // Empty row
        
        // Summary
        $totalAmount = $this->pastOrders->sum('total_amount');
        $totalItems = $this->pastOrders->sum(function($order) {
            return $order->items->sum('quantity');
        });
        
        $data[] = ['EXPORT SUMMARY', '', ''];
        $data[] = ['', '', '']; // Empty row
        $data[] = ['Total Orders:', $this->pastOrders->count(), ''];
        $data[] = ['Total Items:', $totalItems, ''];
        $data[] = ['Total Amount:', 'PHP ' . number_format($totalAmount, 2), ''];
        
        return $data;
    }

    public function title(): string
    {
        return 'Export Info';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D4EDDA'],
                ],
            ],
            5 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E3F2FD'],
                ],
            ],
            // Style for section headers (will need to adjust row numbers dynamically)
            'A:A' => [
                'font' => ['bold' => true],
            ],
        ];
    }
}