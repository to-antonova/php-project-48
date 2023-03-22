<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;
use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testGenDiff()
    {
        $file1 = __DIR__ . "/fixtures/file1.json";
        $file2 = __DIR__ . "/fixtures/file2.json";
        $expected1 = file_get_contents(__DIR__ . "/fixtures/expected1.txt");
        $this->assertEquals($expected1, genDiff($file1, $file2));

        $file3 = __DIR__ . "/fixtures/file3.json";
        $file4 = __DIR__ . "/fixtures/file4.json";
        $expected2 = file_get_contents(__DIR__ . "/fixtures/expected2.txt");
        $this->assertEquals($expected2, genDiff($file3, $file4));

        echo "\033[42mTests passed!\033[0m" . PHP_EOL;
    }
}
