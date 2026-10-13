<?php

namespace App\Libraries;

use CodeIgniter\HTTP\DownloadResponse;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;

class KondisiToExcel
{
    private array $data;
    private string $fileName;

    public function __construct(array $data, string $fileName)
    {
        $this->data = $data;
        $this->fileName = $fileName;
    }

    public function download(): DownloadResponse
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'kondisi_');

        if ($tempFile === false) {
            throw new \RuntimeException('Failed to create temporary file.');
        }

        register_shutdown_function(function () use ($tempFile) {
            if (file_exists($tempFile)) {
                @unlink($tempFile);
            }
        });

        $writer = new Writer();
        $writer->openToFile($tempFile);

        $headers = [
            "No",
            "Periode",
            "Cabang",
            "Normal",
            "Mati",
            "Stand Tunggu",
            "Stand Mundur",
            "Ganti WM",
            "Pencurian Air",
            "Tidak Terjangkau",
            "Berembun",
            "Hilang",
            "Rusak",
            "Buram",
            "Terpendam",
            "Tempel Angka",
            "Pagar Kunci",
            "Segel Putus",
            "Cabut Meter",
            "Tanpa Keterangan",
            "Total",
        ];

        $boldStyle = (new Style())->withFontBold(true);
        $headerStyles = array_fill(0, count($headers), $boldStyle);
        $writer->addRow(Row::fromValuesWithStyles($headers, $headerStyles));

        $urut = 1;

        foreach ($this->data as $row) {
            $rowValues = [
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
            $writer->addRow(Row::fromValues($rowValues));
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
        $writer->addRow(Row::fromValuesWithStyles($totalRow, $headerStyles));

        $writer->close();

        return response()->download($tempFile, null)
            ->setFileName($this->fileName . '.xlsx')
            ->setContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    private function reduceTotal(array $data, string $field): int
    {
        return array_sum(array_column($data, $field));
    }
}
