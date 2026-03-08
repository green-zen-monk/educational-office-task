<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation;

enum GraduationSubject: string
{
    case HungarianGrammarAndLiterature = 'hungarian language and literature';
    case History = 'history';
    case Mathematics = 'mathematics';
    case InformationTechnology = 'information technology';
    case Physics = 'physics';
    case Biology = 'biology';
    case Chemistry = 'chemistry';
    case EnglishGrammar = 'english language';
    case ItalianGrammar = 'italian language';
    case GermanGrammar = 'german language';
    case FrenchGrammar = 'french language';
    case RussianGrammar = 'russian language';
    case SpanishGrammar = 'spanish language';
}
