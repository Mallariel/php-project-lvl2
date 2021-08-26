<?php

namespace Gendiff\Cli;

use Docopt;

function run()
{
    $doc = <<<DOC
    Generate diff

    Usage:
      gendiff (-h|--help)
      gendiff (-v|--version)

    Options:
      -h --help                     Show this screen
      -v --version                  Show version
    DOC;

    $args = Docopt::handle($doc, array('version'=>'Gendiff 0.0.1'));
}
