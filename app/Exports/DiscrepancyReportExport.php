<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DiscrepancyReportExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    private $data;
    private $startDate;
    private $endDate;

    public function __construct($data, $startDate = null, $endDate = null)
    {
        $this->data = $data;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function array(): array
    {
        return collect($this->data)->map(function ($item) {
            return [
                $item['date'],
                $item['store'],
                $item['dr_number'],
                $item['product_name'],
                $item['sales_quantity'],
                $item['sales_price'],
                number_format($item['amount'], 2),
                number_format($item['less'], 2),
                number_format($item['net_amount'], 2),
                $item['remarks']
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'DATE',
            'STORE', 
            'DR#',
            'PRODUCT',
            'QTY SOLD',
            'PRICE',
            'AMOUNT',
            'LESS',
            'NET',
            'REMARKS'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header row styling - now using J columns instead of K
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'DC3545'] // Red color like your example
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Data rows styling
        $lastRow = count($this->data) + 1;
        if ($lastRow > 1) {
            $sheet->getStyle("A2:J{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_TOP,
                    'wrapText' => true
                ]
            ]);

            // Center align date, DR#, quantity columns
            $sheet->getStyle("A2:C{$lastRow}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E2:E{$lastRow}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Right align currency columns
            $sheet->getStyle("F2:I{$lastRow}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // Highlight negative net amounts
            for ($row = 2; $row <= $lastRow; $row++) {
                $netValue = $sheet->getCell("I{$row}")->getCalculatedValue();
                if (is_numeric(str_replace(',', '', $netValue)) && floatval(str_replace(',', '', $netValue)) < 0) {
                    $sheet->getStyle("I{$row}")->applyFromArray([
                        'font' => [
                            'color' => ['rgb' => 'DC3545'],
                            'bold' => true
                        ]
                    ]);
                }
            }
        }

        // Add title
        $sheet->insertNewRowBefore(1, 2);
        
        $title = 'Sales vs Rejected Goods Discrepancy Report (Per Item)';
        if ($this->startDate || $this->endDate) {
            $period = '';
            if ($this->startDate && $this->endDate) {
                $period = " - Period: {$this->startDate} to {$this->endDate}";
            } elseif ($this->startDate) {
                $period = " - From: {$this->startDate}";
            } elseif ($this->endDate) {
                $period = " - Until: {$this->endDate}";
            }
            $title .= $period;
        }
        
        $sheet->setCellValue('A1', $title);
        
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
        
        $sheet->mergeCells('A1:J1');

        // Auto-fit row heights
        for ($row = 1; $row <= $lastRow + 1; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(-1);
        }

        return $sheet;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12, // DATE
            'B' => 20, // STORE
            'C' => 12, // DR#
            'D' => 30, // PRODUCT
            'E' => 10, // QTY SOLD
            'F' => 12, // PRICE
            'G' => 12, // AMOUNT
            'H' => 12, // LESS
            'I' => 12, // NET
            'J' => 40, // REMARKS
        ];
    }

    public function title(): string
    {
        return 'Discrepancy Report';
    }
}
