<?php

namespace App\Controllers\Cetak;

use App\Controllers\BaseController;
use App\Libraries\HasilBacaToExcel;
use App\Libraries\MyDate;
use App\Models\BacaMeterModel;

class DetailHasilBaca0 extends BaseController
{
    private $fields = [
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

    public function index()
    {
        $req = (object)request()->getGet();
        $dateLib = MyDate::withDateYearAndMonth($req->tahun, $req->bulan);

        $tglAwal = $dateLib->getStartDate();
        $tglAkhir = $dateLib->getEndDate();
        $cabang = isset($req->cabang) && $req->cabang != "" ? $req->cabang : null;

        $bacaMeterModel = new BacaMeterModel();

        $data = $bacaMeterModel->getHasilBaca0($tglAwal, $tglAkhir, $cabang);
        $filename = "hasil_baca_0_" . date('YmdHis');
        $toExcel = new HasilBacaToExcel($data, $filename);

        return $toExcel->download();
        // return $this->generateExcel($data);
    }

    private function generateExcel($data)
    {
        ini_set('memory_limit', "256M");
        ini_set('default_charset', '');
        mb_http_output('pass');
        mb_detect_order(["UTF-8"]);
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
        $spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->setActiveSheetIndex(0);

        $sheet = $spreadsheet->getActiveSheet();

        foreach ($this->fields as $field) {
            $f = (object)$field;
            $sheet->setCellValue($f->col . "1", $f->value);
        }

        $rowNum = 2;
        foreach ($data as $row) {
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


        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);
        $filename = "hasil_baca_0_" . date('YmdHis');
        // $writer->save('php://output');
        $writer->save("/tmp/$filename.xlsx");

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');

        readfile("/tmp/$filename.xlsx");
        // unlink("/tmp/$filename.xlsx");
    }
}
