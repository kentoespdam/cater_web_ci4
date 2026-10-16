<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BacaMeterModel;
use App\Models\Sikompak\CustModel;

final class HasilBacaSummaryService
{
    public function __construct(
        private readonly ?CustModel $custModel = null,
        private readonly ?BacaMeterModel $bacaMeterModel = null,
    ) {
    }

    public function getSummaryData(string $periode): array
    {
        [$startDate, $endDate] = get_start_end_date_from_periode($periode);
        $cacheKey = "hasil_baca_data_$startDate-$endDate";

        if (cache($cacheKey) !== null) {
            return cache($cacheKey);
        }

        $customerModel = $this->custModel ?? new CustModel();
        $meterReadModel = $this->bacaMeterModel ?? new BacaMeterModel();

        $customerData = $customerModel->getTotalPelangganPerPembaca();
        $meterReadData = $meterReadModel->getJumlahCekData($startDate, $endDate);

        $result = $this->generateResult($customerData, $meterReadData);
        $footer = $this->generateFooter($result);

        $data = [
            "total" => count($result),
            "rows" => $result,
            "footer" => $footer,
        ];

        cache()->save($cacheKey, $data, 120);

        return $data;
    }

    public function generateResult(array $cust, array $hasilBaca): array
    {
        return array_map(function ($item) use ($hasilBaca) {
            $curHasilBaca = $this->getCurrentHasilBaca($hasilBaca, $item->petugas);

            if ($curHasilBaca == null) {
                return [
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
            }
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
                "progress" => number_format($persen, 2),
            ];
        }, $cust);
    }

    private function getCurrentHasilBaca(array $hasilBaca, string $petugas): ?object
    {
        $values = array_values(
            array_filter($hasilBaca, function ($item) use ($petugas) {
                return $item->petugas == $petugas;
            }),
        );

        return count($values) == 0 ? null : (object) $values[0];
    }

    public function generateFooter(array $data): array
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
}
