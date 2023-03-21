<?php

namespace Differ\Differ;

function stayBool($value)
{
    if ($value === "NULL") {
        return "null";
    }
    return boolval($value) ? 'true' : 'false';
}


function genDiff($pathToFirstFile, $pathToSecondFile)
{
//    $jsonFirstFile = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '../tests/' . $nameOfFirstFile);
//    $jsonSecondFile = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '../tests/' . $nameOfSecondFile);
    if ($pathToFirstFile === false || $pathToSecondFile === false) {
        return 'Error path';
    }
    $jsonFirstFile = file_get_contents($pathToFirstFile);
    $jsonSecondFile = file_get_contents($pathToSecondFile);
    $arrayFirstFile = json_decode($jsonFirstFile, true);
    $arraySecondFile = json_decode($jsonSecondFile, true);
    $unionArray = [];

    foreach ($arrayFirstFile as $key => $value) {
        if (!array_key_exists($key, $arraySecondFile)) {
            $unionArray[] = "- {$key}: " . stayBool($value);
        }
    }

    foreach ($arrayFirstFile as $key => $value) {
        if (array_key_exists($key, $arraySecondFile)) {
            if ($value === $arraySecondFile[$key]) {
                $unionArray[] = "  {$key}: " . stayBool($value);
            } else {
                $unionArray[] = "- {$key}: " . stayBool($value);
                $unionArray[] = "+ {$key}: {$arraySecondFile[$key]}";
            }
        }
    }

    foreach ($arraySecondFile as $key => $value) {
        if (!array_key_exists($key, $arrayFirstFile)) {
            $unionArray[] = "+ {$key}: " . stayBool($value);
        }
    }

    usort($unionArray, function($v1, $v2) {
        return substr($v1, 2,1) > substr($v2, 2, 1) ? 1 : -1;
    });

    return '{' . PHP_EOL . implode(PHP_EOL, $unionArray) . PHP_EOL . '}';
}

//{
//  - follow: false
//    host: hexlet.io
//  - proxy: 123.234.53.22
//  - timeout: 50
//  + timeout: 20
//  + verbose: true
//};

//echo genDiff('file1.json','file2.json') . PHP_EOL;
