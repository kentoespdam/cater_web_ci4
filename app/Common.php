<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
/**
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (! function_exists('get_start_end_date_from_periode')) {
    function get_start_end_date_from_periode(?string $periode): array
    {
        $p = !empty($periode) ? $periode : date('Y-m');
        $dt = new \DateTimeImmutable("{$p}-01");

        return [$dt->format('Y-m-01'), $dt->format('Y-m-t')];
    }
}

if (! function_exists('get_start_end_date_from_year_month')) {
    function get_start_end_date_from_year_month($year, $month): array
    {
        $y = !empty($year) ? $year : date('Y');
        $m = !empty($month) ? (is_numeric($month) ? str_pad((string) $month, 2, '0', STR_PAD_LEFT) : $month) : date('m');
        $dt = new \DateTimeImmutable("{$y}-{$m}-01");

        return [$dt->format('Y-m-01'), $dt->format('Y-m-t')];
    }
}
