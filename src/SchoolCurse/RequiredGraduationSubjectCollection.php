<?php

namespace GreenZenMonk\SimplifiedScoreCalculator\SchoolCurse;

use GreenZenMonk\SimplifiedScoreCalculator\AbstractCollection;

class RequiredGraduationSubjectCollection extends AbstractCollection
{
    protected function isValidItem($item): bool
    {
        return $item instanceof RequiredGraduationSubject;
    }
}
