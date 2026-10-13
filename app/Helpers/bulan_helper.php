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
    static $months = [
        1 => "Januari",
        2 => "Februari",
        3 => "Maret",
        4 => "April",
        5 => "Mei",
        6 => "Juni",
        7 => "Juli",
        8 => "Agustus",
        9 => "September",
        10 => "Oktober",
        11 => "November",
        12 => "Desember",
    ];

    return $months[(int) $monthCode] ?? '';
}
