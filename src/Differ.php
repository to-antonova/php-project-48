<?php

namespace Differ\Differ;

use function Differ\Parsers\turnIntoArray;

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

function findArrayDiff($arrayFirstFile, $arraySecondFile): array
{

    $unionArray = [];

    foreach ($arrayFirstFile as $key => $value) {
        if (!array_key_exists($key, $arraySecondFile)) {
            $unionArray[] = "  - {$key}: " . stayBool($value);
        }
    }

    foreach ($arrayFirstFile as $key => $value) {
        if (array_key_exists($key, $arraySecondFile)) {
            if ($value === $arraySecondFile[$key]) {
                $unionArray[] = "    {$key}: " . stayBool($value);
            } else {
                $unionArray[] = "  - {$key}: " . stayBool($value);
                $unionArray[] = "  + {$key}: {$arraySecondFile[$key]}";
            }
        }
    }

    foreach ($arraySecondFile as $key => $value) {
        if (!array_key_exists($key, $arrayFirstFile)) {
            $unionArray[] = "  + {$key}: " . stayBool($value);
        }
    }

    usort($unionArray, function ($value1, $value2) {
        return substr($value1, 4, 1) > substr($value2, 4, 1) ? 1 : -1;
    });

    return $unionArray;
}

function genDiff($pathToFirstFile, $pathToSecondFile): string
{
    $arrayFirstFile = turnIntoArray($pathToFirstFile);
    $arraySecondFile = turnIntoArray($pathToSecondFile);
    $resultArray = findArrayDiff($arrayFirstFile, $arraySecondFile);
    return '{' . PHP_EOL . implode(PHP_EOL, $resultArray) . PHP_EOL . '}';
}

//$file1 = ['a' => 'none', 'b' => 'yes', 'c' => 35];
//$file2 = ['b' => 'no', 'c' => 35, 'd' => 'true'];
//var_dump(findArrayDiff($file1, $file2));
