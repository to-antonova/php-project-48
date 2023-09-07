<?php

namespace Differ\Formatters\Plain;

use function Functional\flatten;

function stayBool($value)
{
    if ($value === true) {
        return "true";
    } elseif ($value === false) {
        return "false";
    } elseif ($value === null) {
        return "null";
    } elseif (is_array($value)) {
        return "[complex value]";
    } elseif (is_string($value)) {
        return "'{$value}'";
    }
    return $value;
}

function prepareDiff(array $diff, string $path): array
{
    return array_map(function ($node) use ($path) {
        switch ($node['status']) {
            case 'updated':
                $oldValue = stayBool($node['oldValue']);
                $newValue = stayBool($node['newValue']);
                return "Property '{$path}{$node['key']}' was updated. From {$oldValue} to {$newValue}";
            case 'added':
                $value = stayBool($node['value']);
                return "Property '{$path}{$node['key']}' was added with value: {$value}";
            case 'removed':
                return "Property '{$path}{$node['key']}' was removed";
            case 'unchanged':
                return [];
            case 'changed':
                $newPath = "{$path}{$node['key']}.";
                $children = $node['children'];
                return prepareDiff($children, $newPath);
            default:
                throw new \Exception("Unknown node status '{$node['status']}'");
        }
    }, $diff);
}

function toPlain(array $diff): string
{
    $preparedStrings = prepareDiff($diff, '');
    $joinedStrings = implode(PHP_EOL, flatten($preparedStrings));
    return "{$joinedStrings}";
}
