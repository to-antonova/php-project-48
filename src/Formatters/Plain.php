<?php

namespace Differ\Formatters\Plain;

use function Functional\flatten;

function stringify(mixed $value)
{
    if ($value === true) {
        return "true";
    }

    if ($value === false) {
        return "false";
    }

    if ($value === null) {
        return "null";
    }

    if (is_array($value)) {
        return "[complex value]";
    }

    if (is_string($value)) {
        return "'{$value}'";
    }
    return $value;
}

function prepareDiff(array $diff, string $path): array
{
    return array_map(function ($node) use ($path) {

        switch ($node['status']) {
            case 'updated':
                $oldValue = stringify($node['oldValue']);
                $newValue = stringify($node['newValue']);
                return "Property '{$path}{$node['key']}' was updated. From {$oldValue} to {$newValue}";

            case 'added':
                $value = stringify($node['value']);
                return "Property '{$path}{$node['key']}' was added with value: {$value}";

            case 'removed':
                return "Property '{$path}{$node['key']}' was removed";

            case 'unchanged':
                return [];

            case 'has children':
                $newPath = "{$path}{$node['key']}.";
                $children = $node['children'];
                return prepareDiff($children, $newPath);

            default:
                throw new \Exception("Unknown node status '{$node['status']}'");
        }
    }, $diff);
}

function format(array $diff): string
{
    $preparedStrings = prepareDiff($diff, '');
    $joinedStrings = implode(PHP_EOL, flatten($preparedStrings));

    return "{$joinedStrings}";
}
