<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubjectType;
use InvalidArgumentException;

final readonly class GraduationResult
{
    private const MIN_RESULT = 0;
    private const MAX_RESULT = 100;

    public function __construct(
        private GraduationSubject $graduationSubject,
        private GraduationSubjectType $graduationSubjectType,
        private int $result
    ) {
        if ($result < self::MIN_RESULT || $result > self::MAX_RESULT) {
            throw new InvalidArgumentException(
                'Graduation result must be between ' . self::MIN_RESULT . ' and ' . self::MAX_RESULT . '.'
            );
        }
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
