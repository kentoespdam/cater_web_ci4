<?php

namespace Tests\App\Libraries;

use App\Libraries\ImageToString;
use CodeIgniter\Test\CIUnitTestCase;

class ImageToStringTest extends CIUnitTestCase
{
    private string $testFotowmPath;
    private string $sampleImageFile;
    private string $notFoundFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testFotowmPath = sys_get_temp_dir() . '/fotowm_test_' . uniqid() . '/';

        if (!is_dir($this->testFotowmPath)) {
            mkdir($this->testFotowmPath, 0777, true);
        }

        // Set config fotoWmPath and env
        config('App')->fotoWmPath = $this->testFotowmPath;
        putenv('app.fotoWmPath=' . $this->testFotowmPath);
        $_ENV['app.fotoWmPath'] = $this->testFotowmPath;


        // Create sample image (100x100 jpeg)
        $this->sampleImageFile = $this->testFotowmPath . 'sample.jpg';
        $img = imagecreatetruecolor(100, 100);
        $red = imagecolorallocate($img, 255, 0, 0);
        imagefilledrectangle($img, 0, 0, 100, 100, $red);
        imagejpeg($img, $this->sampleImageFile);
        imagedestroy($img);

        // Create notFound.png placeholder
        $this->notFoundFile = $this->testFotowmPath . 'notFound.png';
        $imgNotFound = imagecreatetruecolor(50, 50);
        $blue = imagecolorallocate($imgNotFound, 0, 0, 255);
        imagefilledrectangle($imgNotFound, 0, 0, 50, 50, $blue);
        imagejpeg($imgNotFound, $this->notFoundFile);
        imagedestroy($imgNotFound);
    }

    protected function tearDown(): void
    {
        if (is_file($this->sampleImageFile)) {
            @unlink($this->sampleImageFile);
        }

        if (is_file($this->notFoundFile)) {
            @unlink($this->notFoundFile);
        }

        if (is_dir($this->testFotowmPath)) {
            @rmdir($this->testFotowmPath);
        }
        parent::tearDown();
    }

    public function testGetThumbnailReturnsBase64AndHalfDimensions()
    {
        $converter = new ImageToString('sample.jpg');
        $base64 = $converter->get();

        $this->assertNotEmpty($base64);
        $decoded = base64_decode($base64);
        $this->assertNotFalse($decoded);

        $thumbImg = imagecreatefromstring($decoded);
        $this->assertNotFalse($thumbImg);
        $this->assertEquals(50, imagesx($thumbImg));
        $this->assertEquals(50, imagesy($thumbImg));
        imagedestroy($thumbImg);
    }

    public function testGetOriginalReturnsBase64FullDimensions()
    {
        $converter = new ImageToString('sample.jpg');
        $base64 = $converter->getOriginal();

        $this->assertNotEmpty($base64);
        $decoded = base64_decode($base64);
        $this->assertNotFalse($decoded);

        $origImg = imagecreatefromstring($decoded);
        $this->assertNotFalse($origImg);
        $this->assertEquals(100, imagesx($origImg));
        $this->assertEquals(100, imagesy($origImg));
        imagedestroy($origImg);
    }

    public function testFallbackWhenFileDoesNotExist()
    {
        $converter = new ImageToString('nonexistent.jpg');
        $base64 = $converter->get();

        $this->assertNotEmpty($base64);
        $decoded = base64_decode($base64);
        $this->assertNotFalse($decoded);

        // Should return notFound.png thumbnail (50x50 originally -> 25x25 thumbnail)
        $thumbImg = imagecreatefromstring($decoded);
        $this->assertNotFalse($thumbImg);
        $this->assertEquals(25, imagesx($thumbImg));
        $this->assertEquals(25, imagesy($thumbImg));
        imagedestroy($thumbImg);
    }

    public function testReturnsEmptyStringWhenFileAndPlaceholderDoNotExist()
    {
        @unlink($this->notFoundFile);

        $converter = new ImageToString('nonexistent.jpg');
        $base64 = $converter->get();

        $this->assertEquals('', $base64);
    }
}
