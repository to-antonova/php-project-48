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

function stringify(mixed $value, int $depth): string
{
    if ($value === true) {
        return 'true';
    }

    if ($value === false) {
        return 'false';
    }

    if ($value === null) {
        return 'null';
    }

    if (!is_array($value)) {
        return $value;
    }

    $children =  array_map(function ($child) use ($depth) {
        $signNoSign = '    ';
        $indent = str_repeat('    ', $depth);
        $childValue = stringify($child['value'], $depth + 1);
        return "{$indent}{$signNoSign}{$child['key']}: {$childValue}";
    }, formatArray($value));

    $childrenToStr = implode(PHP_EOL, $children);
    $lastIndent = str_repeat('    ', $depth - 1);

    return "{" . PHP_EOL . $childrenToStr . PHP_EOL . $lastIndent . "    }";
}

function prepareDiff(array $diff, int $depth): array
{
    return array_map(function ($node) use ($depth) {

        $signRemoved = '  - ';
        $signAdded = '  + ';
        $signNoSign = '    ';
        $indent = str_repeat('    ', $depth - 1);

        switch ($node['status']) {
            case 'unchanged':
                $sign = $signNoSign;
                $value = stringify($node['value'], $depth);
                return "{$indent}{$sign}{$node['key']}: {$value}";

            case 'added':
                $sign = $signAdded;
                $value = stringify($node['value'], $depth);
                return "{$indent}{$sign}{$node['key']}: {$value}";

            case 'removed':
                $sign = $signRemoved;
                $value = stringify($node['value'], $depth);
                return "{$indent}{$sign}{$node['key']}: {$value}";

            case 'updated':
                $oldValue = stringify($node['oldValue'], $depth);
                $newValue = stringify($node['newValue'], $depth);
                $firstStr = "{$indent}{$signRemoved}{$node['key']}: {$oldValue}";
                $secondStr = "{$indent}{$signAdded}{$node['key']}: {$newValue}";
                return $firstStr . PHP_EOL . $secondStr;

            case 'has children':
                $sign = $signNoSign;
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

function format(array $diff): string
{
    $preparedStrings = prepareDiff($diff, 1);
    $joinedStrings = implode("\n", flatten($preparedStrings));

    return "{\n{$joinedStrings}\n}";
}
