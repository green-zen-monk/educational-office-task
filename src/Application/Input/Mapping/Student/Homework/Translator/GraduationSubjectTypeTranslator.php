<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\Homework\Translator;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubjectType;
use InvalidArgumentException;

final class GraduationSubjectTypeTranslator
{
    public function map(string $value): GraduationSubjectType
    {
        if ($value === '') {
            throw new InvalidArgumentException('Missing graduation subject type value.');
        }

        return match ($value) {
            'közép' => GraduationSubjectType::Medium,
            'emelt' => GraduationSubjectType::High,
            default => throw new InvalidArgumentException('Invalid graduation subject type value: ' . $value),
        };
    }
}
