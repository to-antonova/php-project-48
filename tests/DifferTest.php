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
        $firstFile = $this->getFixturePath($firstFilePath);
        $secondFile = $this->getFixturePath($secondFilePath);
        $expected = $this->getFixturePath($expectedFilePath);
        $actual = genDiff($firstFile, $secondFile, $format);
        $this->assertStringEqualsFile($expected, $actual);
    }

    private function getFixturePath(string $path)
    {
        return __DIR__ . "/fixtures/" . $path;
    }

    public static function genDiffDataProvider(): array
    {
        return [
            'diff json format files' => [
                'expectedFilePath' => "expected1.txt",
                'firstFilePath' => "file1.json",
                'secondFilePath' => "file2.json",
                'format' => "stylish"
            ],
            'diff yml format files' => [
                'expectedFilePath' => "expected1.txt",
                'firstFilePath' => "file3.yml",
                'secondFilePath' => "file4.yml",
                'format' => "stylish"
            ],
            'diff json format files recursive' => [
                'expectedFilePath' => "expected2.txt",
                'firstFilePath' => "file5.json",
                'secondFilePath' => "file6.json",
                'format' => "stylish"
            ],
            'diff yml format files recursive' => [
                'expectedFilePath' => "expected2.txt",
                'firstFilePath' => "file7.yaml",
                'secondFilePath' => "file8.yaml",
                'format' => "stylish"
            ],
            'diff json format files plain' => [
                'expectedFilePath' => "expected3.txt",
                'firstFilePath' => "file5.json",
                'secondFilePath' => "file6.json",
                'format' => "plain"
            ],
            'diff yml format files plain' => [
                'expectedFilePath' => "expected3.txt",
                'firstFilePath' => "file7.yaml",
                'secondFilePath' => "file8.yaml",
                'format' => "plain"
            ],
            'diff json format files output json' => [
                'expectedFilePath' => "expected4.json",
                'firstFilePath' => "file5.json",
                'secondFilePath' => "file6.json",
                'format' => "json"
            ],
            'diff yml format files output json' => [
                'expectedFilePath' => "expected4.json",
                'firstFilePath' => "file7.yaml",
                'secondFilePath' => "file8.yaml",
                'format' => "json"
            ]
        ];
    }
}
