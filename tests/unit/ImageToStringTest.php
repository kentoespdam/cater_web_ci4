<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Libraries\ImageToString;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ImageToStringTest extends CIUnitTestCase
{
    public function testReturnsEmptyStringOnInvalidImageSource(): void
    {
        $converter = new ImageToString('invalid/path/image.jpg');
        $result = $converter->get();
        $this->assertSame('', $result);
    }

    public function testConfigImageServerHostExists(): void
    {
        $config = config('App');
        $this->assertObjectHasProperty('imageServerHost', $config);
        $this->assertNotEmpty($config->imageServerHost);
    }
}
