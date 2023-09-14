<?php

namespace Differ\Formatters;

use Exception;
use Differ\Formatters;

function format(array $array, string $formatType): string
{
    switch ($formatType) {
        case 'stylish':
            return Formatters\Stylish\toStylish($array);

        case 'plain':
            return Formatters\Plain\toPlain($array);

        case 'json':
            return Formatters\Json\toJson($array);

        default:
            throw new Exception('Non-existent format: ' . $formatType . PHP_EOL);
    }
}
