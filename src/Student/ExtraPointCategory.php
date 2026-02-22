<?php

declare(strict_types=1);

namespace GreenZenMonk\SimplifiedScoreCalculator\Student;

enum ExtraPointCategory: string
{
    case LANGUAGE_EXAM = 'Nyelvvizsga';

    public function isLanguageExam(): bool
    {
        return $this === self::LANGUAGE_EXAM;
    }
}
