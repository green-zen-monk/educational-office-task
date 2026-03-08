<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Model;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School;
use GreenZenMonk\AdmissionScoreCalculator\Shared\Domain\Collection\AbstractCollection;

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
