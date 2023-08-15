<?php

namespace Differ\Formatters\plain;

const NO_DIFF = '0_NO_DIFF';
const DIFF_FIRST = '1_DIFF_FIRST';
const DIFF_SECOND = '2_DIFF_SECOND';

use function Differ\Differ\stayBool;

function isComplexValue($value)
{
    if (is_array($value)) {
        return "[complex value]";
    } elseif (is_string($value)) {
        if ($value === "null") {
            return "null";
        } elseif ($value === "true") {
            return "true";
        } elseif ($value === "false") {
            return "false";
        } else {
            return "'$value'";
        }
    } else {
        return stayBool($value);
    }
}


function plain(array $array, &$resultArray = [], &$propertyPath = []): string
{
    foreach ($array as $arrayKey => $arrayValue) {
//        var_dump($arrayKey);      // common, group1, group2, group3
        $propertyPath[] = $arrayKey;
//        var_dump($propertyPath);

        if (is_array($arrayValue)) {
            foreach ($arrayValue as $arrayValueKey => $arrayValueValue) {
//                var_dump($arrayValueKey);       // NO_DIFF, DIFF_FIRST, DIFF_SECOND  в изначальном массиве

                if (count($arrayValue) === 1) {
                    if ($arrayValueKey === DIFF_FIRST) {
                        $path = implode(".", $propertyPath);
                        $resultArray[] = "Property '{$path}' was removed";
                        continue;
                    }
                    if ($arrayValueKey === DIFF_SECOND) {
                        $path = implode(".", $propertyPath);
                        $value = isComplexValue($arrayValueValue);
                        $resultArray[] = "Property '{$path}' was added with value: {$value}";
                        continue;
                    }
                }

                if (is_array($arrayValueValue)) {
                    plain($arrayValueValue, $resultArray, $propertyPath);
                    continue;
                }

                // если свойство есть в обоих массивах с одинаковыми значениями
                if ($arrayValueKey === NO_DIFF) {
                    continue;
                }

                // если свойство есть в обоих массивах, но с разными значениями
                if (array_key_exists(DIFF_FIRST, $arrayValue) && array_key_exists(DIFF_SECOND, $arrayValue)) {
                    $value1 = isComplexValue($arrayValue[DIFF_FIRST]);
                    $value2 = isComplexValue($arrayValue[DIFF_SECOND]);
                    $path = implode(".", $propertyPath);
                    $string = "Property '{$path}' was updated. From {$value1} to {$value2}";
                    // чтобы не было дублирования строк
                    if (!in_array($string, $resultArray)) {
                        $resultArray[] = $string;
                    }

                // если значение есть только в первом массиве
                } elseif (!array_key_exists(DIFF_SECOND, $arrayValue)) {
                    $path = implode(".", $propertyPath);
                    $resultArray[] = "Property '{$path}' was removed";

                // если значение есть только во втором массиве
                } elseif (!array_key_exists(DIFF_FIRST, $arrayValue)) {
                    $path = implode(".", $propertyPath);
                    $value = isComplexValue($arrayValueValue);
                    $resultArray[] = "Property '{$path}' was added with value: {$value}";
                }
            }
        }
        array_pop($propertyPath);
    }

    return '{' . PHP_EOL . implode(PHP_EOL, $resultArray) . PHP_EOL . '}';
}
