<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Student;

use GreenZenMonk\AdmissionScoreCalculator\AbstractCollection;
use GreenZenMonk\AdmissionScoreCalculator\Student\ExtraPoint\LanguageExamExtraPoint;

/**
 * @extends AbstractCollection<int, LanguageExamExtraPoint>
 */
class LanguageExamExtraPointCollection extends AbstractCollection
{
    protected function isValidItem(mixed $item): bool
    {
        return $item instanceof LanguageExamExtraPoint;
    }
}
