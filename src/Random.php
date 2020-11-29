<?php

declare(strict_types=1);

namespace App;

use LogicException;
use function Functional\map;

final class Random
{
    public static function randItems(array $arr, int $count): array
    {
        if (count($arr) < $count) {
            return $arr;
        }

        return array_values(map(array_rand($arr, $count), fn($index) => $arr[$index]));
    }


    /**
     * Will return true in 1 / $changeTrigger cases, false otherwise
     *
     * @param int $triggerChance
     *     Denumarator of 1
     *
     * @return bool
     *     Chance is calculated as 1 / $triggerChange
     *
     * @throws LogicException
     *     Throws when $triggerChange <= 0
     */
    public static function bool(int $triggerChance): bool
    {
        if (0 >= $triggerChance) {
            throw new LogicException("Trigger change must be positive number, given $triggerChance");
        }

        return 1 === random_int(1, $triggerChance);
    }

    public static function run(callable $action, int $triggerChance)
    {
        return fn($arg) => self::bool($triggerChance)
            ? $action($arg)
            : $arg;
    }
}
