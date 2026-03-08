<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\Homework\Translator;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\ExtraPointCategory;
use InvalidArgumentException;

final class ExtraPointCategoryTranslator
{
    public function map(string $value): ExtraPointCategory
    {
        if ($value === '') {
            throw new InvalidArgumentException('Missing extra point category value.');
        }

        return match ($value) {
            'Nyelvvizsga' => ExtraPointCategory::LanguageExam,
            default => throw new InvalidArgumentException('Invalid extra point category value: ' . $value),
        };
    }
}
