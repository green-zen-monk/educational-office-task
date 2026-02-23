<?php

declare(strict_types=1);

use GreenZenMonk\SimplifiedScoreCalculator\GraduationSubject;
use GreenZenMonk\SimplifiedScoreCalculator\GraduationSubjectType;
use GreenZenMonk\SimplifiedScoreCalculator\School;
use GreenZenMonk\SimplifiedScoreCalculator\SchoolCollection;
use GreenZenMonk\SimplifiedScoreCalculator\SchoolCourse;
use GreenZenMonk\SimplifiedScoreCalculator\SchoolCourse\RequiredGraduationSubject;
use GreenZenMonk\SimplifiedScoreCalculator\SchoolCourse\RequiredGraduationSubjectCollection;

return new SchoolCollection([
    new School(
        'ELTE',
        'IK',
        new SchoolCourse(
            'Programtervező informatikus',
            new RequiredGraduationSubject(GraduationSubject::Mathematics),
            new RequiredGraduationSubjectCollection(
                [
                    new RequiredGraduationSubject(GraduationSubject::Biology),
                    new RequiredGraduationSubject(GraduationSubject::Physics),
                    new RequiredGraduationSubject(GraduationSubject::IT),
                    new RequiredGraduationSubject(GraduationSubject::Chemistry)
                ]
            )
        )
    ),
    new School(
        'PPKE',
        'BTK',
        new SchoolCourse(
            'Anglisztika',
            new RequiredGraduationSubject(GraduationSubject::EnglishGrammar, GraduationSubjectType::High),
            new RequiredGraduationSubjectCollection(
                [
                    new RequiredGraduationSubject(GraduationSubject::FrenchGrammar),
                    new RequiredGraduationSubject(GraduationSubject::GermanGrammar),
                    new RequiredGraduationSubject(GraduationSubject::ItalianGrammar),
                    new RequiredGraduationSubject(GraduationSubject::RussianGrammar),
                    new RequiredGraduationSubject(GraduationSubject::SpanishGrammar),
                    new RequiredGraduationSubject(GraduationSubject::Histor)
                ]
            )
        )
    )
]);
