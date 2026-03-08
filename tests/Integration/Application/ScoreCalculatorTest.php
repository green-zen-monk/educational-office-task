<?php

declare(strict_types=1);

namespace Tests\Integration\Application;

use GreenZenMonk\AdmissionScoreCalculator\Application\Contract\ViolationMessageResolver;
use GreenZenMonk\AdmissionScoreCalculator\Application\Exception\CalculateAdmissionScoreException;
use GreenZenMonk\AdmissionScoreCalculator\Application\UseCase\CalculateAdmissionScore;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Eligibility\Rule\GraduationResultMinNotReachRule;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Eligibility\Rule\RequiredDefaultGraduationSubjectsRule;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Eligibility\Rule\RequiredGraduationSubjectRule;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Eligibility\Rule\RequiredSelectableGraduationSubjectsRule;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Eligibility\EligibilityResult;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Eligibility\ViolationCode;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Scoring\AdmissionScore;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\Policy\BasicScore\BestRequiredSelectableGraduationSubjectPolicy;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\Policy\BasicScore\RequiredGraduationSubjectPolicy;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\Policy\BonusScore\GraduationSubjectTypeHighPolicy;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\Policy\BonusScore\LanguageExamTypePolicy;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\ScoreEngine;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * ScoreCalculatorTest
 */
class ScoreCalculatorTest extends TestCase
{
    private CalculateAdmissionScore $calculator;

    protected function setUp(): void
    {
        $this->calculator = $this->buildCalculator();
    }

    private function buildCalculator(?ViolationMessageResolver $violationMessageResolver = null): CalculateAdmissionScore
    {
        $validator = new GraduationResultMinNotReachRule();
        $validator->setNext(new RequiredDefaultGraduationSubjectsRule())
                  ->setNext(new RequiredGraduationSubjectRule())
                  ->setNext(new RequiredSelectableGraduationSubjectsRule());

        $scoreEngine = new ScoreEngine([
            new RequiredGraduationSubjectPolicy(),
            new BestRequiredSelectableGraduationSubjectPolicy(),
            new GraduationSubjectTypeHighPolicy(),
            new LanguageExamTypePolicy(),
        ]);

        return new CalculateAdmissionScore($validator, $scoreEngine, $violationMessageResolver);
    }

    /**
     * @return array<string, array{0: Student, 1: AdmissionScore}>
     */
    public static function loadSuccessDummyDataSets(): array
    {
        return require __DIR__ . '/../../Fixtures/calculator_success_student_data_sets.php';
    }

    /**
     * @return array<string, array{0: Student, 1: ViolationCode}>
     */
    public static function loadUnsuccessfulDummyDataSets(): array
    {
        return require __DIR__ . '/../../Fixtures/calculator_unsuccessful_student_data_sets.php';
    }

    #[DataProvider('loadSuccessDummyDataSets')]
    public function testSuccessCalculate(
        Student $student,
        AdmissionScore $expectedAdmissionScore
    ): void {
        $actualAdmissionScore = $this->calculator->execute($student);

        $this->assertSame(
            $expectedAdmissionScore->getBasicScore(),
            $actualAdmissionScore->getBasicScore(),
            'Basic Score'
        );

        $this->assertSame(
            $expectedAdmissionScore->getBonusScore(),
            $actualAdmissionScore->getBonusScore(),
            'Bonus Score'
        );

        $this->assertSame(
            $expectedAdmissionScore->getTotalScore(),
            $actualAdmissionScore->getTotalScore(),
            'Total Score'
        );
    }

    #[DataProvider('loadSuccessDummyDataSets')]
    public function testSuccessCalculateDoesNotAccumulateAcrossCalls(
        Student $student,
        AdmissionScore $_expectedAdmissionScore
    ): void {
        $firstCalculation = $this->calculator->execute($student);
        $secondCalculation = $this->calculator->execute($student);

        $this->assertSame(
            $firstCalculation->getBasicScore(),
            $secondCalculation->getBasicScore(),
            'Basic Score should not accumulate across execute() calls'
        );

        $this->assertSame(
            $firstCalculation->getBonusScore(),
            $secondCalculation->getBonusScore(),
            'Bonus Score should not accumulate across execute() calls'
        );

        $this->assertSame(
            $firstCalculation->getTotalScore(),
            $secondCalculation->getTotalScore(),
            'Total Score should not accumulate across execute() calls'
        );
    }

    #[DataProvider('loadUnsuccessfulDummyDataSets')]
    public function testUnsuccessfulCalculate(
        Student $student,
        ViolationCode $expectedViolationCode
    ): void {
        try {
            $this->calculator->execute($student);
            $this->fail('Expected CalculateAdmissionScoreException to be thrown.');
        } catch (CalculateAdmissionScoreException $e) {
            $this->assertSame($expectedViolationCode->value, $e->getMessage());
        }
    }

    #[DataProvider('loadUnsuccessfulDummyDataSets')]
    public function testUnsuccessfulValidatorResult(
        Student $student,
        ViolationCode $expectedViolationCode
    ): void {
        $actualEligibilityResult = $this->calculator->check($student);

        $this->assertSame(
            false,
            $actualEligibilityResult->isEligible(),
            'Validator result - is valid'
        );

        $violations = $actualEligibilityResult->violations();
        $this->assertNotEmpty($violations, 'Validator result - expected at least one violation');

        $this->assertSame(
            $expectedViolationCode,
            $violations[0]->getCode(),
            'Validator result - first violation code'
        );
    }

    #[DataProvider('loadSuccessDummyDataSets')]
    public function testSuccessValidatorResult(
        Student $student,
        AdmissionScore $_expectedAdmissionScore
    ): void {
        $actualEligibilityResult = $this->calculator->check($student);

        $this->assertSame(
            true,
            $actualEligibilityResult->isEligible(),
            'Validator result - is valid'
        );

        $this->assertCount(0, $actualEligibilityResult->violations(), 'Validator result - no violations');
    }

    #[DataProvider('loadUnsuccessfulDummyDataSets')]
    public function testUnsuccessfulCalculateUsesCustomViolationMessageResolver(
        Student $student,
        ViolationCode $expectedViolationCode
    ): void {
        $calculator = $this->buildCalculator(
            new class () implements ViolationMessageResolver {
                public function resolve(EligibilityResult $result): ?string
                {
                    return $result->violations()[0]->getCode()->value ?? null;
                }
            }
        );

        try {
            $calculator->execute($student);
            $this->fail('Expected CalculateAdmissionScoreException to be thrown.');
        } catch (CalculateAdmissionScoreException $exception) {
            $this->assertSame($expectedViolationCode->value, $exception->getMessage());
        }
    }
}
