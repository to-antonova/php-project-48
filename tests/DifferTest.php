<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testGenDiff()
    {
        $expected1 = file_get_contents(__DIR__ . "/fixtures/expected1.txt");

        // проверка плоских файлов json
        $file1 = __DIR__ . "/fixtures/file1.json";
        $file2 = __DIR__ . "/fixtures/file2.json";
        $this->assertEquals($expected1, genDiff($file1, $file2));

        // проверка плоских файлов yaml
        $file3 = __DIR__ . "/fixtures/file3.yml";
        $file4 = __DIR__ . "/fixtures/file4.yml";
        $this->assertEquals($expected1, genDiff($file3, $file4));

        echo "\n\033[42mFlat Tests passed!\033[0m\n";
    }


    public function testGenDiffRecursive()
    {
        $expected2 = file_get_contents(__DIR__ . "/fixtures/expected2.txt");

        // рекурсивное сравнение json
        $file5 = __DIR__ . "/fixtures/file5.json";
        $file6 = __DIR__ . "/fixtures/file6.json";
        $this->assertEquals($expected2, genDiff($file5, $file6));

        // рекурсивное сравнение yaml
        $file7 = __DIR__ . "/fixtures/file7.yaml";
        $file8 = __DIR__ . "/fixtures/file8.yaml";
        $this->assertEquals($expected2, genDiff($file7, $file8));

        echo "\n\033[42mRecursive Tests passed!\033[0m\n";
    }

    public function testGenDiffPlain()
    {
        $expected3 = file_get_contents(__DIR__ . "/fixtures/expected3.txt");

        // плоский формат json
        $file5 = __DIR__ . "/fixtures/file5.json";
        $file6 = __DIR__ . "/fixtures/file6.json";
        $this->assertEquals($expected3, genDiff($file5, $file6, 'plain'));

        // плоский формат yaml
        $file7 = __DIR__ . "/fixtures/file7.yaml";
        $file8 = __DIR__ . "/fixtures/file8.yaml";
        $this->assertEquals($expected3, genDiff($file7, $file8, 'plain'));

        echo "\n\033[42mPlain Tests passed!\033[0m\n";
    }

    public function testGenDiffJsonFormat()
    {
        $expected4 = file_get_contents(__DIR__ . "/fixtures/expected4.json");

        // формат json для файлов json
        $file5 = __DIR__ . "/fixtures/file5.json";
        $file6 = __DIR__ . "/fixtures/file6.json";
        $this->assertEquals($expected4, genDiff($file5, $file6, 'json'));

        // формат json для файлов yaml
        $file7 = __DIR__ . "/fixtures/file7.yaml";
        $file8 = __DIR__ . "/fixtures/file8.yaml";
        $this->assertEquals($expected4, genDiff($file7, $file8, 'json'));

        echo "\n\033[42mJsonFormat Tests passed!\033[0m\n";
    }
}
