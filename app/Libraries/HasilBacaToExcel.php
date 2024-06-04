<?php

namespace App\Libraries;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class HasilBacaToExcel
{
    private array $data;
    private string $fileName;
    private array $allBorder = [
        'bottom' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        ], 'top' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        ],
        'left' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        ],
        'right' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        ],
    ];

    private array $styleHeader = [
        'font' => [
            'bold' => true,
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        ]
    ];

    public function __construct(array $data, string $fileName)
    {
        $this->data = $data;
        $this->fileName = $fileName;
    }

    public function download()
    {
        ini_set('memory_limit', "4096M");
        ini_set('default_charset', '');
        mb_http_output('pass');
        mb_detect_order(["UTF-8"]);
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Hasil Baca Meter');
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $this->setHeader($sheet);

        $rowNum = 2;
        foreach ($this->data as $row) {
            $this->setCellValue($sheet, $row, $rowNum);
            $rowNum++;
        }

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $this->fileName . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    private function setHeader($sheet)
    {
        $this->styleHeader['borders'] = $this->allBorder;
        $fields = [
            ["col" => "A", "value" => "tgl"],
            ["col" => "B", "value" => "no_sam"],
            ["col" => "C", "value" => "stan_kini"],
            ["col" => "D", "value" => "stan_lalu"],
            ["col" => "E", "value" => "pakai"],
            ["col" => "F", "value" => "petugas"],
            ["col" => "G", "value" => "kondisi"],
            ["col" => "H", "value" => "ket"],
            ["col" => "I", "value" => "info"],
            ["col" => "J", "value" => "ptgs_met"],
            ["col" => "K", "value" => "rata"],
            ["col" => "L", "value" => "kd_wil"],
            ["col" => "M", "value" => "wil"],
            ["col" => "N", "value" => "kd_cabang"],
            ["col" => "O", "value" => "nm_cabang"],
            ["col" => "P", "value" => "kd_cek"],
            ["col" => "Q", "value" => "status_cek"],
            ["col" => "R", "value" => "nama"],
            ["col" => "S", "value" => "alamat"],
        ];

        foreach ($fields as $field) {
            $f = (object)$field;
            $sheet->setCellValue($f->col . "1", $f->value)
                ->getStyle($f->col . "1")
                ->applyFromArray($this->styleHeader);
        }
    }

    private function setCellValue($sheet, $row, $rowNum)
    {
        $sheet->setCellValue("A" . $rowNum, $row->tgl);
        $sheet->setCellValue("B" . $rowNum, $row->no_sam);
        $sheet->setCellValue("C" . $rowNum, $row->stan_kini);
        $sheet->setCellValue("D" . $rowNum, $row->stan_lalu);
        $sheet->setCellValue("E" . $rowNum, $row->pakai);
        $sheet->setCellValue("F" . $rowNum, $row->petugas);
        $sheet->setCellValue("G" . $rowNum, $row->kondisi);
        $sheet->setCellValue("H" . $rowNum, $row->ket);
        $sheet->setCellValue("I" . $rowNum, $row->info);
        $sheet->setCellValue("J" . $rowNum, $row->ptgs_met);
        $sheet->setCellValue("K" . $rowNum, $row->rata);
        $sheet->setCellValue("L" . $rowNum, $row->kd_wil);
        $sheet->setCellValue("M" . $rowNum, $row->wil);
        $sheet->setCellValue("N" . $rowNum, $row->kd_cabang);
        $sheet->setCellValue("O" . $rowNum, $row->nm_cabang);
        $sheet->setCellValue("P" . $rowNum, $row->kd_cek);
        $sheet->setCellValue("Q" . $rowNum, $row->status_cek);
        $sheet->setCellValue("R" . $rowNum, $row->nama);
        $sheet->setCellValue("S" . $rowNum, $row->alamat);
        $rowNum++;
    }
}
