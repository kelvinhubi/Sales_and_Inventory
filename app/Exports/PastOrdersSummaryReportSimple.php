<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PastOrdersSummaryReportSimple implements FromArray, WithHeadings
{
    protected $pastOrders;
    protected $dateRange;

    public function __construct($pastOrders, $dateRange = null)
    {
        $this->pastOrders = $pastOrders;
        $this->dateRange = $dateRange;
    }

    public function array(): array
    {
        // Check if this is for an empty report
        if ($this->pastOrders->isEmpty()) {
            $startDate = $this->dateRange['start'] ?? 'Not specified';
            $endDate = $this->dateRange['end'] ?? 'Not specified';
            
            return [
                ['No orders found for the selected criteria'],
                ['Start Date: ' . $startDate],
                ['End Date: ' . $endDate],
                ['Generated: ' . now()->format('Y-m-d H:i:s')],
                [''],
                ['Please adjust your date range or selection criteria.']
            ];
        }
        
        // Simple test data for non-empty case
        return [
            ['Test Product 1', 'Test Branch 1', 10, 100.00],
            ['Test Product 2', 'Test Branch 2', 5, 50.00],
        ];
    }

    public function headings(): array
    {
        // Check if this is for an empty report
        if ($this->pastOrders->isEmpty()) {
            return ['Information'];
        }
        
        return ['Product Name', 'Branch', 'Quantity', 'Total'];
    }
}