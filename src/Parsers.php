<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;
use Exception;

/**
 * @throws Exception
 */
function convertToArray(string $pathToFile, string $fileContent)
{
    $extension = pathinfo($pathToFile, PATHINFO_EXTENSION);
    switch ($extension) {
        case 'json':
            return json_decode($fileContent, true);
        case 'yaml':
        case 'yml':
            return Yaml::parse($fileContent);
        default:
            throw new Exception('Extension error! Try json, yaml, yml.' . PHP_EOL);
    }
}
