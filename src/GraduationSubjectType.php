<?php

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
