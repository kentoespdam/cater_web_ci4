<?php

declare(strict_types=1);

namespace App\Libraries;

use App\Models\KondisiModel;
use App\Models\Sikompak\CabangModel;

class FlattenByKondisi
{
    private static ?array $cachedBranches = null;
    private static ?array $cachedConditions = null;

    private array $data;
    private string $periode;
    private array $branchList;
    private array $conditionList;

    private ?array $rows = null;
    private ?array $footer = null;

    public function __construct(
        array $data,
        string $periode,
        ?array $branchList = null,
        ?array $conditionList = null,
    ) {
        $this->data = $data;
        $this->periode = $periode;

        $this->branchList = $branchList ?? (self::$cachedBranches ??= (new CabangModel())->findAll());
        $this->conditionList = $conditionList ?? (self::$cachedConditions ??= (new KondisiModel())->select("LOWER(kondisi) AS kondisi")->findAll());
    }

    public static function clearCache(): void
    {
        self::$cachedBranches = null;
        self::$cachedConditions = null;
    }

    public function get(): array
    {
        if ($this->rows === null) {
            $this->process();
        }

        return $this->rows;
    }

    public function getFooter(): array
    {
        if ($this->footer === null) {
            $this->process();
        }

        return $this->footer;
    }

    private function process(): void
    {
        $matrix = [];
        $branchTotals = [];
        $branchUnknowns = [];
        $condTotals = [];
        $grandTotal = 0;
        $totalUnknown = 0;

        $validConditions = [];

        foreach ($this->conditionList as $cond) {
            $cName = (string) (is_object($cond) ? ($cond->kondisi ?? '') : ($cond['kondisi'] ?? ''));
            $validConditions[strtolower(trim($cName))] = true;
        }

        foreach ($this->data as $item) {
            $satker = (string) (is_object($item) ? ($item->satker ?? '') : ($item['satker'] ?? ''));
            $kondisi = (string) (is_object($item) ? ($item->kondisi ?? '') : ($item['kondisi'] ?? ''));
            $total = (int) (is_object($item) ? ($item->total ?? 0) : ($item['total'] ?? 0));

            $grandTotal += $total;
            $branchTotals[$satker] = ($branchTotals[$satker] ?? 0) + $total;

            $kLower = strtolower(trim($kondisi));

            if ($kLower === '' || $kLower === 'unknown' || !isset($validConditions[$kLower])) {
                $branchUnknowns[$satker] = ($branchUnknowns[$satker] ?? 0) + $total;
                $totalUnknown += $total;
            } else {
                $matrix[$satker][$kLower] = ($matrix[$satker][$kLower] ?? 0) + $total;
                $condTotals[$kLower] = ($condTotals[$kLower] ?? 0) + $total;
            }
        }

        $this->rows = [];

        foreach ($this->branchList as $branch) {
            $satker = (string) (is_object($branch) ? ($branch->id_cabang ?? '') : ($branch['id_cabang'] ?? ''));
            $nmCabang = (string) (is_object($branch) ? ($branch->nm_cabang ?? '') : ($branch['nm_cabang'] ?? ''));

            $row = [
                'satker'  => $satker,
                'cabang'  => $nmCabang,
                'periode' => $this->periode,
                'total'   => $branchTotals[$satker] ?? 0,
            ];

            foreach ($this->conditionList as $cond) {
                $cName = (string) (is_object($cond) ? ($cond->kondisi ?? '') : ($cond['kondisi'] ?? ''));
                $cKey = strtolower(trim($cName));
                $row[$cName] = $matrix[$satker][$cKey] ?? 0;
            }

            $row['unknown'] = $branchUnknowns[$satker] ?? 0;
            $this->rows[] = $row;
        }

        $footerRow = [
            'cabang' => 'Total',
            'total'  => $grandTotal,
        ];

        foreach ($this->conditionList as $cond) {
            $cName = (string) (is_object($cond) ? ($cond->kondisi ?? '') : ($cond['kondisi'] ?? ''));
            $cKey = strtolower(trim($cName));
            $footerRow[$cName] = $condTotals[$cKey] ?? 0;
        }

        $footerRow['unknown'] = $totalUnknown;
        $this->footer = [$footerRow];
    }
}
