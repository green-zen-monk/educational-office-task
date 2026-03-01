<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator;

use GreenZenMonk\AdmissionScoreCalculator\AbstractCollection;

/**
 * @extends AbstractCollection<int, School>
 */
final class SchoolCollection extends AbstractCollection
{
    protected function isValidItem(mixed $value): bool
    {
        return $value instanceof School;
    }
}
