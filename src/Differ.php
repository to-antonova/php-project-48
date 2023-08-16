<?php

namespace Differ\Differ;

use function Differ\Parsers\turnIntoArray;
use function Differ\Formatters\format;

//use function Differ\Formatters\plain\plain;

const NO_DIFF = '0_NO_DIFF';
const DIFF_FIRST = '1_DIFF_FIRST';
const DIFF_SECOND = '2_DIFF_SECOND';

function mySort(array &$arr)
{
    ksort($arr);
    foreach ($arr as &$v) {
        if (is_array($v)) {
            mySort($v);
        }
    }
}



function findArrayDiffRecursive($arrayFirstFile, $arraySecondFile): array
{
    $result = [];

    foreach ($arrayFirstFile as $key => $value) {
        if (is_array($arraySecondFile)) {
            if (array_key_exists($key, $arraySecondFile)) {
                if (is_array($value) && is_array($arraySecondFile[$key])) {
                    $result[$key][NO_DIFF] = findArrayDiffRecursive($value, $arraySecondFile[$key]);
                } elseif (!is_array($value) && !is_array($arraySecondFile[$key]) && $value === $arraySecondFile[$key]) {
                    $result[$key][NO_DIFF] = $value;
                } else {
                    $result[$key][DIFF_FIRST] = is_array($value) ? recursiveDiff($value) : $value;
                    $result[$key][DIFF_SECOND] = is_array($arraySecondFile[$key]) ?
                        recursiveDiff($arraySecondFile[$key]) : $arraySecondFile[$key];
                }
            } else {
                $result[$key][DIFF_FIRST] = is_array($value) ? recursiveDiff($value) : $value;
            }
        } else {
            if (is_array($value)) {
                $result[$key][DIFF_FIRST] = recursiveDiff($value);
                $result[$key][DIFF_SECOND] = $arraySecondFile;
            } else {
                if ($value === $arraySecondFile[$key]) {
                    $result[$key][NO_DIFF] = $value;
                } else {
                    $result[$key][DIFF_FIRST] = $value;
                    $result[$key][DIFF_SECOND] = $arraySecondFile[$key];
                }
            }
        }
    }

    foreach ($arraySecondFile as $key => $value) {
        if (is_array($arrayFirstFile)) {
            if (!array_key_exists($key, $arrayFirstFile)) {
                $result[$key][DIFF_SECOND] = is_array($arraySecondFile[$key]) ?
                    recursiveDiff($arraySecondFile[$key]) : $arraySecondFile[$key];
            }
        }
    }

    return $result;
}

function recursiveDiff(array $input): array
{
    $out = [];
    foreach ($input as $key => $value) {
        if (is_array($value)) {
            $out[$key] = recursiveDiff($value);
            continue;
        }
        $out[$key] = $value;
    }

    return $out;
}

function genDiff($pathToFirstFile, $pathToSecondFile, $formatType = 'stylish')
{
    $arrayFirstFile = turnIntoArray($pathToFirstFile);
    $arraySecondFile = turnIntoArray($pathToSecondFile);
    $resultArray = findArrayDiffRecursive($arrayFirstFile, $arraySecondFile);
    mySort($resultArray);
//    var_dump($format($resultArray));
    return format($resultArray, $formatType);
//    var_dump($resultArray);
//    return plain($resultArray);   // работает
}
