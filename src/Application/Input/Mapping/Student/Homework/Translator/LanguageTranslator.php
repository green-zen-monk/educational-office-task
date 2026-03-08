<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\Homework\Translator;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\ExtraPoint\LanguageExam\Language;
use InvalidArgumentException;

final class LanguageTranslator
{
    public function map(string $value): Language
    {
        if ($value === '') {
            throw new InvalidArgumentException('Missing language exam language value.');
        }

        return match ($value) {
            'angol' => Language::English,
            'olasz' => Language::Italian,
            'német' => Language::German,
            'francia' => Language::French,
            'orosz' => Language::Russian,
            'spanyol' => Language::Spanish,
            default => throw new InvalidArgumentException('Invalid language exam language value: ' . $value),
        };
    }
}
