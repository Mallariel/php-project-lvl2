<?php

namespace Gendiff\Gendiff;

// use Functional as F;

function gendiff($pathToFile1, $pathToFile2, $format)
{
    //read
    $rawFile1 = file_get_contents($pathToFile1);
    $rawFile2 = file_get_contents($pathToFile2);
    //parse
    $dataFile1 = json_decode($rawFile1);
    $dataFile2 = json_decode($rawFile2);
    //diff
    $dataDiff = buildTreeDiff($dataFile1, $dataFile2);

    return render($dataDiff);
}

function buildTreeDiff($dataFile1, $dataFile2)
{
    $keys = unionArrayKeysObjects($dataFile1, $dataFile2);

    return array_map(function ($key) use ($dataFile1, $dataFile2) {
        if (!property_exists($dataFile1, $key)) {
            return makeNode($key, 'added', null, $dataFile2->$key);
        }

        if (!property_exists($dataFile2, $key)) {
            return makeNode($key, 'removed', $dataFile1->$key, null);
        }

        if ($dataFile1->$key === $dataFile2->$key) {
            return makeNode($key, 'unchanged', $dataFile1->$key, $dataFile2->$key);
        } else {
            return makeNode($key, 'changed', $dataFile1->$key, $dataFile2->$key);
        }
    }, $keys);
}

function makeNode($key, $status, $oldValud, $newValue)
{
    return [
        'key'       => $key,
        'status'    => $status,
        'oldValue'  => $oldValud,
        'newValue'  => $newValue
    ];
}

function unionArrayKeysObjects($object1, $object2)
{
    $array1 = get_object_vars($object1);
    $array2 = get_object_vars($object2);

    return array_keys(array_merge($array1, $array2));
}

function render($data)
{
    $openBrace = "{\n";
    $closeBrace = "\n}";

    $result = array_reduce($data, function ($acc, $node) {
        $indent_spaces = 2;
        $sign_space = " ";

        $sign_add = "+ ";
        $sign_remove = "- ";
        $nosign = "  ";
        $indent = str_repeat($sign_space, $indent_spaces);

        switch ($node['status']) {
            case "added":
                $acc[] = makeString($node['key'], $node['newValue'], $indent, $sign_add);
                break;
            case 'removed':
                $acc[] = makeString($node['key'], $node['oldValue'], $indent, $sign_remove);
                break;
            case 'changed':
                $acc[] = makeString($node['key'], $node['oldValue'], $indent, $sign_remove);
                $acc[] = makeString($node['key'], $node['newValue'], $indent, $sign_add);
                break;
            case 'unchanged':
                $acc[] = makeString($node['key'], $node['newValue'], $indent, $nosign);
                break;
        }

        return $acc;
    }, []);

    $string = implode("\n", $result);

    return "{$openBrace}{$string}{$closeBrace}";
}

function makeString($key, $value, $indent, $sign)
{
    return "{$indent}{$sign}{$key}: " . stringify($value);
}

function stringify($value)
{
    if (gettype($value) === 'string') {
        return $value;
    }

    return json_encode($value);
}
