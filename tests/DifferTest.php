<?php

namespace Differ\Tests;

use Exception;
use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    /**
     * @param string $firstFilePath
     * @param string $secondFilePath
     * @param string $format
     * @param string $expectedFilePath
     * @throws Exception
     * @dataProvider genDiffDataProvider
     */
    public function testGenDiff(string $expectedFilePath, string $firstFilePath, string $secondFilePath, string $format)
    {
        $firstFile = __DIR__ . $firstFilePath;
        $secondFile = __DIR__ . $secondFilePath;
        $expected = __DIR__ . $expectedFilePath;
        $actual = genDiff($firstFile, $secondFile, $format);
        $this->assertStringEqualsFile($expected, $actual);
    }

    public static function genDiffDataProvider(): array
    {
        return [
            'diff json format files' => [
                'expectedFilePath' => "/fixtures/expected1.txt",
                'firstFilePath' => "/fixtures/file1.json",
                'secondFilePath' => "/fixtures/file2.json",
                'format' => "stylish"
            ],
            'diff yml format files' => [
                'expectedFilePath' => "/fixtures/expected1.txt",
                'firstFilePath' => "/fixtures/file3.yml",
                'secondFilePath' => "/fixtures/file4.yml",
                'format' => "stylish"
            ],
            'diff json format files recursive' => [
                'expectedFilePath' => "/fixtures/expected2.txt",
                'firstFilePath' => "/fixtures/file5.json",
                'secondFilePath' => "/fixtures/file6.json",
                'format' => "stylish"
            ],
            'diff yml format files recursive' => [
                'expectedFilePath' => "/fixtures/expected2.txt",
                'firstFilePath' => "/fixtures/file7.yaml",
                'secondFilePath' => "/fixtures/file8.yaml",
                'format' => "stylish"
            ],
            'diff json format files plain' => [
                'expectedFilePath' => "/fixtures/expected3.txt",
                'firstFilePath' => "/fixtures/file5.json",
                'secondFilePath' => "/fixtures/file6.json",
                'format' => "plain"
            ],
            'diff yml format files plain' => [
                'expectedFilePath' => "/fixtures/expected3.txt",
                'firstFilePath' => "/fixtures/file7.yaml",
                'secondFilePath' => "/fixtures/file8.yaml",
                'format' => "plain"
            ],
            'diff json format files output json' => [
                'expectedFilePath' => "/fixtures/expected4.json",
                'firstFilePath' => "/fixtures/file5.json",
                'secondFilePath' => "/fixtures/file6.json",
                'format' => "json"
            ],
            'diff yml format files output json' => [
                'expectedFilePath' => "/fixtures/expected4.json",
                'firstFilePath' => "/fixtures/file7.yaml",
                'secondFilePath' => "/fixtures/file8.yaml",
                'format' => "json"
            ]
        ];
    }
}
