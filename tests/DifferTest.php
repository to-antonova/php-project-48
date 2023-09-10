<?php

namespace Differ\Tests;

use Exception;
use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    /**
     * @param string $firstFile
     * @param string $secondFile
     * @param string $format
     * @param string $expected
     * @throws Exception
     * @dataProvider genDiffDataProvider
     */
    public function testGenDiff(string $expected, string $firstFile, string $secondFile, string $format = 'stylish')
    {
        $actual = genDiff($firstFile, $secondFile, $format);
        $this->assertEquals($expected, $actual);
    }

    public static function genDiffDataProvider(): array
    {
        $expectedFlat  = file_get_contents(__DIR__ . "/fixtures/expected1.txt");
        $expectedRecursive  = file_get_contents(__DIR__ . "/fixtures/expected2.txt");
        $expectedPlain = file_get_contents(__DIR__ . "/fixtures/expected3.txt");
        $expectedOutputJson = file_get_contents(__DIR__ . "/fixtures/expected4.json");
        return [
            'diff json format files' => [
                'expected' => $expectedFlat,
                'firstFile' =>  __DIR__ . "/fixtures/file1.json",
                'secondFile' =>  __DIR__ . "/fixtures/file2.json"
            ],
            'diff yml format files' => [
                'expected' => $expectedFlat,
                'firstFile' =>  __DIR__ . "/fixtures/file3.yml",
                'secondFile' =>  __DIR__ . "/fixtures/file4.yml"
            ],
            'diff json format files recursive' => [
                'expected' => $expectedRecursive,
                'firstFile' =>  __DIR__ . "/fixtures/file5.json",
                'secondFile' =>  __DIR__ . "/fixtures/file6.json"
            ],
            'diff yml format files recursive' => [
                'expected' => $expectedRecursive,
                'firstFile' =>  __DIR__ . "/fixtures/file7.yaml",
                'secondFile' =>  __DIR__ . "/fixtures/file8.yaml"
            ],
            'diff json format files plain' => [
                'expected' => $expectedPlain,
                'firstFile' =>  __DIR__ . "/fixtures/file5.json",
                'secondFile' =>  __DIR__ . "/fixtures/file6.json",
                'format' => "plain"
            ],
            'diff yml format files plain' => [
                'expected' => $expectedPlain,
                'firstFile' =>  __DIR__ . "/fixtures/file7.yaml",
                'secondFile' =>  __DIR__ . "/fixtures/file8.yaml",
                'format' => "plain"
            ],
            'diff json format files output json' => [
                'expected' => $expectedOutputJson,
                'firstFile' =>  __DIR__ . "/fixtures/file5.json",
                'secondFile' =>  __DIR__ . "/fixtures/file6.json",
                'format' => "json"
            ],
            'diff yml format files output json' => [
                'expected' => $expectedOutputJson,
                'firstFile' =>  __DIR__ . "/fixtures/file7.yaml",
                'secondFile' =>  __DIR__ . "/fixtures/file8.yaml",
                'format' => "json"
            ]
        ];
    }
}
