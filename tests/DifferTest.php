<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;
use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testGenDiff()
    {
        $pathToFile1 = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'file1.json');
        $pathToFile2 = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'file2.json');
        $expected = '{
            - follow: false
            host: hexlet.io
            - proxy: 123.234.53.22
            - timeout: 50
            + timeout: 20
            + verbose: true
        }';

        $this->assertEquals(genDiff($pathToFile1, $pathToFile2), $expected);
    }

    public function testThatGenDiffHasOneRequiredArg()
    {
        $pathToFile1 = '';
        $pathToFile2 = '';
        $this->assertNull(genDiff($pathToFile1));
        $this->assertNull(genDiff($pathToFile2));
    }
}
