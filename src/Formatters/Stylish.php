<?php

namespace Differ\Formatters\Stylish;

use function Functional\flatten;

function formatArray(array $node): array
{
    $keys = array_keys($node);
    return array_map(function ($key) use ($node) {
        return ['key' => $key, 'value' => $node[$key]];
    }, $keys);
}

function stringify($value, int $depth): string
{
    if ($value === true) {
        return 'true';
    } elseif ($value === false) {
        return 'false';
    } elseif ($value === null) {
        return 'null';
    }
    if (!is_array($value)) {
        return $value;
    }
    $newArray =  array_map(function ($node) use ($depth) {
        $signNoDiff = '    ';
        $indent = str_repeat('    ', $depth);
        $childrenValue = stringify($node['value'], $depth + 1);
        return "{$indent}{$signNoDiff}{$node['key']}: {$childrenValue}";
    }, formatArray($value));
    $arrayToStr = implode(PHP_EOL, $newArray);
    $lastIndent = str_repeat('    ', $depth - 1);

    return "{" . PHP_EOL . $arrayToStr . PHP_EOL . $lastIndent . "    }";
}

function prepareDiff(array $diff, int $depth): array
{
    return array_map(function ($node) use ($depth) {
        $signFirstDiff = '  - ';
        $signSecondDiff = '  + ';
        $signNoDiff = '    ';
        $indent = str_repeat('    ', $depth - 1);
        switch ($node['status']) {
            case 'unchanged':
                $sign = $signNoDiff;
                $value = stringify($node['value'], $depth);
                return "{$indent}{$sign}{$node['key']}: {$value}";
            case 'added':
                $sign = $signSecondDiff;
                $value = stringify($node['value'], $depth);
                return "{$indent}{$sign}{$node['key']}: {$value}";
            case 'removed':
                $sign = $signFirstDiff;
                $value = stringify($node['value'], $depth);
                return "{$indent}{$sign}{$node['key']}: {$value}";
            case 'updated':
                $oldValue = stringify($node['oldValue'], $depth);
                $newValue = stringify($node['newValue'], $depth);
                $firstStr = "{$indent}{$signFirstDiff}{$node['key']}: {$oldValue}";
                $secondStr = "{$indent}{$signSecondDiff}{$node['key']}: {$newValue}";
                return $firstStr . PHP_EOL . $secondStr;
            case 'changed':
                $sign = $signNoDiff;
                $children = $node['children'];
                $firstStr = "{$indent}{$sign}{$node['key']}: {";
                $preparedStrings = prepareDiff($children, $depth + 1);
                $childrenStr = implode(PHP_EOL, $preparedStrings);
                $lastStr = "{$indent}    }";
                return $firstStr . PHP_EOL . $childrenStr . PHP_EOL . $lastStr;
            default:
                throw new \Exception("Unknown node status '{$node['status']}'");
        }
    }, $diff);
}

function toStylish(array $diff): string
{
    $preparedStrings = prepareDiff($diff, 1);
    $joinedStrings = implode("\n", flatten($preparedStrings));
    return "{\n{$joinedStrings}\n}";
}
