<?php

namespace Differ\Formatters\Plain;

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

function toPlain(array $array, &$resultArray = [], &$propertyPath = []): string
{
    foreach ($array as $arrayKey => $arrayValue) {
        $propertyPath[] = $arrayKey;

        if (!array_key_exists("status", $arrayValue)) {
            toPlain($arrayValue, $resultArray, $propertyPath);
            array_pop($propertyPath);
            continue;
        }

        // если свойство есть в обоих массивах с одинаковыми значениями
        if ($arrayValue["status"] === "unchanged") {
            array_pop($propertyPath);
            continue;

        // если свойство есть в обоих массивах, но с разными значениями
        } elseif ($arrayValue["status"] === "updated") {
            $value1 = isComplexValue($arrayValue["value1"]);
            $value2 = isComplexValue($arrayValue["value2"]);
            $path = implode(".", $propertyPath);
            $string = "Property '{$path}' was updated. From {$value1} to {$value2}";
            // чтобы не было дублирования строк
            if (!in_array($string, $resultArray)) {
                $resultArray[] = $string;
            }

        // если значение есть только в первом массиве
        } elseif ($arrayValue["status"] === "removed") {
            $path = implode(".", $propertyPath);
            $resultArray[] = "Property '{$path}' was removed";

        // если значение есть только во втором массиве
        } elseif ($arrayValue["status"] === "added") {
            $path = implode(".", $propertyPath);
            $value = isComplexValue($arrayValue["value"]);
            $resultArray[] = "Property '{$path}' was added with value: {$value}";
        }
        array_pop($propertyPath);
    }

    return implode(PHP_EOL, $resultArray);
}
