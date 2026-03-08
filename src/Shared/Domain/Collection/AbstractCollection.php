<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Shared\Domain\Collection;

/**
 * @template TKey of array-key
 * @template TValue of object
 * @implements CollectionInterface<TKey, TValue>
 */
abstract class AbstractCollection implements CollectionInterface
{
    /** @var array<TKey, TValue> */
    private array $collection = [];

    abstract protected function isValidItem(mixed $item): bool;

    /**
     * @throws CollectionException set invalid object
     * @param array<TKey, TValue> $collection
     */
    public function __construct(array $collection = [])
    {
        foreach ($collection as $item) {
            $this->validate($item);
        }
        $this->collection = $collection;
    }

    /**
     * @return TValue
     */
    public function current(): mixed
    {
        $item = current($this->collection);

        if ($item === false) {
            throw new CollectionException('Has no items in collection!');
        }

        return $item;
    }

    /**
     * @return TKey
     */
    public function key(): mixed
    {
        $key = key($this->collection);

        if ($key === null) {
            throw new CollectionException('Has no items in collection!');
        }

        /** @var TKey $key */
        return $key;
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

    /**
     * @return TValue
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->collection[$offset];
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->collection[$offset]);
    }

    /**
     * @throws CollectionException set invalid object
     * @param TKey|null $key
     * @param TValue $item
     */
    public function offsetSet($key, $item): void
    {
        $this->validate($item);
        if ($key !== null) {
            $this->collection[$key] = $item;
        } else {
            $this->collection[] = $item;
        }
    }

    public function count(): int
    {
        return count($this->collection);
    }

    /**
     * @param callable(TValue): bool $conditionCallback
     */
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

    /**
     * @param callable(TValue): bool $callback
     * @return TValue|null
     */
    public function findWithCallback(callable $callback): ?object
    {
        $searchResult = current(
            $this->filterWithCallback($callback)
        );

        return $searchResult ?: null;
    }

    /**
     * @param callable(TValue): bool $callback
     * @return array<TKey, TValue>
     */
    public function filterWithCallback(callable $callback): array
    {
        return array_filter($this->collection, $callback);
    }

    /**
     * Check valid item
     * @throws CollectionException set invalid item
     * @param mixed $item
     * @return void
     */
    protected function validate(mixed $item): void
    {
        if (!$this->isValidItem($item)) {
            $className = is_object($item) ? get_class($item) : gettype($item);
            throw new CollectionException(
                'Invalid item type: ' . $className
            );
        }
    }
}
