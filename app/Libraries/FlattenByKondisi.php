<?php

namespace App\Libraries;

use App\Models\KondisiModel;
use App\Models\Sikompak\CabangModel;

class FlattenByKondisi
{
    private array $data;
    private string $periode;
    private array $branchList;
    private array $conditionList;

    public function __construct(array $data, string $periode)
    {
        $this->data = $data;
        $this->periode = $periode;

        $branches = new CabangModel();
        $this->branchList = $branches->findAll();

        $conditions = new KondisiModel();
        $this->conditionList = $conditions->findAll();
    }

    public function get(): array
    {
        return array_map(function (object $branch) {
            return $this->flattenBranch($branch);
        }, $this->branchList);
    }

    public function getFooter(): array
    {
        return $this->flattenFooter();
    }

    private function flattenBranch(object $branch): array
    {
        $result = [
            'satker' => $branch->id_cabang,
            'cabang' => $branch->nm_cabang,
            'periode' => $this->periode,
            'total' => $this->countBranchTotal($branch),
        ];

        foreach ($this->conditionList as $condition) {
            $result[$condition->kondisi] = $this->countBranchConditionTotal($branch, $condition);
        }

        $result['unknown'] = $this->countBranchUnknownTotal($branch);

        return $result;
    }

    private function countBranchTotal(object $branch): int
    {
        return array_reduce($this->data, function (int $total, object $row) use ($branch) {
            return $row->satker === $branch->id_cabang ? $total + $row->total : $total;
        }, 0);
    }

    private function countBranchConditionTotal(object $branch, object $condition): int
    {
        return array_reduce($this->data, function (int $count, object $row) use ($branch, $condition) {
            return $row->satker === $branch->id_cabang && $row->kondisi === $condition->kondisi ? $count + $row->total : $count;
        }, 0);
    }

    private function countBranchUnknownTotal(object $branch): int
    {
        return array_reduce($this->data, function (int $count, object $row) use ($branch) {
            return $row->satker === $branch->id_cabang && $row->kondisi === '' ? $count + $row->total : $count;
        }, 0);
    }

    private function flattenFooter(): array
    {
        $result = [
            "cabang" => "Total",
            "total" => $this->countTotal()
        ];

        foreach ($this->conditionList as $condition) {
            $result[$condition->kondisi] = $this->countConditionTotal($condition);
        }

        return [$result];
    }
    private function countTotal(): int
    {
        return array_reduce($this->data, fn (int $carry, object $item) => $carry + $item->total, 0);
    }

    private function countConditionTotal(object $condition): int
    {
        return array_reduce(
            $this->data,
            fn (int $carry, object $item) => $carry + ($item->kondisi === $condition->kondisi ? $item->total : 0),
            0
        );
    }
}
