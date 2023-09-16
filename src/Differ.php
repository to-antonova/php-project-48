<?php

namespace Differ\Differ;

use Exception;

use function Differ\Parsers\convertToArray;
use function Differ\Formatters\formatOutput;
use function Functional\sort;

function findDiff(array $firstDataStructure, array $secondDataStructure): array
{
    $mergedKeys = array_keys(array_merge($firstDataStructure, $secondDataStructure));
    $sortedKeys = sort($mergedKeys, fn ($left, $right) => strcmp($left, $right));

    return array_map(function ($key) use ($firstDataStructure, $secondDataStructure) {
        if (!array_key_exists($key, $firstDataStructure)) {
            return ['key' => $key, 'status' => 'added', 'value' => $secondDataStructure[$key]];
        }

        if (!array_key_exists($key, $secondDataStructure)) {
            return ['key' => $key, 'status' => 'removed', 'value' => $firstDataStructure[$key]];
        }

        if (is_array($firstDataStructure[$key]) && is_array($secondDataStructure[$key])) {
            $children = findDiff($firstDataStructure[$key], $secondDataStructure[$key]);
            return ['key' => $key, 'status' => 'has children', 'children' => $children];
        }

        if ($firstDataStructure[$key] === $secondDataStructure[$key]) {
            return  ['key' => $key, 'status' => 'unchanged', 'value' => $firstDataStructure[$key]];
        }

        return [
            'key' => $key,
            'status' => 'updated',
            'oldValue' => $firstDataStructure[$key],
            'newValue' => $secondDataStructure[$key]
        ];
    }, $sortedKeys);
}

function getFileContent(string $pathToFile): array
{
    $realPathToFile = realpath($pathToFile);
    if ($realPathToFile == "") {
        throw new Exception('File not found, path to file: ' . $pathToFile);
    }

    $fileContent = file_get_contents($pathToFile);
    if ($fileContent === false) {
        throw new Exception('Cannot read the file');
    }

    $fileExtension = pathinfo($pathToFile, PATHINFO_EXTENSION);

    return convertToArray($fileExtension, $fileContent);
}

function genDiff(string $pathToFirstFile, string $pathToSecondFile, string $formatType = 'stylish')
{
    $firstDataStructure = getFileContent($pathToFirstFile);
    $secondDataStructure = getFileContent($pathToSecondFile);
    $diff = findDiff($firstDataStructure, $secondDataStructure);

    return formatOutput($diff, $formatType);
}
