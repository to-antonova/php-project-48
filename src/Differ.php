<?php

namespace Differ\Differ;

use function Differ\Formatters\Plain\toPlain;
use function Differ\Formatters\Stylish\toStylish;
use function Differ\Parsers\turnIntoArray;
use function Differ\Formatters\format;

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

/////////////////////////////////////////////////////////////////////////////////////
function formattish(array $array, &$resultArray = [])
{
    foreach ($array as $arrayKey => $arrayValue) {
//        var_dump($arrayKey);      // common, group1, group2, group3

        if (is_array($arrayValue)) {
            foreach ($arrayValue as $arrayValueKey => $arrayValueValue) {
//                var_dump($arrayValueKey);       // NO_DIFF, DIFF_FIRST, DIFF_SECOND  в изначальном массиве

                if (count($arrayValue) === 1) {     // сделать switch
                    if ($arrayValueKey === DIFF_FIRST) {
                        $resultArray[$arrayKey]["status"] = "removed";
                        $resultArray[$arrayKey]["value"] = $arrayValueValue;
                        continue;
                    }
                    if ($arrayValueKey === DIFF_SECOND) {
                        $resultArray[$arrayKey]["status"] = "added";
                        $resultArray[$arrayKey]["value"] = $arrayValueValue;
                        continue;
                    }
                }


                if (is_array($arrayValueValue)) {
                    $resultArray[$arrayKey] = [];
                    formattish($arrayValueValue, $resultArray[$arrayKey]);
                    continue;
                }

                // если свойство есть в обоих массивах с одинаковыми значениями
                if ($arrayValueKey === NO_DIFF) {
                    $resultArray[$arrayKey]["status"] = "unchanged";
                    $resultArray[$arrayKey]["value"] = $arrayValueValue;
                    continue;
                }

                // если свойство есть в обоих массивах, но с разными значениями
                if (array_key_exists(DIFF_FIRST, $arrayValue) && array_key_exists(DIFF_SECOND, $arrayValue)) {
                    $resultArray[$arrayKey]["status"] = "updated";
                    $resultArray[$arrayKey]["value1"] = $arrayValue[DIFF_FIRST];
                    $resultArray[$arrayKey]["value2"] = $arrayValue[DIFF_SECOND];

                    // если значение есть только в первом массиве
                } elseif (!array_key_exists(DIFF_SECOND, $arrayValue)) {
                    $resultArray[$arrayKey]["status"] = "removed";

                    // если значение есть только во втором массиве
                } elseif (!array_key_exists(DIFF_FIRST, $arrayValue)) {
                    $resultArray[$arrayKey]["status"] = "added";
                    $resultArray[$arrayKey]["value"] = $arrayValueValue;
                }
            }
        }
    }
    return $resultArray;
}

function genDiff($pathToFirstFile, $pathToSecondFile, $formatType = 'stylish')
{
    $arrayFirstFile = turnIntoArray($pathToFirstFile);
    $arraySecondFile = turnIntoArray($pathToSecondFile);
    $resultArray = findArrayDiffRecursive($arrayFirstFile, $arraySecondFile);

    $result = formattish($resultArray);
    mySort($result);
    return format($result, $formatType);
}
