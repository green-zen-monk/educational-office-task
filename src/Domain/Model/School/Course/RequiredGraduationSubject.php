<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubjectType;

final readonly class RequiredGraduationSubject
{
    public function __construct(
        private GraduationSubject $subject,
        private GraduationSubjectType $subjectType = GraduationSubjectType::Medium
    ) {
    }

    public function getTitle(): string
    {
        $subjectTypeIsHigh = $this->subjectType->isHigh();
        $subjectTypeValue = $this->subjectType->value;
        $subjectValue = $this->subject->value;

        return $subjectValue . ($subjectTypeIsHigh ? '(' . $subjectTypeValue . ')' : '');
    }

    public function isAvailable(
        GraduationSubject $actualSubject,
        GraduationSubjectType $actualSubjectType
    ): bool {
        return $actualSubject === $this->subject
               && (
                   $this->subjectType !== GraduationSubjectType::High
                    || $actualSubjectType === $this->subjectType
               );
    }
}
