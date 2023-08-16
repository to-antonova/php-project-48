<?php

namespace Differ\Formatters\Json;

const NO_DIFF = '0_NO_DIFF';
const DIFF_FIRST = '1_DIFF_FIRST';
const DIFF_SECOND = '2_DIFF_SECOND';

function toJson(array $array, &$resultArray = []): string
{
    foreach ($array as $arrayKey => $arrayValue) {
//        var_dump($arrayKey);      // common, group1, group2, group3

        if (is_array($arrayValue)) {
            foreach ($arrayValue as $arrayValueKey => $arrayValueValue) {
//                var_dump($arrayValueKey);       // NO_DIFF, DIFF_FIRST, DIFF_SECOND  в изначальном массиве

                if (count($arrayValue) === 1) {     // сделать switch
                    if ($arrayValueKey === DIFF_FIRST) {
                        $resultArray[$arrayKey]["status"] = "removed";
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
                    toJson($arrayValueValue, $resultArray[$arrayKey]);
                    continue;
                }

                // если свойство есть в обоих массивах с одинаковыми значениями
                if ($arrayValueKey === NO_DIFF) {
                    continue;
                }

                // если свойство есть в обоих массивах, но с разными значениями
                if (array_key_exists(DIFF_FIRST, $arrayValue) && array_key_exists(DIFF_SECOND, $arrayValue)) {
                    $resultArray[$arrayKey]["status"] = "updated";
                    $resultArray[$arrayKey]["oldvalue"] = $arrayValue[DIFF_FIRST];
                    $resultArray[$arrayKey]["newvalue"] = $arrayValue[DIFF_SECOND];

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

    return json_encode($resultArray, JSON_PRETTY_PRINT);    // ориентир
}
