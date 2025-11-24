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
use App\Models\PenanggungJawab;

class ReportExport implements FromCollection, WithHeadings, WithStyles
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
            } catch (\Exception $e) {
                $reportDate = $item->created_at ?? '-';
            }

            $observation = $item->deskripsi_masalah ?? '-';
            $personInCharge = $this->getPersonInCharge($item);
            $deadline = $item->tenggat_waktu ? \Carbon\Carbon::parse($item->tenggat_waktu)->format('l, j-n-Y') : '-';

            return [
                $index + 1, // No
                $reportDate, // Report Date
                $deadline, // Deadline
                $this->getAreaStation($item), // Area/Station
                $item->problemCategory?->name ?? '-', // Category
                $personInCharge, // Person in Charge
                $observation, // Observation
                $item->status ?? '-', // Status
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Report Date',
            'Deadline',
            'Area/Station',
            'Category',
            'Person in Charge',
            'Observation',
            'Status',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:H1')->applyFromArray([
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
            $sheet->getStyle('A2:H' . $highestRow)->applyFromArray([
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
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(35);
        $sheet->getColumnDimension('H')->setWidth(15);

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

    private function getPersonInCharge($item)
    {
        $pics = [];

        // Jika ada penanggungJawab, tambahkan ke list
        if ($item->penanggungJawab) {
            $pics[] = $item->penanggungJawab->name;
        } else {
            // Jika tidak ada penanggungJawab, ambil semua PIC dari area (kecuali station General)
            if ($item->area) {
                $areaPics = PenanggungJawab::where('area_id', $item->area_id)
                    ->where('station', '!=', 'General')
                    ->get();
                foreach ($areaPics as $pic) {
                    $pics[] = $pic->name;
                }
            }
        }

        // Tambahkan additional PICs
        if (!empty($item->additional_pic_objects) && count($item->additional_pic_objects) > 0) {
            foreach ($item->additional_pic_objects as $pic) {
                $pics[] = $pic->name;
            }
        }

        return !empty($pics) ? implode(', ', $pics) : '-';
    }
}
