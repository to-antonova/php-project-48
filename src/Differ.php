<?php

namespace Differ\Differ;

use function Differ\Parsers\turnIntoArray;
use function Differ\Formatters\format;

function cmp(array $a, array $b)
{
    if ($a['key'] == $b['key']) {
        return 0;
    }
    return ($a['key'] < $b['key']) ? -1 : 1;
}

function mySort(array $a)
{
    $newA = $a;
    usort($newA, 'Differ\Differ\cmp');
    return array_map(function ($v) {
        $newV = $v;
        if (array_key_exists('children', $newV)) {
            $newV['children'] = mySort($v['children']);
        }
        return $newV;
    }, $newA);
}

function findArrayDiff(array $arrayFirstFile, array $arraySecondFile): array
{
    $mergedKeys = array_keys(array_merge($arrayFirstFile, $arraySecondFile));
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
    }, $mergedKeys);
}

function genDiff(string $pathToFirstFile, string $pathToSecondFile, string $formatType = 'stylish')
{
    $arrayFirstFile = turnIntoArray($pathToFirstFile);
    $arraySecondFile = turnIntoArray($pathToSecondFile);
    $arrayDiff = findArrayDiff($arrayFirstFile, $arraySecondFile);
    $sortedArrayDiff = mySort($arrayDiff);
    return format($sortedArrayDiff, $formatType);
}
