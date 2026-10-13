<?php

declare(strict_types=1);

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class BulanHelperTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper('bulan');
    }

    public function testListBulanReturnsCorrectStructure(): void
    {
        $bulanList = listBulan();
        $this->assertIsArray($bulanList);
        $this->assertCount(12, $bulanList);
        $this->assertSame(1, $bulanList[0]['code']);
        $this->assertSame('Januari', $bulanList[0]['value']);
        $this->assertSame(12, $bulanList[11]['code']);
        $this->assertSame('Desember', $bulanList[11]['value']);
    }

    public function testGetMonthNameValidInt(): void
    {
        $this->assertSame('Januari', getMonthName(1));
        $this->assertSame('Desember', getMonthName(12));
    }

    public function testGetMonthNameValidString(): void
    {
        $this->assertSame('Januari', getMonthName('01'));
        $this->assertSame('Oktober', getMonthName('10'));
    }

    public function testGetMonthNameInvalid(): void
    {
        $this->assertSame('', getMonthName(0));
        $this->assertSame('', getMonthName(13));
        $this->assertSame('', getMonthName('invalid'));
    }
}
