<?php

namespace Tests\App\Services;

use App\Services\SelisihFotoService;
use CodeIgniter\Test\CIUnitTestCase;

class SelisihFotoServiceTest extends CIUnitTestCase
{
    protected string $tmpDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tmpDir = sys_get_temp_dir() . '/fotowm_test_' . uniqid();
        mkdir($this->tmpDir, 0777, true);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->recursiveRemove($this->tmpDir);
    }

    private function recursiveRemove($dir)
    {
        if (!is_dir($dir)) {
            return;
        }
        $objects = scandir($dir);

        foreach ($objects as $object) {
            if ($object !== "." && $object !== "..") {
                if (is_dir($dir . "/" . $object)) {
                    $this->recursiveRemove($dir . "/" . $object);
                } else {
                    unlink($dir . "/" . $object);
                }
            }
        }
        rmdir($dir);
    }

    public function testListFileReturnsEmptyWhenDirNotFound()
    {
        $service = new SelisihFotoService($this->tmpDir);
        $files = $service->listFile('agus', '05', '2024');
        $this->assertIsArray($files);
        $this->assertEmpty($files);
    }

    public function testListFileReturnsCorrectFiles()
    {
        $subDir = $this->tmpDir . '/202405/agus';
        mkdir($subDir, 0777, true);
        file_put_contents($subDir . '/foto1.jpg', 'fakeimage');
        file_put_contents($subDir . '/foto2.jpg', 'fakeimage');

        $service = new SelisihFotoService($this->tmpDir);
        $files = $service->listFile('agus', '5', '2024');

        sort($files);
        $this->assertEquals(['foto1.jpg', 'foto2.jpg'], $files);
    }
}
