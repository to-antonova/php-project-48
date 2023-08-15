<?php

namespace Differ\Formatters\stylish;

//use function Differ\Differ\stayBool;

const NO_DIFF = '0_NO_DIFF';
const DIFF_FIRST = '1_DIFF_FIRST';
const DIFF_SECOND = '2_DIFF_SECOND';

function chooseSign($value): string
{
    switch ($value) {
        case "1_DIFF_FIRST":
            $sign = '- ';
            break;
        case "2_DIFF_SECOND":
            $sign = '+ ';
            break;
        case "0_NO_DIFF":
            $sign = '  ';       // для наглядности заменить пробелы на '0 '
            break;
        default:
            $sign = '  ';
    }
    return $sign;
}

function isDiffKey($key): bool
{
    return in_array($key, [ NO_DIFF, DIFF_SECOND, DIFF_FIRST]);
}

function getIndent($depth): string
{
    return str_repeat('  ', $depth);        // для наглядности можно заменить пробелы точками
}

function stylish(array $array, &$resultArray = [], $depth = 1): string
{
    $indent = getIndent($depth);
    $currentIndent = getIndent($depth + 1);

    foreach ($array as $arrayKey => $arrayValue) {
//        var_dump($arrayKey);      // common, group1, group2, group3

        if (is_array($arrayValue)) {
            foreach ($arrayValue as $arrayValueKey => $arrayValueValue) {
//                var_dump($arrayValueKey);       // NO_DIFF, DIFF_FIRST, DIFF_SECOND  в изначальном массиве

                $sign = chooseSign($arrayValueKey);

                if (is_array($arrayValueValue)) {
                    $resultArray[] = "{$indent}{$sign}{$arrayKey}: {";
                    if (!isDiffKey($arrayValueKey)) {
                        $resultArray[] = "{$indent}{$indent}{$arrayValueKey}: {";
                        foreach ($arrayValueValue as $k => $v) {
                            if (!is_array($v)) {
                                $resultArray[] = "{$currentIndent}{$currentIndent}{$k}: {$v}";
                            } else {
                                stylish($v, $resultArray, $depth + 3);
                            }
                        }
                        $resultArray[] = "{$currentIndent}    }";       // здесь вместо sign 4 пробела
                        $resultArray[] = "{$currentIndent}}";
                        continue;
                    }
                    stylish($arrayValueValue, $resultArray, $depth + 2);

                } else {
                    if (isDiffKey($arrayValueKey)) {
                        if ($arrayValueValue === "") {
                            // костыль, чтобы wow с пустым значением выводился корректно
                            // в файл expected2.txt не могу добавить пробел после wow, автоматически удаляется
                            $resultArray[] = "{$indent}{$sign}{$arrayKey}:";
                            continue;
                        }
                        $resultArray[] = "{$indent}{$sign}{$arrayKey}: {$arrayValueValue}";
                        continue;
                    } else {
                        $resultArray[] = "{$indent}{$sign}{$arrayKey}: {";
                        $resultArray[] = "{$indent}{$indent}{$arrayValueKey}: {$arrayValueValue}";
                    }
                }

                $resultArray[] = "{$currentIndent}}";
            }
        } else {
            $resultArray[] = "{$currentIndent}{$arrayKey}: {$arrayValue}";
        }
    }

    return '{' . PHP_EOL . implode(PHP_EOL, $resultArray) . PHP_EOL . '}';
}
