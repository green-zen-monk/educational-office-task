<?php

declare(strict_types=1);

namespace GreenZenMonk\SimplifiedScoreCalculator;

enum GraduationSubjectType: string
{
    case MEDIUM = 'közép';
    case HIGH = 'emelt';

    public function isHigh(): bool
    {
        return $this === self::HIGH;
    }
}
