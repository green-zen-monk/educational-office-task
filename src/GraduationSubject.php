<?php

declare(strict_types=1);

namespace GreenZenMonk\SimplifiedScoreCalculator;

enum GraduationSubject: string
{
    case HungarianGrammarAndLiterature = 'magyar nyelv és irodalom';
    case Histor = 'történelem';
    case Mathematics = 'matematika';
    case InformationTechnology = 'informatika';
    case Physics = 'fizika';
    case Biology = 'biológia';
    case Chemistry = 'kémia';
    case EnglishGrammar = 'angol nyelv';
    case ItalianGrammar = 'olasz nyelv';
    case GermanGrammar = 'német nyelv';
    case FrenchGrammar = 'francia nyelv';
    case RussianGrammar = 'orosz nyelv';
    case SpanishGrammar = 'spanyol nyelv';
}
