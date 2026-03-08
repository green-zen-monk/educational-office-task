<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student;

use GreenZenMonk\AdmissionScoreCalculator\Shared\Domain\Collection\AbstractCollection;

/**
 * @extends AbstractCollection<int, ExtraPointInterface>
 */
class ExtraPointCollection extends AbstractCollection
{
    protected function isValidItem(mixed $item): bool
    {
        return $item instanceof ExtraPointInterface;
    }
}
