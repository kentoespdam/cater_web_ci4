<?php

namespace App\Libraries;

use CodeIgniter\HTTP\DownloadResponse;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;

class HasilBacaToExcel
{
    private iterable $data;
    private string $fileName;

    public function __construct(iterable $data, string $fileName)
    {
        $this->data = $data;
        $this->fileName = $fileName;
    }

    public function download(): DownloadResponse
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'hasil_baca_');

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

        $fields = [
            "tgl", "no_sam", "stan_kini", "stan_lalu", "pakai", "petugas", "kondisi", "ket", "info", "ptgs_met", "rata", "kd_wil", "wil", "kd_cabang", "nm_cabang", "kd_cek", "status_cek", "nama", "alamat",
        ];

        $boldStyle = (new Style())->withFontBold(true);
        $headerStyles = array_fill(0, count($fields), $boldStyle);
        $writer->addRow(Row::fromValuesWithStyles($fields, $headerStyles));

        foreach ($this->data as $item) {
            if (is_object($item)) {
                $item = (array) $item;
            }
            $rowValues = [];

            foreach ($fields as $field) {
                $rowValues[] = $item[$field] ?? '';
            }
            $writer->addRow(Row::fromValues($rowValues));
        }

        $writer->close();

        return response()->download($tempFile, null)
            ->setFileName($this->fileName . '.xlsx')
            ->setContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
