<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\ExtraPoint\LanguageExam;

enum Language: string
{
    case English = 'english';
    case Italian = 'italian';
    case German = 'german';
    case French = 'french';
    case Russian = 'russian';
    case Spanish = 'spanish';
}
