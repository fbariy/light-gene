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

$stream = fopen("php://stdin","r");

echo 'The list of available items:' . PHP_EOL . '=================' . PHP_EOL;

foreach ($estimatedHash as $item => $estimation) {
    echo "$item - $estimation" . PHP_EOL;
}

echo PHP_EOL . 'The population by default: ' . PHP_EOL . '=================' . PHP_EOL;

foreach ($initialPopulation as $individual) {
    echo "({$individual[0]}, {$individual[1]})" . PHP_EOL;
}

echo PHP_EOL . 'Enter the population size: ';
$populationSize = fgets($stream);

echo PHP_EOL . 'Enter the generations count: ';
$generationsCount = fgets($stream);

echo PHP_EOL . 'Popular books: ' . PHP_EOL . '=================' . PHP_EOL;

$gene = new Gene(4, 5, 3);
$result = $gene->process($initialPopulation, $estimatedHash);

foreach ($result as $item) {
    echo "\"$item\"" . PHP_EOL;
}
