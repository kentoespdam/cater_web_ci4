<?php

namespace App\Controllers\Api;

use App\Libraries\MyDate;
use App\Models\BacaMeterModel;
use App\Models\Sikompak\CustModel;
use CodeIgniter\Controller;

class HasilBaca extends Controller
{
    public function index()
    {
        $requestedDate = request()->getGet("periode");
        $myDate = MyDate::withPeriode($requestedDate);
        $startDate = $myDate->getStartDate();
        $endDate = $myDate->getEndDate();

        $cacheKey = "hasil_baca_data_$startDate-$endDate";
        if (cache($cacheKey) !== null) {
            return response()->setJSON(cache($cacheKey));
        }

        $customerModel = new CustModel();
        $meterReadModel = new BacaMeterModel();

        $customerData = $customerModel->getTotalPelangganPerPembaca();
        $meterReadData = $meterReadModel->getJumlahCekData($startDate, $endDate);
        $result = $this->generateResult($customerData, $meterReadData);
        $footer = $this->generateFooter($result);

        $data = [
            "total" => count($result),
            "rows" => $result,
            "footer" => $footer
        ];

        cache()->save($cacheKey, $data, 120);

        return response()->setJSON($data);
    }

    private function generateResult($cust, $hasilBaca)
    {
        return array_values(
            array_map(function ($item) use ($hasilBaca) {
                $curHasilBaca = $this->getCurrentHasilBaca($hasilBaca, $item->petugas);
                if ($curHasilBaca == null) return [
                    "petugas" => $item->petugas,
                    "nama" => $item->nama,
                    "cabang" => $item->cabang,
                    "jml_pelanggan" => $item->jml_pelanggan,
                    "jml_baca" => 0,
                    "cek_koperasi" => 0,
                    "cek_cabang" => 0,
                    "gagal" => 0,
                    "progress" => 0,
                ];
                $persen = ($curHasilBaca->jml_baca / $item->jml_pelanggan) * 100;
                return [
                    "petugas" => $item->petugas,
                    "nama" => $item->nama,
                    "cabang" => $item->cabang,
                    "jml_pelanggan" => $item->jml_pelanggan,
                    "jml_baca" => $curHasilBaca->jml_baca,
                    "cek_koperasi" => $curHasilBaca->cek_koperasi,
                    "cek_cabang" => $curHasilBaca->cek_cabang,
                    "gagal" => $curHasilBaca->gagal,
                    "progress" => number_format($persen, 2)
                ];
            }, $cust)
        );
    }

    private function getCurrentHasilBaca($hasilBaca, $petugas)
    {
        $values = array_values(
            array_filter($hasilBaca, function ($item) use ($petugas) {
                return $item->petugas == $petugas;
            })
        );
        return count($values) == 0 ? null : (object)$values[0];
    }

    private function generateFooter(array $data): array
    {
        return array_reduce($data, function (array $total, array $item) {
            return [
                'jml_pelanggan' => $total['jml_pelanggan'] + $item['jml_pelanggan'],
                'jml_baca' => $total['jml_baca'] + $item['jml_baca'],
                'progress' => $total['progress'] + $item['progress'],
                'cek_koperasi' => $total['cek_koperasi'] + $item['cek_koperasi'],
                'cek_cabang' => $total['cek_cabang'] + $item['cek_cabang'],
                'gagal' => $total['gagal'] + $item['gagal'],
            ];
        }, array_fill_keys(['jml_pelanggan', 'jml_baca', 'progress', 'cek_koperasi', 'cek_cabang', 'gagal'], 0));
    }

    public function getDataGagal()
    {
        $req = (object)request()->getGet();
        if (!isset($req->petugas) || !isset($req->periode) || empty($req->petugas) || empty($req->periode))
            return response()->setJSON([]);
        $skrng = new \DateTime("$req->periode-01");
        $tglAwal = date("Y-m-d", $skrng->getTimestamp());
        $akhir = new \DateTime($skrng->format('Y-m-t'));
        $tglAkhir = date('Y-m-d', $akhir->getTimestamp());

        $hasilBacaModel = new BacaMeterModel();
        $custModel = new CustModel();

        $hasilBacaData = $hasilBacaModel->getDataGagalPerUser($tglAwal, $tglAkhir, $req->petugas);
        $noPelanggan = array_map(function ($item) {
            return $item->nosamw;
        }, $hasilBacaData);
        $custData = $custModel->getDetailKampungPelanggan($noPelanggan);

        return response()->setJSON([
            "id" => $req->petugas,
            "total" => count($custData),
            "rows" => $custData
        ]);
    }
}
