<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course\RequiredGraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\Shared\Domain\Collection\AbstractCollection;

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
