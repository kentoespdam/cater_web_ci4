<?php

namespace App\Libraries;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class KondisiToExcel
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
        ],
    ];

    public function __construct(array $data, string $fileName)
    {
        $this->data = $data;
        $this->fileName = $fileName;
    }

    public function download()
    {
        ini_set('memory_limit', "1024M");
        ini_set('default_charset', '');
        mb_http_output('pass');
        mb_detect_order(["UTF-8"]);
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Kondisi Water Meter');
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $this->setHeader($sheet);

        $urut = 1;
        $rowNum = 2;

        foreach ($this->data as $row) {
            $this->setCellValue($sheet, $row, $rowNum, $urut);
            $rowNum++;
            $urut++;
        }

        $this->setFooter($sheet, $this->data, $rowNum);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $this->fileName . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    private function setHeader($sheet)
    {
        $this->styleHeader['borders'] = $this->allBorder;

        $sheet->setCellValue("A1", "No")->getStyle("A1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("B1", "Periode")->getStyle("B1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("C1", "Cabang")->getStyle("C1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("D1", "Normal")->getStyle("D1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("E1", "Mati")->getStyle("E1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("F1", "Stand Tunggu")->getStyle("F1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("G1", "Stand Mundur")->getStyle("G1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("H1", "Ganti WM")->getStyle("H1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("I1", "Pencurian Air")->getStyle("I1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("J1", "Tidak Terjangkau")->getStyle("J1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("K1", "Berembun")->getStyle("K1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("L1", "Hilang")->getStyle("L1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("M1", "Rusak")->getStyle("M1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("N1", "Buram")->getStyle("N1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("O1", "Terpendam")->getStyle("O1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("P1", "Tempel Angka")->getStyle("P1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("Q1", "Pagar Kunci")->getStyle("Q1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("R1", "Segel Putus")->getStyle("R1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("S1", "Cabut Meter")->getStyle("S1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("T1", "Tanpa Keterangan")->getStyle("T1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("U1", "Total")->getStyle("U1")->applyFromArray($this->styleHeader);
    }

    private function setCellValue($sheet, $row, $rowNum, $urut)
    {
        $sheet->setCellValue("A" . $rowNum, $urut)->getStyle('A' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("B" . $rowNum, $row['periode'] ?? '')->getStyle('B' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("C" . $rowNum, $row['cabang'] ?? '')->getStyle('C' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);

        $sheet->setCellValue("D" . $rowNum, $row['normal'] ?? 0)
            ->getStyle('D' . $rowNum)->applyFromArray(['borders' => $this->allBorder])->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('00B050');

        $sheet->setCellValue("E" . $rowNum, $row['mati'] ?? 0)->getStyle('E' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("F" . $rowNum, $row['stand tunggu'] ?? 0)->getStyle('F' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("G" . $rowNum, $row['stand mundur'] ?? 0)->getStyle('G' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("H" . $rowNum, $row['ganti wm'] ?? 0)->getStyle('H' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("I" . $rowNum, $row['pencurian air'] ?? 0)->getStyle('I' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("J" . $rowNum, $row['tidak terjangkau'] ?? 0)->getStyle('J' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("K" . $rowNum, $row['berembun'] ?? 0)->getStyle('K' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("L" . $rowNum, $row['hilang'] ?? 0)->getStyle('L' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("M" . $rowNum, $row['rusak'] ?? 0)->getStyle('M' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("N" . $rowNum, $row['buram'] ?? 0)->getStyle('N' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("O" . $rowNum, $row['terpendam'] ?? 0)->getStyle('O' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("P" . $rowNum, $row['tempel angka'] ?? 0)->getStyle('P' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("Q" . $rowNum, $row['pagar kunci'] ?? 0)->getStyle('Q' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("R" . $rowNum, $row['segel putus'] ?? 0)->getStyle('R' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("S" . $rowNum, $row['cabut meter'] ?? 0)->getStyle('S' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("T" . $rowNum, $row['unknown'] ?? 0)->getStyle('T' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);

        $sheet->setCellValue("U" . $rowNum, $row['total'] ?? 0)
            ->getStyle('U' . $rowNum)->applyFromArray(['borders' => $this->allBorder])->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('00B050');
    }

    private function setFooter($sheet, $rows, $rowNum)
    {
        $sheet->setCellValue("A" . $rowNum, "TOTAL")->mergeCells("A$rowNum:C$rowNum", Worksheet::MERGE_CELL_CONTENT_MERGE)
            ->getStyle("A$rowNum:C$rowNum")->applyFromArray($this->styleHeader);

        $sheet->setCellValue("D" . $rowNum, $this->reduceTotal($rows, 'normal'))
            ->getStyle('D' . $rowNum)->applyFromArray(['borders' => $this->allBorder])->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('00B050');

        $sheet->setCellValue("E" . $rowNum, $this->reduceTotal($rows, 'mati'))->getStyle('E' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("F" . $rowNum, $this->reduceTotal($rows, 'stand tunggu'))->getStyle('F' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("G" . $rowNum, $this->reduceTotal($rows, 'stand mundur'))->getStyle('G' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("H" . $rowNum, $this->reduceTotal($rows, 'ganti wm'))->getStyle('H' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("I" . $rowNum, $this->reduceTotal($rows, 'pencurian air'))->getStyle('I' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("J" . $rowNum, $this->reduceTotal($rows, 'tidak terjangkau'))->getStyle('J' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("K" . $rowNum, $this->reduceTotal($rows, 'berembun'))->getStyle('K' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("L" . $rowNum, $this->reduceTotal($rows, 'hilang'))->getStyle('L' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("M" . $rowNum, $this->reduceTotal($rows, 'rusak'))->getStyle('M' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("N" . $rowNum, $this->reduceTotal($rows, 'buram'))->getStyle('N' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("O" . $rowNum, $this->reduceTotal($rows, 'terpendam'))->getStyle('O' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("P" . $rowNum, $this->reduceTotal($rows, 'tempel angka'))->getStyle('P' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("Q" . $rowNum, $this->reduceTotal($rows, 'pagar kunci'))->getStyle('Q' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("R" . $rowNum, $this->reduceTotal($rows, 'segel putus'))->getStyle('R' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("S" . $rowNum, $this->reduceTotal($rows, 'cabut meter'))->getStyle('S' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
        $sheet->setCellValue("T" . $rowNum, $this->reduceTotal($rows, 'unknown'))->getStyle('T' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);

        $sheet->setCellValue("U" . $rowNum, $this->reduceTotal($rows, 'total'))
            ->getStyle('U' . $rowNum)->applyFromArray(['borders' => $this->allBorder])->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('00B050');
    }

    private function reduceTotal($data, $field)
    {
        return array_sum(array_column($data, $field));
    }
}
