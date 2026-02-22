<?php

declare(strict_types=1);

namespace GreenZenMonk\SimplifiedScoreCalculator\Student;

use GreenZenMonk\SimplifiedScoreCalculator\AbstractCollection;
use GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPoint\LanguageExamExtraPoint;

class LanguageExamExtraPointCollection extends AbstractCollection
{
    protected function isValidItem($item): bool
    {
        return $item instanceof LanguageExamExtraPoint;
    }
}
