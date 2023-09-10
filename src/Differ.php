<?php

namespace Differ\Differ;

use Exception;

use function Differ\Parsers\convertToArray;
use function Differ\Formatters\format;
use function Functional\sort;

function findArrayDiff(array $arrayFirstFile, array $arraySecondFile): array
{
    $mergedKeys = array_keys(array_merge($arrayFirstFile, $arraySecondFile));
    $sortedKeys = sort($mergedKeys, fn ($left, $right) => strcmp($left, $right));
    return array_map(function ($key) use ($arrayFirstFile, $arraySecondFile) {
        if (!array_key_exists($key, $arrayFirstFile)) {
            return ['key' => $key, 'status' => 'added', 'value' => $arraySecondFile[$key]];
        } elseif (!array_key_exists($key, $arraySecondFile)) {
            return ['key' => $key, 'status' => 'removed', 'value' => $arrayFirstFile[$key]];
        }
        if (is_array($arrayFirstFile[$key]) && is_array($arraySecondFile[$key])) {
            $children = findArrayDiff($arrayFirstFile[$key], $arraySecondFile[$key]);
            return ['key' => $key, 'status' => 'changed', 'children' => $children];
        }
        if ($arrayFirstFile[$key] === $arraySecondFile[$key]) {
            return  ['key' => $key, 'status' => 'unchanged', 'value' => $arrayFirstFile[$key]];
        } else {
            return [
                'key' => $key,
                'status' => 'updated',
                'oldValue' => $arrayFirstFile[$key],
                'newValue' => $arraySecondFile[$key]
            ];
        }
    }, $sortedKeys);
}

function getFileContent(string $pathToFile): array
{
    $fileContent = (string) file_get_contents($pathToFile);
    return convertToArray($pathToFile, $fileContent);
}

/**
 * @throws Exception
 */
function genDiff(string $pathToFirstFile, string $pathToSecondFile, string $formatType = 'stylish')
{
    if ($pathToFirstFile == "") {
        throw new Exception('First file path error');
    }

    if ($pathToSecondFile == "") {
        throw new Exception('Second file path error');
    }

    $arrayFirstFile = getFileContent($pathToFirstFile);
    $arraySecondFile = getFileContent($pathToSecondFile);
    $arrayDiff = findArrayDiff($arrayFirstFile, $arraySecondFile);
    return format($arrayDiff, $formatType);
}
