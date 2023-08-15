<?php

namespace Differ\Formatters;

use Differ\Formatters\Stylish;
use Differ\Formatters\Plain;

function format($array, $formatType): string
{
    switch ($formatType) {
        case 'stylish':
            return Stylish\stylish($array);
        case 'plain':
            return Plain\plain($array);
        default:
            throw new Exception('Non-existent format! Try stylish, plain.' . PHP_EOL);
    }
}
