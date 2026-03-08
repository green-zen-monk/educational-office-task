<?php

declare(strict_types=1);

namespace Tests\Unit\Application\UseCase;

use GreenZenMonk\AdmissionScoreCalculator\Application\Contract\ViolationMessageResolver;
use GreenZenMonk\AdmissionScoreCalculator\Application\Exception\CalculateAdmissionScoreException;
use GreenZenMonk\AdmissionScoreCalculator\Application\UseCase\CalculateAdmissionScore;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Eligibility\Contract\EligibilityRule;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Eligibility\EligibilityResult;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Eligibility\Violation;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Eligibility\ViolationCode;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course\RequiredGraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course\RequiredGraduationSubjectCollection;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\ExtraPointCollection;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\GraduationResultCollection;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\ScoreEngine;
use PHPUnit\Framework\TestCase;

class CalculateAdmissionScoreTest extends TestCase
{
    public function testExecuteFallsBackToViolationCodeWhenResolverReturnsNull(): void
    {
        $useCase = new CalculateAdmissionScore(
            new StaticEligibilityRule(EligibilityResult::notEligible(
                new Violation(ViolationCode::RequiredGraduationSubjectMissing)
            )),
            new ScoreEngine([]),
            new class () implements ViolationMessageResolver {
                public function resolve(EligibilityResult $result): ?string
                {
                    return null;
                }
            }
        );

        $this->expectException(CalculateAdmissionScoreException::class);
        $this->expectExceptionMessage(ViolationCode::RequiredGraduationSubjectMissing->value);

        $useCase->execute($this->student());
    }

    public function testExecuteFallsBackToViolationCodeWhenResolverReturnsEmptyString(): void
    {
        $useCase = new CalculateAdmissionScore(
            new StaticEligibilityRule(EligibilityResult::notEligible(
                new Violation(ViolationCode::SelectableSubjectMissing)
            )),
            new ScoreEngine([]),
            new class () implements ViolationMessageResolver {
                public function resolve(EligibilityResult $result): ?string
                {
                    return '';
                }
            }
        );

        $this->expectException(CalculateAdmissionScoreException::class);
        $this->expectExceptionMessage(ViolationCode::SelectableSubjectMissing->value);

        $useCase->execute($this->student());
    }

    private function student(): Student
    {
        return new Student(
            new School(
                'ELTE',
                'IK',
                new Course(
                    'Programtervezo informatikus',
                    new RequiredGraduationSubject(GraduationSubject::Mathematics),
                    new RequiredGraduationSubjectCollection()
                )
            ),
            new GraduationResultCollection(),
            new ExtraPointCollection()
        );
    }
}

final class StaticEligibilityRule implements EligibilityRule
{
    public function __construct(private readonly EligibilityResult $result)
    {
    }

    public function setNext(EligibilityRule $next): EligibilityRule
    {
        return $next;
    }

    public function check(Student $student): EligibilityResult
    {
        return $this->result;
    }
}
