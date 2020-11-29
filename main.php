<?php

declare(strict_types=1);

use App\Gene;

require_once __DIR__ . '/vendor/autoload.php';

$estimatedHash = [
    'Types in programming languages' => 2,
    'Structure and Interpretation of Computer Programs' => 9,
    'Functional programming in scala' => 1,
    'Приемы объектно-ориентированного проектирования' => 1,
    'Атлант расправил плечи' => 5,
    'Хроники Амбера' => 12,
    'Гарри поттер' => 7,
    'The little Redis book' => 2,
    'Domain driven design' => 1,
    'Бегущий в лабиринте' => 6,
    'Мир реки' => 4,
];

$initialPopulation = [
    ['Types in programming languages', 'Structure and Interpretation of Computer Programs'],
    ['Functional programming in scala', 'Приемы объектно-ориентированного проектирования'],
    ['Атлант расправил плечи', 'Хроники Амбера'],
    ['The little Redis book', 'Domain driven design'],
];

$gene = new Gene(4, 5, 3);
$res = $gene->process($initialPopulation, $estimatedHash);

var_dump($res);

