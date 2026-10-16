<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BacaMeterModel;

final class BacaMeterQueryService
{
    public function __construct(
        private readonly ?BacaMeterModel $bacaMeterModel = null,
    ) {
    }

    private function getModel(): BacaMeterModel
    {
        return $this->bacaMeterModel ?? new BacaMeterModel();
    }

    public function getJumlahCekData(string $tglAwal, string $tglAkhir): array
    {
        return $this->getModel()->getJumlahCekData($tglAwal, $tglAkhir);
    }

    public function getDataGagalPerUser(string $tglAwal, string $tglAkhir, string $user): array
    {
        return $this->getModel()->getDataGagalPerUser($tglAwal, $tglAkhir, $user);
    }

    public function getDataVerif(
        string $tglAwal,
        string $tglAkhir,
        string $cek,
        string $order,
        ?int $page = null,
        ?int $size = null,
        ?string $sort = null,
        ?string $cabang = null,
        ?string $petugas = null,
        ?string $kampung = null,
        ?string $kondisi = null,
        ?string $nosamw = null,
    ): array {
        return $this->getModel()->getDataVerif(
            $tglAwal,
            $tglAkhir,
            $cek,
            $order,
            $page,
            $size,
            $sort,
            $cabang,
            $petugas,
            $kampung,
            $kondisi,
            $nosamw,
        );
    }

    public function getTotalData(
        string $tglAwal,
        string $tglAkhir,
        string $cek,
        ?string $cabang = null,
        ?string $petugas = null,
        ?string $kampung = null,
        ?string $kondisi = null,
        ?string $nosamw = null,
    ): ?object {
        return $this->getModel()->getTotalData(
            $tglAwal,
            $tglAkhir,
            $cek,
            $cabang,
            $petugas,
            $kampung,
            $kondisi,
            $nosamw,
        );
    }

    public function cekFoto(string $nosamw, string $tglAwal, string $tglAkhir): array
    {
        return $this->getModel()->cekFoto($nosamw, $tglAwal, $tglAkhir);
    }

    public function getHasilBaca0(string $tglAwal, string $tglAkhir, ?string $satker = null, ?int $size = null, ?int $offset = null): array
    {
        return $this->getModel()->getHasilBaca0($tglAwal, $tglAkhir, $satker, $size, $offset);
    }

    public function getHasilBaca0Cursor(string $tglAwal, string $tglAkhir, ?string $satker = null): \Generator
    {
        return $this->getModel()->getHasilBaca0Cursor($tglAwal, $tglAkhir, $satker);
    }

    public function getKondisiBaca(string $tglAwal, string $tglAkhir): array
    {
        return $this->getModel()->getKondisiBaca($tglAwal, $tglAkhir);
    }

    public function getKondisiBaca0(string $tglAwal, string $tglAkhir): array
    {
        return $this->getModel()->getKondisiBaca0($tglAwal, $tglAkhir);
    }

    public function getDataVerifCursor(
        string $tglAwal,
        string $tglAkhir,
        string $cek,
        string $order = 'asc',
        ?string $cabang = null,
        ?string $petugas = null,
        ?string $kampung = null,
        ?string $kondisi = null,
        ?string $nosamw = null,
    ): \Generator {
        return $this->getModel()->getDataVerifCursor(
            $tglAwal,
            $tglAkhir,
            $cek,
            $order,
            $cabang,
            $petugas,
            $kampung,
            $kondisi,
            $nosamw,
        );
    }
}
