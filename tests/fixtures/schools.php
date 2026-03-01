<?php

declare(strict_types=1);

use GreenZenMonk\AdmissionScoreCalculator\GraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\GraduationSubjectType;
use GreenZenMonk\AdmissionScoreCalculator\School;
use GreenZenMonk\AdmissionScoreCalculator\SchoolCollection;
use GreenZenMonk\AdmissionScoreCalculator\SchoolCourse;
use GreenZenMonk\AdmissionScoreCalculator\SchoolCourse\RequiredGraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\SchoolCourse\RequiredGraduationSubjectCollection;

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
                    new RequiredGraduationSubject(GraduationSubject::InformationTechnology),
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
