<?php

namespace Differ\Formatters;

use Exception;

function formatOutput(array $diff, string $formatType): string
{
    switch ($formatType) {
        case 'stylish':
            return Stylish\format($diff);

        case 'plain':
            return Plain\format($diff);

        case 'json':
            return Json\format($diff);

        default:
            throw new Exception('Non-existent format: ' . $formatType . PHP_EOL);
    }
}
