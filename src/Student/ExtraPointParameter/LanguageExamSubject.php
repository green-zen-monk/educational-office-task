<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Student\ExtraPointParameter;

enum LanguageExamSubject: string
{
    case English = 'angol';
    case Italian = 'olasz';
    case German = 'német';
    case French = 'francia';
    case Russian = 'orosz';
    case Spanish = 'spanyol';
}
