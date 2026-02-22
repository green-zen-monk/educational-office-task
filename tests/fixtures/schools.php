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
            new RequiredGraduationSubject(GraduationSubject::MATHEMATICS),
            new RequiredGraduationSubjectCollection(
                [
                    new RequiredGraduationSubject(GraduationSubject::BIOLOGY),
                    new RequiredGraduationSubject(GraduationSubject::PHYSICS),
                    new RequiredGraduationSubject(GraduationSubject::IT),
                    new RequiredGraduationSubject(GraduationSubject::CHEMISTRY)
                ]
            )
        )
    ),
    new School(
        'PPKE',
        'BTK',
        new SchoolCourse(
            'Anglisztika',
            new RequiredGraduationSubject(GraduationSubject::ENGLISH_GRAMMAR, GraduationSubjectType::HIGH),
            new RequiredGraduationSubjectCollection(
                [
                    new RequiredGraduationSubject(GraduationSubject::FRENCH_GRAMMAR),
                    new RequiredGraduationSubject(GraduationSubject::GERMAN_GRAMMAR),
                    new RequiredGraduationSubject(GraduationSubject::ITALIAN_GRAMMAR),
                    new RequiredGraduationSubject(GraduationSubject::RUSSIAN_GRAMMAR),
                    new RequiredGraduationSubject(GraduationSubject::SPANISH_GRAMMAR),
                    new RequiredGraduationSubject(GraduationSubject::HISTORY)
                ]
            )
        )
    )
]);
