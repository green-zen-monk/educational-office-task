<?php

declare(strict_types=1);

namespace GreenZenMonk\SimplifiedScoreCalculator\SchoolCourse;

use GreenZenMonk\SimplifiedScoreCalculator\AbstractCollection;

class RequiredGraduationSubjectCollection extends AbstractCollection
{
    protected function isValidItem(mixed $item): bool
    {
        return $item instanceof RequiredGraduationSubject;
    }
}
