<?php

declare(strict_types=1);

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubjectType;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course\RequiredGraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course\RequiredGraduationSubjectCollection;
use GreenZenMonk\AdmissionScoreCalculator\Infrastructure\Repository\InMemorySchoolRepository;

return new InMemorySchoolRepository([
    new School(
        'ELTE',
        'IK',
        new Course(
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
        new Course(
            'Anglisztika',
            new RequiredGraduationSubject(GraduationSubject::EnglishGrammar, GraduationSubjectType::High),
            new RequiredGraduationSubjectCollection(
                [
                    new RequiredGraduationSubject(GraduationSubject::FrenchGrammar),
                    new RequiredGraduationSubject(GraduationSubject::GermanGrammar),
                    new RequiredGraduationSubject(GraduationSubject::ItalianGrammar),
                    new RequiredGraduationSubject(GraduationSubject::RussianGrammar),
                    new RequiredGraduationSubject(GraduationSubject::SpanishGrammar),
                    new RequiredGraduationSubject(GraduationSubject::History)
                ]
            )
        )
    )
]);
