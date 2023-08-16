<?php

namespace Differ\Formatters;

use Differ\Formatters\Stylish;
use Differ\Formatters\Plain;
use Differ\Formatters\Json;

function format($array, $formatType): string
{
    switch ($formatType) {
        case 'stylish':
            return Stylish\toStylish($array);
        case 'plain':
            return Plain\toPlain($array);
        case 'json':
            return Json\toJson($array);
        default:
            throw new Exception('Non-existent format! Try stylish, plain.' . PHP_EOL);
    }
}
