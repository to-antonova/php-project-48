<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;
use Exception;

function turnIntoArray($pathToFile)
{
    if ($pathToFile === false) {
        return '\033[41mPath Error!\033[0m' . PHP_EOL;
    }

    $fileContent =  file_get_contents($pathToFile);

    switch (pathinfo($pathToFile, PATHINFO_EXTENSION)) {
        case 'json':
            return json_decode($fileContent, true);
        case 'yaml':
        case 'yml':
//            return Yaml::parse($fileContent, Yaml::PARSE_OBJECT_FOR_MAP);
            return Yaml::parse($fileContent);
        default:
            throw new Exception('Extension error! Try json, yaml, yml.' . PHP_EOL);
    }
}
