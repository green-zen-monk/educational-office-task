<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubjectType;

final readonly class RequiredGraduationSubjectInput
{
    public function __construct(
        private GraduationSubject $subject,
        private GraduationSubjectType $subjectType
    ) {
    }

    public function getSubject(): GraduationSubject
    {
        return $this->subject;
    }

    public function getSubjectType(): GraduationSubjectType
    {
        return $this->subjectType;
    }
}
