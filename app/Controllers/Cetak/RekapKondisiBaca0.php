<?php

namespace App\Controllers\Cetak;

use App\Controllers\BaseController;
use App\Libraries\FlattenByKondisi;
use App\Libraries\MyDate;
use App\Models\BacaMeterModel;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RekapKondisiBaca0 extends BaseController
{

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

    public function index()
    {
        $req = (object)request()->getGet();
        $dateLib = MyDate::withDateYearAndMonth($req->tahun, $req->bulan);

        $tglAwal = $dateLib->getStartDate();
        $tglAkhir = $dateLib->getEndDate();
        $bulan = $req->bulan < 10 ? '0' . $req->bulan : $req->bulan;
        $periode = "{$req->tahun}-{$bulan}";


        $bacaMeterModel = new BacaMeterModel();

        $data = $bacaMeterModel->getKondisiBaca0($tglAwal, $tglAkhir);
        $flattenKondisi = new FlattenByKondisi($data, $periode);
        $result = $flattenKondisi->get();
        return $this->generateExcel($result);
    }

    private function generateExcel($data)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Kondisi Water Meter');
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $this->styleHeader['borders'] = $this->allBorder;

        $sheet->setCellValue("A1", "No")->getStyle("A1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("B1", "Periode")->getStyle("B1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("C1", "Cabang")->getStyle("C1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("D1", "Normal")->getStyle("D1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("E1", "WM Mati")->getStyle("E1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("F1", "Angka tidak wajar")->getStyle("F1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("G1", "Stand Tunggu")->getStyle("G1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("H1", "Stand Mundur")->getStyle("H1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("I1", "Ganti WM")->getStyle("I1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("J1", "Pencurian Air")->getStyle("J1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("K1", "Tidak Mengalir")->getStyle("K1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("L1", "Tidak Ketemu Alamat")->getStyle("L1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("M1", "Meter Embun")->getStyle("M1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("N1", "Meter Pecah")->getStyle("N1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("O1", "Meter Buram")->getStyle("O1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("P1", "Meter Terpendam")->getStyle("P1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("Q1", "Meter Tertimbun")->getStyle("Q1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("R1", "Edit Stand OCR")->getStyle("R1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("S1", "Ada Anjing")->getStyle("S1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("T1", "Pelanggan Tempel Angka")->getStyle("T1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("U1", "Pagar Kunci")->getStyle("U1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("V1", "Segel Putus")->getStyle("V1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("W1", "Box Meter Terkunci")->getStyle("W1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("X1", "Meter Macet")->getStyle("X1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("Y1", "Meter Tera")->getStyle("Y1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("Z1", "Meter Terbalik")->getStyle("Z1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("AA1", "Air Tidak Terpakai")->getStyle("AA1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("AB1", "Lain-Lain")->getStyle("AB1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("AC1", "Tanpa Keterangan")->getStyle("AC1")->applyFromArray($this->styleHeader);
        $sheet->setCellValue("AD1", "JML PEMAKAIAN 0")->getStyle("AD1")->applyFromArray($this->styleHeader);

        $urut = 1;
        $rowNum = 2;
        foreach ($data as $row) {
            $sheet->setCellValue("A" . $rowNum, $urut++)->getStyle('A' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
            $sheet->setCellValue("B" . $rowNum, $row['periode'])->getStyle('B' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
            $sheet->setCellValue("C" . $rowNum, $row['cabang'])->getStyle('C' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
            $sheet->setCellValue("D" . $rowNum, $row['Normal'])
                ->getStyle('D' . $rowNum)->applyFromArray(['borders' => $this->allBorder])->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('00B050');
            $sheet->setCellValue("E" . $rowNum, $row['WM Mati'])->getStyle('E' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
            $sheet->setCellValue("F" . $rowNum, $row['Angka tidak wajar'])->getStyle('F' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
            $sheet->setCellValue("G" . $rowNum, $row['Stand Tunggu'])->getStyle('G' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
            $sheet->setCellValue("H" . $rowNum, $row['Stand Mundur'])->getStyle('H' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
            $sheet->setCellValue("I" . $rowNum, $row['Ganti WM'])->getStyle('I' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
            $sheet->setCellValue("J" . $rowNum, $row['Pencurian Air'])->getStyle('J' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
            $sheet->setCellValue("K" . $rowNum, $row['Tidak Mengalir'])->getStyle('K' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
            $sheet->setCellValue("L" . $rowNum, $row['Tidak Ketemu Alamat'])->getStyle('L' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
            $sheet->setCellValue("M" . $rowNum, $row['Meter Embun'])->getStyle('M' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
            $sheet->setCellValue("N" . $rowNum, $row['Meter Pecah'])->getStyle('N' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
            $sheet->setCellValue("O" . $rowNum, $row['Meter Buram'])->getStyle('O' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
            $sheet->setCellValue("P" . $rowNum, $row['Meter Terpendam'])->getStyle('P' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
            $sheet->setCellValue("Q" . $rowNum, $row['Meter Tertimbun'])->getStyle('Q' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
            $sheet->setCellValue("R" . $rowNum, $row['Edit Stand OCR'])->getStyle('R' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
            $sheet->setCellValue("S" . $rowNum, $row['Ada Anjing'])->getStyle('S' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
            $sheet->setCellValue("T" . $rowNum, $row['Pelanggan Tempel Angka'])->getStyle('T' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
            $sheet->setCellValue("U" . $rowNum, $row['Pagar Kunci'])->getStyle('U' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
            $sheet->setCellValue("V" . $rowNum, $row['Segel Putus'])->getStyle('V' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
            $sheet->setCellValue("W" . $rowNum, $row['Box Meter Terkunci'])->getStyle('W' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
            $sheet->setCellValue("X" . $rowNum, $row['Meter Macet'])->getStyle('X' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
            $sheet->setCellValue("Y" . $rowNum, $row['Meter Tera'])->getStyle('Y' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
            $sheet->setCellValue("Z" . $rowNum, $row['Meter Terbalik'])->getStyle('Z' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
            $sheet->setCellValue("AA" . $rowNum, $row['Air Tidak Terpakai'])->getStyle('AA' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
            $sheet->setCellValue("AB" . $rowNum, $row['Lain-Lain'])->getStyle('AB' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
            $sheet->setCellValue("AC" . $rowNum, $row['unknown'])->getStyle('AC' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
            $sheet->setCellValue("AD" . $rowNum, $row['total'])->getStyle('AD' . $rowNum)->applyFromArray(['borders' => $this->allBorder]);
            $rowNum++;
        }



        $writer = new Xlsx($spreadsheet);
        $filename = "rekap_kondisi_baca_0-" . date('Y-m-d-His');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }
}
