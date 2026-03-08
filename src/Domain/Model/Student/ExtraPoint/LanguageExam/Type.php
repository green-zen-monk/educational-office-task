<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\ExtraPoint\LanguageExam;

enum Type: string
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
