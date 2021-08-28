<?php

namespace Gendiff\Cli;

use Docopt;
use function Gendiff\Gendiff\gendiff;

function run()
{
    $doc = <<<DOC
    Generate diff

    Usage:
      gendiff (-h|--help)
      gendiff (-v|--version)
      gendiff [--format <fmt>] <firstFile> <secondFile>

    Options:
      -h --help                     Show this screen
      -v --version                  Show version
      --format <fmt>                Report format [default: stylish]
    DOC;

    $args = Docopt::handle($doc, array('version'=>'Gendiff 0.0.1'));

    $format = $args->args['--format'];
    $pathToFirstFile = $args->args['<firstFile>'];
    $pathToSecondFile = $args->args['<secondFile>'];

    echo gendiff($pathToFirstFile, $pathToSecondFile, $format) . "\n";
}
