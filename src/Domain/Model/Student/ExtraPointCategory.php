<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student;

enum ExtraPointCategory: string
{
    case LanguageExam = 'language exam';

    public function isLanguageExam(): bool
    {
        return $this === self::LanguageExam;
    }
}
