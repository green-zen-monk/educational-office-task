<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Eligibility\Rule;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Eligibility\Rule\RequiredSelectableGraduationSubjectsRule;
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

class RequiredSelectableGraduationSubjectsRuleTest extends TestCase
{
    public function testReturnsEligibleWhenAtLeastOneRequiredSelectableSubjectIsPresent(): void
    {
        $rule = new RequiredSelectableGraduationSubjectsRule();
        $student = $this->createStudent(
            [
                new RequiredGraduationSubject(GraduationSubject::Physics),
                new RequiredGraduationSubject(GraduationSubject::Chemistry),
            ],
            [
                new GraduationResult(GraduationSubject::Physics, GraduationSubjectType::Medium, 78),
            ]
        );

        $result = $rule->check($student);

        $this->assertTrue($result->isEligible());
        $this->assertCount(0, $result->violations());
    }

    public function testReturnsNotEligibleWhenNoRequiredSelectableSubjectsArePresent(): void
    {
        $rule = new RequiredSelectableGraduationSubjectsRule();
        $student = $this->createStudent(
            [
                new RequiredGraduationSubject(GraduationSubject::Physics),
                new RequiredGraduationSubject(GraduationSubject::Chemistry),
            ],
            [
                new GraduationResult(GraduationSubject::History, GraduationSubjectType::Medium, 80),
            ]
        );

        $result = $rule->check($student);

        $this->assertFalse($result->isEligible());
        $this->assertSame(ViolationCode::SelectableSubjectMissing, $result->violations()[0]->getCode());
    }

    /**
     * @param list<RequiredGraduationSubject> $requiredSelectableSubjects
     * @param list<GraduationResult> $results
     */
    private function createStudent(array $requiredSelectableSubjects, array $results): Student
    {
        return new Student(
            new School(
                'ELTE',
                'IK',
                new Course(
                    'Programtervező informatikus',
                    new RequiredGraduationSubject(GraduationSubject::Mathematics),
                    new RequiredGraduationSubjectCollection($requiredSelectableSubjects)
                )
            ),
            new GraduationResultCollection($results),
            new ExtraPointCollection()
        );
    }
}
