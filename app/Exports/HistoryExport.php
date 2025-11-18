<?php

namespace App\Exports;

use App\Models\Laporan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class HistoryExport implements FromCollection, WithHeadings, WithStyles
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        // Check if $query is already a collection or a query builder
        if ($this->query instanceof \Illuminate\Database\Eloquent\Collection) {
            $laporan = $this->query;
        } else {
            $laporan = $this->query->get();
        }

        return $laporan->map(function ($item, $index) {
            try {
                $reportDate = $item->created_at ? Carbon::parse($item->created_at)->format('l, j-n-Y') : '-';
                $completionDate = $item->penyelesaian && $item->penyelesaian->Tanggal 
                    ? Carbon::parse($item->penyelesaian->Tanggal)->format('l, j-n-Y') 
                    : '-';
            } catch (\Exception $e) {
                $reportDate = $item->created_at ?? '-';
                $completionDate = '-';
            }

            $observation = $item->deskripsi_masalah ?? '-';
            $resolution = $item->penyelesaian ? ($item->penyelesaian->deskripsi_penyelesaian ?? '-') : '-';

            return [
                $index + 1, // No
                $reportDate, // Report Date
                $completionDate, // Completion Date
                $this->getAreaStation($item), // Area/Station
                $item->problemCategory?->name ?? '-', // Category
                $observation, // Observation
                $resolution, // Resolution
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Report Date',
            'Completion Date',
            'Area/Station',
            'Category',
            'Observation',
            'Resolution',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0D6EFD'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Style data rows (only if there are data rows)
        $highestRow = $sheet->getHighestRow();
        if ($highestRow > 1) {
            $sheet->getStyle('A2:G' . $highestRow)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_TOP,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ]);
        }

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(35);
        $sheet->getColumnDimension('G')->setWidth(35);

        // Set row height for header
        $sheet->getRowDimension(1)->setRowHeight(30);

        return [];
    }

    private function getAreaStation($item)
    {
        if ($item->area) {
            $areaName = $item->area->name;
            if ($item->penanggungJawab) {
                $stationOrPic = trim((string)($item->penanggungJawab->station ?? ''));
                if ($stationOrPic === '') {
                    $stationOrPic = $item->penanggungJawab->name ?? '';
                }
                return $stationOrPic !== '' ? $areaName . ' (' . $stationOrPic . ')' : $areaName;
            }
            return $areaName;
        }
        return '-';
    }
}
