<?php

declare(strict_types=1);

namespace App;

use LogicException;
use RuntimeException;
use function Functional\compose;
use function Functional\flatten;
use function Functional\map;
use function Functional\take_left;
use function Functional\unique;

final class Gene
{
    private array $estimatedHash = [];
    private int $populationSize;
    private int $generationsCount;
    private int $finalItemsCount;
    private array $restItems = [];

    public function __construct(int $populationSize, int $generationsCount, int $finalItemsCount)
    {
        $this->populationSize = $populationSize;
        $this->generationsCount = $generationsCount;
        $this->finalItemsCount = $finalItemsCount;
    }

    public function assertEstimatedHash(array $estimatedHash)
    {
        if (count($estimatedHash) !== count(unique(array_keys($estimatedHash)))) {
            throw new RuntimeException('Estimated set consist of non unique items');
        }
    }

    public function assertInitialPopulation(array $initialPopulation, array $estimatedHash)
    {
        $initialsItems = flatten($initialPopulation);

        if (count($initialsItems) !== count(unique($initialsItems))) {
            throw new RuntimeException('Initial population consist of non unique individuals');
        }

        foreach ($initialsItems as $item) {
            if (!array_key_exists($item, $estimatedHash)) {
                throw new RuntimeException("Initial population contains undefined item '$item'");
            }
        }
    }

    public function process(array $population, array $estimatedHash): array
    {
        $this->assertEstimatedHash($estimatedHash);
        $this->assertInitialPopulation($population, $estimatedHash);

        $this->estimatedHash = $estimatedHash;
        $restItems = array_diff(array_keys($estimatedHash), flatten($population));
        $this->restItems = array_combine($restItems, $restItems);

        $mutationAlgorithm = compose(
            Random::run([$this, 'crossover'], 2),
            Random::run([$this, 'mutate'], 20),
            [$this, 'reduce']
        );

        for ($i = 0; $i < $this->generationsCount; $i++) {
            $population = $mutationAlgorithm($population);
        }

        $estimationItems = map(flatten($population), fn(string $item) => [$item, $estimatedHash[$item]]);
        usort($estimationItems, fn(array $left, array $right) => $right[1] <=> $left[1]);
        $bestItems = take_left(unique(map($estimationItems, fn(array $_) => $_[0])), $this->finalItemsCount);
        return array_values($bestItems);
    }

    public function mutate(array $population): array
    {
        $items = Random::randItems($this->restItems, 2);

        if (2 === count($items)) {
            unset($this->restItems[$items[0]]);
            unset($this->restItems[$items[1]]);

            $population[] = $items;
        }

        return $population;
    }

    public function crossover(array $population): array
    {
        $individuals = Random::randItems($population, 2);

        if (2 === count($individuals) && $individuals[0] !== $individuals[1]) {
            $population[] = $this->crossoverItems($individuals[0], $individuals[1]);
        }

        return $population;
    }

    private function crossoverItems(array $left, array $right): array
    {
        [, $leftSecond] = $left;
        [, $rightSecond] = $right;

        return [$leftSecond, $rightSecond];
    }

    public function reduce(array $population): array
    {
        $estimatedPopulation = map($population,
            fn(array $individual) => [$individual, $this->estimate($individual)]);

        usort($estimatedPopulation, fn(array $left, array $right) => $right[1] <=> $left[1]);

        $sortedPopulation = map($estimatedPopulation, fn($_) => $_[0]);
        return take_left($sortedPopulation, $this->populationSize);
    }

    public function estimate(array $individual): int
    {
        [$first, $second] = $individual;

        if (!array_key_exists($first, $this->estimatedHash) || !array_key_exists($second, $this->estimatedHash)) {
            throw new LogicException("Individual [$first, $second] contains from not available items");
        }

        return $this->estimatedHash[$first] + $this->estimatedHash[$second];
    }
}
