<?php

namespace GreenZenMonk\SimplifiedScoreCalculator;

use ArrayObject;
use Exception;
use ArrayIterator;

abstract class AbstractCollection extends ArrayObject
{
    abstract protected function isValidItem($item): bool;

    public function __construct(array|object $array = [], int $flags = 0, string $iteratorClass = ArrayIterator::class)
    {
        foreach ($array as $item) {
            $this->validate($item);
        }
        parent::__construct($array, $flags, $iteratorClass);
    }

    public function append($value): void
    {
        $this->validate($value);
        parent::append($value);
    }

    public function offsetSet($key, $value): void
    {
        $this->validate($value);
        parent::offsetSet($key, $value);
    }

    public function containsWithConditionCallback(callable $conditionCallback): bool
    {
        $conditionResult = false;
        foreach ($this as $item) {
            $conditionResult = $conditionCallback($item);
            if ($conditionResult) {
                break;
            }
        }

        return $conditionResult;
    }

    public function findWithCallback(callable $callback): ?object
    {
        $searchResult = current(
            array_filter(
                $this->getArrayCopy(),
                $callback
            )
        );

        return $searchResult ? : null;
    }

    protected function validate($value): void
    {
        if (!$this->isValidItem($value)) {
            $className = get_class($value);
            throw new Exception(
                'Nem meg felelő objektumot adtál meg: ' . $className
            );
        }
    }
}
