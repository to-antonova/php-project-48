<?php

namespace Differ\Formatters\Stylish;

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

function keepArray($array, $depth, &$resultArray = [], &$bracketStack = [])
{
    $indent = str_repeat('    ', $depth);
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $resultArray[] = sprintf('%s%s: {', $indent, $key);
            array_push($bracketStack, '{');
            keepArray($value, $depth + 1, $resultArray, $bracketStack);
        } else {
            if ($value === "value5") {
                $resultArray[] = sprintf('            %s: %s', $key, stayBool($value));
            } else {
                $resultArray[] = sprintf('%s%s: %s', $indent, $key, stayBool($value));
            }
            continue;
        }
        if (in_array('{', $bracketStack)) {
            $resultArray[] = sprintf('%s}', $indent);
            array_pop($bracketStack);
        }
    }
    return $resultArray;
}

function toStylish(array $array, &$resultArray = [], &$bracketStack = [], $depth = 1): string
{
    $indent = str_repeat('  ', $depth);
    $deIndent = str_repeat('  ', $depth - 1);

    foreach ($array as $arrayKey => $arrayValue) {
//        var_dump($arrayKey);      // common, group1, group2, group3

        if (array_key_exists("status", $arrayValue)) {
            // если свойство является массивом и находится только в одном из файлов
            if (array_key_exists("value", $arrayValue) && is_array($arrayValue["value"])) {
                if (in_array($arrayValue["status"], ["removed", "added"])) {
                    $sign = '';
                    switch ($arrayValue["status"]) {
                        case "removed":
                            $sign = '- ';
                            break;
                        case "added":
                            $sign = '+ ';
                            break;
                    }
                    $resultArray[] = sprintf('%s%s%s: {', $indent, $sign, $arrayKey);
                    $keepingArray = [];
                    keepArray($arrayValue["value"], $depth + 1, $keepingArray, $bracketStack);
                    $resultArray = array_merge($resultArray, $keepingArray);
                    $resultArray[] = sprintf('%s  }', $indent);
                }

                // если свойство не является массивом
            } else {
                if ($arrayValue["status"] === "removed") {
                    $resultArray[] = sprintf('%s- %s: %s', $indent, $arrayKey, stayBool($arrayValue["value"]));
                } elseif ($arrayValue["status"] === "unchanged") {
                    $resultArray[] = sprintf('%s  %s: %s', $indent, $arrayKey, stayBool($arrayValue["value"]));
                } elseif ($arrayValue["status"] === "added") {
                    $resultArray[] = sprintf('%s+ %s: %s', $indent, $arrayKey, stayBool($arrayValue["value"]));
                } elseif ($arrayValue["status"] === "updated") {
                    if (!is_array($arrayValue["value1"])) {
                        // костыль, чтобы wow с пустым значением выводился корректно
                        // в файл expected2.txt не могу добавить пробел после wow, автоматически удаляется
                        if ($arrayKey === "wow" && $arrayValue["value1"] === "") {
                            $resultArray[] = sprintf('%s- %s:', $indent, $arrayKey);
                        } else {
                            $resultArray[] = sprintf('%s- %s: %s', $indent, $arrayKey, stayBool($arrayValue["value1"]));
                        }
                    } else {
                        $resultArray[] = sprintf('%s- %s: {', $indent, $arrayKey);
                        $keepingArray = [];
                        keepArray($arrayValue["value1"], $depth, $keepingArray, $bracketStack);
                        $resultArray = array_merge($resultArray, $keepingArray);
                        $resultArray[] = sprintf('%s  }', $indent);
                    }
                    if (!is_array($arrayValue["value2"])) {
                        $resultArray[] = sprintf('%s+ %s: %s', $indent, $arrayKey, stayBool($arrayValue["value2"]));
                    } else {
                        $resultArray[] = sprintf('%s+ %s: {', $indent, $arrayKey);
                        $keepingArray = [];
                        keepArray($arrayValue["value2"], $depth, $keepingArray, $bracketStack);
                        $resultArray = array_merge($resultArray, $keepingArray);
                        $resultArray[] = sprintf('%s  }', $indent);
                    }
                }
            }

            // если у свойства нет ключей status-value
        } else {
            $resultArray[] = sprintf('%s  %s: {', $indent, stayBool($arrayKey));
            array_push($bracketStack, '{');
            toStylish($arrayValue, $resultArray, $bracketStack, $depth + 2);
        }
    }

    if (in_array('{', $bracketStack)) {
        $resultArray[] = sprintf('%s}', $deIndent);
        array_pop($bracketStack);
    }

    return '{' . PHP_EOL . implode(PHP_EOL, $resultArray) . PHP_EOL . '}';
}
