<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Libraries\FlattenByKondisi;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class FlattenByKondisiTest extends CIUnitTestCase
{
    private array $mockBranches;
    private array $mockConditions;

    protected function setUp(): void
    {
        parent::setUp();
        FlattenByKondisi::clearCache();

        $this->mockBranches = [
            (object) ['id_cabang' => '0200', 'nm_cabang' => 'Cabang Purwokerto 1'],
            (object) ['id_cabang' => '0300', 'nm_cabang' => 'Cabang Purwokerto 2'],
            (object) ['id_cabang' => '0400', 'nm_cabang' => 'Cabang Banyumas'],
        ];

        $this->mockConditions = [
            (object) ['kondisi' => 'normal'],
            (object) ['kondisi' => 'rusak'],
            (object) ['kondisi' => 'hilang'],
        ];
    }

    public function testBasicFlatteningAndTotals(): void
    {
        $data = [
            (object) ['satker' => '0200', 'kondisi' => 'normal', 'total' => 10],
            (object) ['satker' => '0200', 'kondisi' => 'rusak', 'total' => 3],
            (object) ['satker' => '0300', 'kondisi' => 'normal', 'total' => 5],
            (object) ['satker' => '0300', 'kondisi' => 'hilang', 'total' => 2],
        ];

        $flatten = new FlattenByKondisi($data, '2026-10', $this->mockBranches, $this->mockConditions);
        $rows = $flatten->get();
        $footer = $flatten->getFooter();

        $this->assertCount(3, $rows);

        // Branch 0200
        $this->assertSame('0200', $rows[0]['satker']);
        $this->assertSame('Cabang Purwokerto 1', $rows[0]['cabang']);
        $this->assertSame('2026-10', $rows[0]['periode']);
        $this->assertSame(13, $rows[0]['total']);
        $this->assertSame(10, $rows[0]['normal']);
        $this->assertSame(3, $rows[0]['rusak']);
        $this->assertSame(0, $rows[0]['hilang']);
        $this->assertSame(0, $rows[0]['unknown']);

        // Branch 0300
        $this->assertSame('0300', $rows[1]['satker']);
        $this->assertSame(7, $rows[1]['total']);
        $this->assertSame(5, $rows[1]['normal']);
        $this->assertSame(0, $rows[1]['rusak']);
        $this->assertSame(2, $rows[1]['hilang']);
        $this->assertSame(0, $rows[1]['unknown']);

        // Branch 0400 (empty)
        $this->assertSame('0400', $rows[2]['satker']);
        $this->assertSame(0, $rows[2]['total']);
        $this->assertSame(0, $rows[2]['normal']);

        // Footer
        $this->assertCount(1, $footer);
        $this->assertSame('Total', $footer[0]['cabang']);
        $this->assertSame(20, $footer[0]['total']);
        $this->assertSame(15, $footer[0]['normal']);
        $this->assertSame(3, $footer[0]['rusak']);
        $this->assertSame(2, $footer[0]['hilang']);
        $this->assertSame(0, $footer[0]['unknown']);
    }

    public function testUnknownConditionHandling(): void
    {
        $data = [
            (object) ['satker' => '0200', 'kondisi' => '', 'total' => 4],
            (object) ['satker' => '0200', 'kondisi' => 'unknown', 'total' => 2],
            (object) ['satker' => '0300', 'kondisi' => null, 'total' => 3],
            (object) ['satker' => '0300', 'kondisi' => 'normal', 'total' => 10],
        ];

        $flatten = new FlattenByKondisi($data, '2026-10', $this->mockBranches, $this->mockConditions);
        $rows = $flatten->get();
        $footer = $flatten->getFooter();

        // Branch 0200 unknown = 4 + 2 = 6
        $this->assertSame(6, $rows[0]['unknown']);
        $this->assertSame(6, $rows[0]['total']);

        // Branch 0300 unknown = 3, normal = 10, total = 13
        $this->assertSame(3, $rows[1]['unknown']);
        $this->assertSame(10, $rows[1]['normal']);
        $this->assertSame(13, $rows[1]['total']);

        // Footer unknown = 6 + 3 = 9, total = 19
        $this->assertSame(9, $footer[0]['unknown']);
        $this->assertSame(19, $footer[0]['total']);

        // Footer unknown matches sum of branch unknowns
        $sumBranchUnknown = array_sum(array_column($rows, 'unknown'));
        $this->assertSame($sumBranchUnknown, $footer[0]['unknown']);
    }

    public function testCaseInsensitiveMatching(): void
    {
        $conditionsWithMixedCase = [
            (object) ['kondisi' => 'Normal'],
            (object) ['kondisi' => 'Rusak Ringan'],
        ];

        $data = [
            (object) ['satker' => '0200', 'kondisi' => 'normal', 'total' => 8],
            (object) ['satker' => '0200', 'kondisi' => 'RUSAK RINGAN', 'total' => 2],
            (object) ['satker' => '0300', 'kondisi' => 'NORMAL', 'total' => 7],
        ];

        $flatten = new FlattenByKondisi($data, '2026-10', $this->mockBranches, $conditionsWithMixedCase);
        $rows = $flatten->get();
        $footer = $flatten->getFooter();

        // Keys retain master condition casing
        $this->assertSame(8, $rows[0]['Normal']);
        $this->assertSame(2, $rows[0]['Rusak Ringan']);

        $this->assertSame(7, $rows[1]['Normal']);
        $this->assertSame(0, $rows[1]['Rusak Ringan']);

        $this->assertSame(15, $footer[0]['Normal']);
        $this->assertSame(2, $footer[0]['Rusak Ringan']);
    }

    public function testDataAsAssociativeArray(): void
    {
        $branchesAsArray = [
            ['id_cabang' => '0200', 'nm_cabang' => 'Cabang Purwokerto 1'],
        ];

        $conditionsAsArray = [
            ['kondisi' => 'normal'],
        ];

        $dataAsArray = [
            ['satker' => '0200', 'kondisi' => 'normal', 'total' => 15],
            ['satker' => '0200', 'kondisi' => '', 'total' => 5],
        ];

        $flatten = new FlattenByKondisi($dataAsArray, '2026-10', $branchesAsArray, $conditionsAsArray);
        $rows = $flatten->get();
        $footer = $flatten->getFooter();

        $this->assertSame('0200', $rows[0]['satker']);
        $this->assertSame(20, $rows[0]['total']);
        $this->assertSame(15, $rows[0]['normal']);
        $this->assertSame(5, $rows[0]['unknown']);

        $this->assertSame(20, $footer[0]['total']);
        $this->assertSame(15, $footer[0]['normal']);
        $this->assertSame(5, $footer[0]['unknown']);
    }

    public function testConditionNotInConditionListGoesToUnknown(): void
    {
        $data = [
            (object) ['satker' => '0200', 'kondisi' => 'rusak parah', 'total' => 5],
            (object) ['satker' => '0200', 'kondisi' => 'afkir', 'total' => 3],
            (object) ['satker' => '0300', 'kondisi' => 'normal', 'total' => 10],
        ];

        $flatten = new FlattenByKondisi($data, '2026-10', $this->mockBranches, $this->mockConditions);
        $rows = $flatten->get();
        $footer = $flatten->getFooter();

        // Branch 0200 unknown = 5 + 3 = 8
        $this->assertSame(8, $rows[0]['unknown']);
        $this->assertSame(8, $rows[0]['total']);

        // Branch 0300 normal = 10, total = 10
        $this->assertSame(10, $rows[1]['normal']);
        $this->assertSame(10, $rows[1]['total']);
        $this->assertSame(0, $rows[1]['unknown']);

        // Footer unknown = 8, normal = 10, total = 18
        $this->assertSame(8, $footer[0]['unknown']);
        $this->assertSame(10, $footer[0]['normal']);
        $this->assertSame(18, $footer[0]['total']);
    }

    public function testResultMemoization(): void
    {
        $data = [
            (object) ['satker' => '0200', 'kondisi' => 'normal', 'total' => 10],
        ];

        $flatten = new FlattenByKondisi($data, '2026-10', $this->mockBranches, $this->mockConditions);

        $firstGet = $flatten->get();
        $secondGet = $flatten->get();
        $firstFooter = $flatten->getFooter();
        $secondFooter = $flatten->getFooter();

        $this->assertSame($firstGet, $secondGet);
        $this->assertSame($firstFooter, $secondFooter);
    }
}
