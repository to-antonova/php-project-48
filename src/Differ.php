<?php

namespace Differ\Differ;

use function Differ\Parsers\turnIntoArray;
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

function genDiff(string $pathToFirstFile, string $pathToSecondFile, string $formatType = 'stylish')
{
    $arrayFirstFile = turnIntoArray($pathToFirstFile);
    $arraySecondFile = turnIntoArray($pathToSecondFile);
    $arrayDiff = findArrayDiff($arrayFirstFile, $arraySecondFile);
    return format($arrayDiff, $formatType);
}
