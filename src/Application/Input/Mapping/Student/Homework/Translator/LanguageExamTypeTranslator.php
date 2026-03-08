<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\Homework\Translator;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\ExtraPoint\LanguageExam\Type;
use InvalidArgumentException;

final class LanguageExamTypeTranslator
{
    public function map(string $value): Type
    {
        if ($value === '') {
            throw new InvalidArgumentException('Missing language exam type value.');
        }

        return match ($value) {
            'B2' => Type::B2,
            'C1' => Type::C1,
            default => throw new InvalidArgumentException('Invalid language exam type value: ' . $value),
        };
    }
}
