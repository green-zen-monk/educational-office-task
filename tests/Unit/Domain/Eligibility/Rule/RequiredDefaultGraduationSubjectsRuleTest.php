<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Eligibility\Rule;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Eligibility\Rule\RequiredDefaultGraduationSubjectsRule;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Eligibility\ViolationCode;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubjectType;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course\RequiredGraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course\RequiredGraduationSubjectCollection;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\ExtraPointCollection;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\GraduationResult;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\GraduationResultCollection;
use PHPUnit\Framework\TestCase;

class RequiredDefaultGraduationSubjectsRuleTest extends TestCase
{
    public function testReturnsEligibleWhenAllDefaultSubjectsArePresent(): void
    {
        $rule = new RequiredDefaultGraduationSubjectsRule();
        $student = $this->createStudent([
            new GraduationResult(GraduationSubject::HungarianGrammarAndLiterature, GraduationSubjectType::Medium, 70),
            new GraduationResult(GraduationSubject::History, GraduationSubjectType::Medium, 80),
            new GraduationResult(GraduationSubject::Mathematics, GraduationSubjectType::Medium, 90),
        ]);

        $result = $rule->check($student);

        $this->assertTrue($result->isEligible());
        $this->assertCount(0, $result->violations());
    }

    public function testReturnsNotEligibleWhenAnyDefaultSubjectIsMissing(): void
    {
        $rule = new RequiredDefaultGraduationSubjectsRule();
        $student = $this->createStudent([
            new GraduationResult(GraduationSubject::HungarianGrammarAndLiterature, GraduationSubjectType::Medium, 70),
            new GraduationResult(GraduationSubject::History, GraduationSubjectType::Medium, 80),
        ]);

        $result = $rule->check($student);

        $this->assertFalse($result->isEligible());
        $this->assertSame(
            ViolationCode::MandatorySubjectsMissing,
            $result->violations()[0]->getCode()
        );
        $this->assertSame(
            [GraduationSubject::Mathematics],
            $result->violations()[0]->getParameters()['missingSubjects']
        );
    }

    /**
     * @param list<GraduationResult> $results
     */
    private function createStudent(array $results): Student
    {
        return new Student(
            new School(
                'ELTE',
                'IK',
                new Course(
                    'Programtervező informatikus',
                    new RequiredGraduationSubject(GraduationSubject::Mathematics),
                    new RequiredGraduationSubjectCollection([
                        new RequiredGraduationSubject(GraduationSubject::Physics),
                    ])
                )
            ),
            new GraduationResultCollection($results),
            new ExtraPointCollection()
        );
    }
}
