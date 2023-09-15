<?php

namespace Differ\Differ;

use Exception;

use function Differ\Parsers\convertToArray;
use function Differ\Formatters\formatOutput;
use function Functional\sort;

function findArrayDiff(array $arrayFirstFile, array $arraySecondFile): array
{
    $mergedKeys = array_keys(array_merge($arrayFirstFile, $arraySecondFile));
    $sortedKeys = sort($mergedKeys, fn ($left, $right) => strcmp($left, $right));

    return array_map(function ($key) use ($arrayFirstFile, $arraySecondFile) {
        if (!array_key_exists($key, $arrayFirstFile)) {
            return ['key' => $key, 'status' => 'added', 'value' => $arraySecondFile[$key]];
        }

        if (!array_key_exists($key, $arraySecondFile)) {
            return ['key' => $key, 'status' => 'removed', 'value' => $arrayFirstFile[$key]];
        }

        if (is_array($arrayFirstFile[$key]) && is_array($arraySecondFile[$key])) {
            $children = findArrayDiff($arrayFirstFile[$key], $arraySecondFile[$key]);
            return ['key' => $key, 'status' => 'changed', 'children' => $children];
        }

        if ($arrayFirstFile[$key] === $arraySecondFile[$key]) {
            return  ['key' => $key, 'status' => 'unchanged', 'value' => $arrayFirstFile[$key]];
        }

        return [
            'key' => $key,
            'status' => 'updated',
            'oldValue' => $arrayFirstFile[$key],
            'newValue' => $arraySecondFile[$key]
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
    $firstFile = getFileContent($pathToFirstFile);
    $secondFile = getFileContent($pathToSecondFile);
    $diff = findArrayDiff($firstFile, $secondFile);

    return formatOutput($diff, $formatType);
}
