<?php

namespace Differ\Formatters\Json;

function toJson(array $array): string
{
    return json_encode($array, JSON_PRETTY_PRINT);
}
