<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation;

enum GraduationSubjectType: string
{
    case Medium = 'medium';
    case High = 'high';

    public function isHigh(): bool
    {
        return $this === self::High;
    }
}
