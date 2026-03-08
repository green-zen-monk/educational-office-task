<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\Homework\Translator;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubject;
use InvalidArgumentException;

final class GraduationSubjectTranslator
{
    public function map(string $value): GraduationSubject
    {
        if ($value === '') {
            throw new InvalidArgumentException('Missing graduation subject value.');
        }

        return match ($value) {
            'magyar nyelv és irodalom' => GraduationSubject::HungarianGrammarAndLiterature,
            'történelem' => GraduationSubject::History,
            'matematika' => GraduationSubject::Mathematics,
            'informatika' => GraduationSubject::InformationTechnology,
            'fizika' => GraduationSubject::Physics,
            'biológia' => GraduationSubject::Biology,
            'kémia' => GraduationSubject::Chemistry,
            'angol nyelv' => GraduationSubject::EnglishGrammar,
            'olasz nyelv' => GraduationSubject::ItalianGrammar,
            'német nyelv' => GraduationSubject::GermanGrammar,
            'francia nyelv' => GraduationSubject::FrenchGrammar,
            'orosz nyelv' => GraduationSubject::RussianGrammar,
            'spanyol nyelv' => GraduationSubject::SpanishGrammar,
            default => throw new InvalidArgumentException('Invalid graduation subject value: ' . $value),
        };
    }
}
