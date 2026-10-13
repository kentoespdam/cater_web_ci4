<?php

declare(strict_types=1);

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class DateTest extends CIUnitTestCase
{
    public function testGetStartEndFromYearMonthStandard(): void
    {
        [$start, $end] = get_start_end_date_from_year_month(2026, 9);
        $this->assertSame('2026-09-01', $start);
        $this->assertSame('2026-09-30', $end);
    }

    public function testGetStartEndFromYearMonthLeapYear(): void
    {
        [$start, $end] = get_start_end_date_from_year_month(2024, 2);
        $this->assertSame('2024-02-01', $start);
        $this->assertSame('2024-02-29', $end);
    }

    public function testGetStartEndFromPeriode(): void
    {
        [$start, $end] = get_start_end_date_from_periode('2026-10');
        $this->assertSame('2026-10-01', $start);
        $this->assertSame('2026-10-31', $end);
    }
}
