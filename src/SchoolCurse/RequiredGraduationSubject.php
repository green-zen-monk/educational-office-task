<?php

namespace GreenZenMonk\SimplifiedScoreCalculator\SchoolCurse;

use GreenZenMonk\SimplifiedScoreCalculator\GraduationSubject;
use GreenZenMonk\SimplifiedScoreCalculator\GraduationSubjectType;

class RequiredGraduationSubject
{
    private GraduationSubject $graduationSubject;
    private GraduationSubjectType $graduationSubjectType;

    public function __construct(
        GraduationSubject $graduationSubject,
        GraduationSubjectType $graduationSubjectType = GraduationSubjectType::MEDIUM
    ) {
        $this->graduationSubject = $graduationSubject;
        $this->graduationSubjectType = $graduationSubjectType;
    }

    public function getTitle(): string
    {
        $graduationSubjectTypeIsHigh = $this->graduationSubjectType->isHigh();
        $graduationSubjectTypeValue = $this->graduationSubjectType->value;
        $graduationSubjectValue = $this->graduationSubject->value;

        return $graduationSubjectValue . ($graduationSubjectTypeIsHigh ? '(' . $graduationSubjectTypeValue . ')' : '');
    }

    public function isAvailable(
        GraduationSubject $graduationSubject,
        GraduationSubjectType $graduationSubjectType
    ): bool {
        return $graduationSubject === $this->graduationSubject
               && (
                    $this->graduationSubjectType !== GraduationSubjectType::HIGH
                    || $graduationSubjectType === $this->graduationSubjectType
               );
    }
}
