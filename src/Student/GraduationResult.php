<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Student;

use GreenZenMonk\AdmissionScoreCalculator\GraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\GraduationSubjectType;
use InvalidArgumentException;

final readonly class GraduationResult
{
    private const MIN_RESULT = 0;
    private const MAX_RESULT = 100;

    private GraduationSubject $graduationSubject;
    private GraduationSubjectType $graduationSubjectType;
    private int $result;

    public function __construct(
        GraduationSubject $graduationSubject,
        GraduationSubjectType $graduationSubjectType,
        int $result
    ) {
        if ($result < self::MIN_RESULT || $result > self::MAX_RESULT) {
            throw new InvalidArgumentException(
                'Az érettségi eredmény csak ' . self::MIN_RESULT . '% és ' . self::MAX_RESULT . '% között lehet.'
            );
        }

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
