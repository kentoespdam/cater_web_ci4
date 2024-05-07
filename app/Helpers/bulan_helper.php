<?php

function listBulan()
{
    return [
        ["code" => 1, "value" => "Januari"],
        ["code" => 2, "value" => "Februari"],
        ["code" => 3, "value" => "Maret"],
        ["code" => 4, "value" => "April"],
        ["code" => 5, "value" => "Mei"],
        ["code" => 6, "value" => "Juni"],
        ["code" => 7, "value" => "Juli"],
        ["code" => 8, "value" => "Agustus"],
        ["code" => 9, "value" => "September"],
        ["code" => 10, "value" => "Oktober"],
        ["code" => 11, "value" => "November"],
        ["code" => 12, "value" => "Desember"],
    ];
}

function getMonthName($monthCode)
{
    $monthList = listBulan();

    $monthName = array_reduce($monthList, function ($carry, $item) use ($monthCode) {
        return $item['code'] === $monthCode ? $item['value'] : $carry;
    }, '');

    return $monthName;
}
