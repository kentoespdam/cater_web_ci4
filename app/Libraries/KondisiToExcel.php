<?php

namespace App\Libraries;

use CodeIgniter\HTTP\DownloadResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class KondisiToExcel
{
    private array $data;
    private string $fileName;
    private array $allBorder = [
        'bottom' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
        'top' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
        'left' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
        'right' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ];

    private array $styleHeader = [
        'font' => [
            'bold' => true,
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
        ],
    ];

    public function __construct(array $data, string $fileName)
    {
        $this->data = $data;
        $this->fileName = $fileName;
    }

    public function download(): DownloadResponse
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_');

        if ($tempFile === false) {
            throw new \RuntimeException('Failed to create temporary file.');
        }

        register_shutdown_function(function () use ($tempFile) {
            if (file_exists($tempFile)) {
                @unlink($tempFile);
            }
        });

        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Kondisi Water Meter');
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $this->setHeader($sheet);

        $rowsData = [];
        $urut = 1;

        foreach ($this->data as $row) {
            $rowsData[] = [
                $urut++,
                $row['periode'] ?? '',
                $row['cabang'] ?? '',
                $row['normal'] ?? 0,
                $row['mati'] ?? 0,
                $row['stand tunggu'] ?? 0,
                $row['stand mundur'] ?? 0,
                $row['ganti wm'] ?? 0,
                $row['pencurian air'] ?? 0,
                $row['tidak terjangkau'] ?? 0,
                $row['berembun'] ?? 0,
                $row['hilang'] ?? 0,
                $row['rusak'] ?? 0,
                $row['buram'] ?? 0,
                $row['terpendam'] ?? 0,
                $row['tempel angka'] ?? 0,
                $row['pagar kunci'] ?? 0,
                $row['segel putus'] ?? 0,
                $row['cabut meter'] ?? 0,
                $row['unknown'] ?? 0,
                $row['total'] ?? 0,
            ];
        }

        $totalRow = [
            'TOTAL',
            '',
            '',
            $this->reduceTotal($this->data, 'normal'),
            $this->reduceTotal($this->data, 'mati'),
            $this->reduceTotal($this->data, 'stand tunggu'),
            $this->reduceTotal($this->data, 'stand mundur'),
            $this->reduceTotal($this->data, 'ganti wm'),
            $this->reduceTotal($this->data, 'pencurian air'),
            $this->reduceTotal($this->data, 'tidak terjangkau'),
            $this->reduceTotal($this->data, 'berembun'),
            $this->reduceTotal($this->data, 'hilang'),
            $this->reduceTotal($this->data, 'rusak'),
            $this->reduceTotal($this->data, 'buram'),
            $this->reduceTotal($this->data, 'terpendam'),
            $this->reduceTotal($this->data, 'tempel angka'),
            $this->reduceTotal($this->data, 'pagar kunci'),
            $this->reduceTotal($this->data, 'segel putus'),
            $this->reduceTotal($this->data, 'cabut meter'),
            $this->reduceTotal($this->data, 'unknown'),
            $this->reduceTotal($this->data, 'total'),
        ];
        $rowsData[] = $totalRow;

        if (!empty($rowsData)) {
            $sheet->fromArray($rowsData, null, 'A2');
        }

        $lastDataRow = 1 + count($this->data);
        $footerRow = $lastDataRow + 1;

        $sheet->mergeCells("A{$footerRow}:C{$footerRow}", Worksheet::MERGE_CELL_CONTENT_MERGE);

        // Batch range styling
        $sheet->getStyle("A1:U{$footerRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        $sheet->getStyle("A1:U1")->applyFromArray($this->styleHeader);
        $sheet->getStyle("A{$footerRow}:C{$footerRow}")->applyFromArray($this->styleHeader);

        $greenStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => '00B050'],
            ],
        ];
        $sheet->getStyle("D2:D{$footerRow}")->applyFromArray($greenStyle);
        $sheet->getStyle("U2:U{$footerRow}")->applyFromArray($greenStyle);

        $columnWidths = [
            'A' => 6,
            'B' => 12,
            'C' => 20,
            'D' => 12,
            'E' => 12,
            'F' => 15,
            'G' => 15,
            'H' => 12,
            'I' => 15,
            'J' => 18,
            'K' => 12,
            'L' => 12,
            'M' => 12,
            'N' => 12,
            'O' => 12,
            'P' => 15,
            'Q' => 15,
            'R' => 15,
            'S' => 15,
            'T' => 18,
            'U' => 12,
        ];

        foreach ($columnWidths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);
        $writer->save($tempFile);

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return response()->download($tempFile, null)->setFileName($this->fileName . '.xlsx');
    }

    private function setHeader($sheet)
    {
        $headers = [
            "No", "Periode", "Cabang", "Normal", "Mati", "Stand Tunggu", "Stand Mundur",
            "Ganti WM", "Pencurian Air", "Tidak Terjangkau", "Berembun", "Hilang",
            "Rusak", "Buram", "Terpendam", "Tempel Angka", "Pagar Kunci",
            "Segel Putus", "Cabut Meter", "Tanpa Keterangan", "Total",
        ];

        $sheet->fromArray($headers, null, 'A1');
    }

    private function reduceTotal($data, $field)
    {
        return array_sum(array_column($data, $field));
    }
}
