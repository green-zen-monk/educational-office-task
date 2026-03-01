<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator;

enum GraduationSubjectType: string
{
    case Medium = 'közép';
    case High = 'emelt';

    public function isHigh(): bool
    {
        return $this === self::High;
    }
}
