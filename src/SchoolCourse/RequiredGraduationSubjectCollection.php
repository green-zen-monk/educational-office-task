<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\SchoolCourse;

use GreenZenMonk\AdmissionScoreCalculator\AbstractCollection;

/**
 * @extends AbstractCollection<int, RequiredGraduationSubject>
 */
class RequiredGraduationSubjectCollection extends AbstractCollection
{
    protected function isValidItem(mixed $item): bool
    {
        return $item instanceof RequiredGraduationSubject;
    }
}
