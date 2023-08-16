<?php

namespace Differ\Formatters\Stylish;

const NO_DIFF = '0_NO_DIFF';
const DIFF_FIRST = '1_DIFF_FIRST';
const DIFF_SECOND = '2_DIFF_SECOND';

function stayBool($value)
{
    if ($value === null) {
        return "null";
    }

    if ($value === true) {
        return "true";
    }

    if ($value === false) {
        return "false";
    } else {
        return $value;
    }
}

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

function toStylish(array $array, &$resultArray = [], $depth = 1): string
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
                    $resultArray[] = sprintf('%s%s%s: {', $indent, $sign, stayBool($arrayKey));
                    if (!isDiffKey($arrayValueKey)) {
                        $resultArray[] = sprintf('%s%s%s: {', $indent, $indent, stayBool($arrayValueKey));
                        foreach ($arrayValueValue as $k => $v) {
                            if (!is_array($v)) {
                                $resultArray[] = sprintf(
                                    '%s%s%s: %s',
                                    $currentIndent,
                                    $currentIndent,
                                    $k,
                                    stayBool($v)
                                );
                            } else {
                                toStylish($v, $resultArray, $depth + 3);
                            }
                        }
                        $resultArray[] = "{$currentIndent}    }";       // здесь вместо sign 4 пробела
                        $resultArray[] = "{$currentIndent}}";
                        continue;
                    }
                    toStylish($arrayValueValue, $resultArray, $depth + 2);
                } else {
                    if (isDiffKey($arrayValueKey)) {
                        if ($arrayValueValue === "") {
                            // костыль, чтобы wow с пустым значением выводился корректно
                            // в файл expected2.txt не могу добавить пробел после wow, автоматически удаляется
                            $resultArray[] = sprintf('%s%s%s:', $indent, $sign, stayBool($arrayKey));
                            continue;
                        }
                        $resultArray[] = sprintf('%s%s%s: %s', $indent, $sign, $arrayKey, stayBool($arrayValueValue));
                        continue;
                    } else {
                        $resultArray[] = sprintf('%s%s%s: {', $indent, $sign, stayBool($arrayKey));
                        $resultArray[] = sprintf(
                            '%s%s%s: %s',
                            $indent,
                            $indent,
                            $arrayValueKey,
                            stayBool($arrayValueValue)
                        );
                    }
                }

                $resultArray[] = "{$currentIndent}}";
            }
        } else {
            $resultArray[] = sprintf('%s%s: %s', $currentIndent, $arrayKey, $arrayValue);
        }
    }

    return '{' . PHP_EOL . implode(PHP_EOL, $resultArray) . PHP_EOL . '}';
}
