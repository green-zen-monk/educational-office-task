<?php

declare(strict_types=1);

namespace GreenZenMonk\SimplifiedScoreCalculator\Student;

use GreenZenMonk\SimplifiedScoreCalculator\AbstractCollection;
use GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPoint\LanguageExamExtraPoint;

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
