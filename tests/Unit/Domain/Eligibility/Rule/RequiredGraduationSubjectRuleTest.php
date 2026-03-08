<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Eligibility\Rule;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Eligibility\Rule\RequiredGraduationSubjectRule;
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

class RequiredGraduationSubjectRuleTest extends TestCase
{
    public function testReturnsEligibleWhenRequiredSubjectIsPresent(): void
    {
        $rule = new RequiredGraduationSubjectRule();
        $student = $this->createStudent(
            new RequiredGraduationSubject(GraduationSubject::EnglishGrammar, GraduationSubjectType::High),
            [
                new GraduationResult(GraduationSubject::EnglishGrammar, GraduationSubjectType::High, 80),
            ]
        );

        $result = $rule->check($student);

        $this->assertTrue($result->isEligible());
        $this->assertCount(0, $result->violations());
    }

    public function testReturnsNotEligibleWhenRequiredSubjectIsMissing(): void
    {
        $rule = new RequiredGraduationSubjectRule();
        $student = $this->createStudent(
            new RequiredGraduationSubject(GraduationSubject::Mathematics),
            [
                new GraduationResult(GraduationSubject::History, GraduationSubjectType::Medium, 90),
            ]
        );

        $result = $rule->check($student);

        $this->assertFalse($result->isEligible());
        $this->assertSame(
            ViolationCode::RequiredGraduationSubjectMissing,
            $result->violations()[0]->getCode()
        );
    }

    public function testReturnsNotEligibleWhenRequiredHighTypeIsOnlyAvailableAsMedium(): void
    {
        $rule = new RequiredGraduationSubjectRule();
        $student = $this->createStudent(
            new RequiredGraduationSubject(GraduationSubject::EnglishGrammar, GraduationSubjectType::High),
            [
                new GraduationResult(GraduationSubject::EnglishGrammar, GraduationSubjectType::Medium, 90),
            ]
        );

        $result = $rule->check($student);

        $this->assertFalse($result->isEligible());
        $this->assertSame(
            ViolationCode::RequiredGraduationSubjectMissing,
            $result->violations()[0]->getCode()
        );
    }

    /**
     * @param list<GraduationResult> $results
     */
    private function createStudent(RequiredGraduationSubject $requiredSubject, array $results): Student
    {
        return new Student(
            new School(
                'ELTE',
                'IK',
                new Course(
                    'Programtervező informatikus',
                    $requiredSubject,
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
