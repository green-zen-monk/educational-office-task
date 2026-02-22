<?php

declare(strict_types=1);

namespace GreenZenMonk\SimplifiedScoreCalculator\Student;

use GreenZenMonk\SimplifiedScoreCalculator\GraduationSubject;
use GreenZenMonk\SimplifiedScoreCalculator\GraduationSubjectType;

class GraduationResult
{
    private GraduationSubject $graduationSubject;
    private GraduationSubjectType $graduationSubjectType;
    private int $result;

    public function __construct(
        GraduationSubject $graduationSubject,
        GraduationSubjectType $graduationSubjectType,
        int $result
    ) {
        $this->result = $result;
        $this->graduationSubject = $graduationSubject;
        $this->graduationSubjectType = $graduationSubjectType;
    }

    public function getResult(): int
    {
        return $this->result;
    }

    public function getGraduationSubject(): GraduationSubject
    {
        return $this->graduationSubject;
    }

    public function getGraduationSubjectType(): GraduationSubjectType
    {
        return $this->graduationSubjectType;
    }
}
