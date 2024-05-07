<?php

namespace App\Libraries;

use DateTime;

class MyDate
{
    private $startDate = '';
    private $endDate = '';

    public function __construct()
    {
    }

    public static function withDateYearAndMonth($year, $month)
    {
        $instance = new self();
        $currentDate = new DateTime($year . "-" . $month . '-01');
        $instance->startDate = date("Y-m-d", $currentDate->getTimestamp());
        $instance->endDate = date("Y-m-t", $currentDate->getTimestamp());
        return $instance;
    }

    public static function withPeriode($periode)
    {
        $instance = new self();
        $currentDate = new DateTime($periode . '-01');
        $instance->startDate = date("Y-m-d", $currentDate->getTimestamp());
        $instance->endDate = date("Y-m-t", $currentDate->getTimestamp());
        return $instance;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }
}
