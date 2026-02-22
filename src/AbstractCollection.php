<?php

declare(strict_types=1);

namespace GreenZenMonk\SimplifiedScoreCalculator;

use Iterator;
use ArrayAccess;
use Countable;
use Exception;

abstract class AbstractCollection implements Iterator, ArrayAccess, Countable
{
    private array $collection = [];

    abstract protected function isValidItem(mixed $item): bool;

    /**
     * @throws Exception set invalid object
     */
    public function __construct(array $collection = [])
    {
        foreach ($collection as $item) {
            $this->validate($item);
        }
        $this->collection = $collection;
    }

    public function current(): mixed
    {
        return current($this->collection);
    }

    public function key(): mixed
    {
        return key($this->collection);
    }

    public function next(): void
    {
        next($this->collection);
    }

    public function rewind(): void
    {
        reset($this->collection);
    }

    public function valid(): bool
    {
        return key($this->collection) !== null;
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->collection);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->collection[$offset];
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->collection[$offset]);
    }

    /**
     * @throws Exception set invalid object
     */
    public function offsetSet($key, $item): void
    {
        $this->validate($item);
        if (!empty($key)) {
            $this->collection[$key] = $item;
        } else {
            $this->collection[] = $item;
        }
    }

    public function count(): int
    {
        return count($this->collection);
    }

    public function containsWithConditionCallback(callable $conditionCallback): bool
    {
        $conditionResult = false;
        foreach ($this->collection as $item) {
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
                $this->collection,
                $callback
            )
        );

        return $searchResult ?: null;
    }

    /**
     * Check valid item
     * @throws Exception set invalid item
     * @param mixed $item
     * @return void
     */
    protected function validate(mixed $item): void
    {
        if (!$this->isValidItem($item)) {
            $className = get_class($item);
            throw new Exception(
                'Nem meg felelő objektumot adtál meg: ' . $className
            );
        }
    }
}
