<?php

namespace GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPointParameter;

enum LanguageExamType: string
{
    case C1 = 'C1';
    case B2 = 'B2';

    public function isC1(): bool
    {
        return self::C1 === $this;
    }

    public function isB2(): bool
    {
        return self::B2 === $this;
    }
}
