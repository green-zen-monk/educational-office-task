<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Student;

enum ExtraPointCategory: string
{
    case LanguageExam = 'Nyelvvizsga';

    public function isLanguageExam(): bool
    {
        return $this === self::LanguageExam;
    }
}
