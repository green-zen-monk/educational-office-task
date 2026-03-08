<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Shared\Domain\Collection;

use ArrayAccess;
use Countable;
use Iterator;

/**
 * @template TKey of array-key
 * @template TValue of object
 * @extends Iterator<TKey, TValue>
 * @extends ArrayAccess<TKey, TValue>
 */
interface CollectionInterface extends Iterator, ArrayAccess, Countable
{
    /**
     * @throws CollectionException set invalid object
     * @param array<TKey, TValue> $collection
     */
    public function __construct(array $collection = []);

    /**
     * @return TValue
     */
    public function current(): mixed;

    /**
     * @return TKey
     */
    public function key(): mixed;

    /**
     * @return TValue
     */
    public function offsetGet(mixed $offset): mixed;

    /**
     * @throws CollectionException set invalid object
     * @param TKey|null $key
     * @param TValue $item
     */
    public function offsetSet($key, $item): void;

    /**
     * @param callable(TValue): bool $conditionCallback
     */
    public function containsWithConditionCallback(callable $conditionCallback): bool;

    /**
     * @param callable(TValue): bool $callback
     * @return TValue|null
     */
    public function findWithCallback(callable $callback): ?object;

    /**
     * @param callable(TValue): bool $callback
     * @return array<TKey, TValue>
     */
    public function filterWithCallback(callable $callback): array;
}
