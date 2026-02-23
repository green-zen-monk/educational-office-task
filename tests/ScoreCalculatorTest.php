<?php

declare(strict_types=1);

namespace Tests;

use GreenZenMonk\SimplifiedScoreCalculator\ScoreCalculator;
use GreenZenMonk\SimplifiedScoreCalculator\Calculator\Middleware\BasicScore\RequiredGraduationSubjectCalculator;
use GreenZenMonk\SimplifiedScoreCalculator\Calculator\Middleware\BasicScore\BestRequiredSelectableGraduationSubjectCalculator;
use GreenZenMonk\SimplifiedScoreCalculator\Calculator\Middleware\BonusScore\GraduationSubjectTypeHighCalculator;
use GreenZenMonk\SimplifiedScoreCalculator\Calculator\Middleware\BonusScore\LanguageExamTypeCalculator;
use GreenZenMonk\SimplifiedScoreCalculator\Calculator\CalculatorResult;
use GreenZenMonk\SimplifiedScoreCalculator\Calculator\Validator\GraduationResultMinNotReachValidator;
use GreenZenMonk\SimplifiedScoreCalculator\Calculator\Validator\RequiredDefaultGraduationSubjectsValidator;
use GreenZenMonk\SimplifiedScoreCalculator\Calculator\Validator\RequiredGraduationSubjectValidator;
use GreenZenMonk\SimplifiedScoreCalculator\Calculator\Validator\RequiredSelectableGraduationSubjectsValidator;
use GreenZenMonk\SimplifiedScoreCalculator\Calculator\ValidatorResult;
use GreenZenMonk\SimplifiedScoreCalculator\ScoreCalculatorException;
use GreenZenMonk\SimplifiedScoreCalculator\Student;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * ScoreCalculatorTest
 */
class ScoreCalculatorTest extends TestCase
{
    private ScoreCalculator $calculator;

    protected function setUp(): void
    {
        $validator = new GraduationResultMinNotReachValidator();
        $validator->linkWith(new RequiredDefaultGraduationSubjectsValidator())
                  ->linkWith(new RequiredGraduationSubjectValidator())
                  ->linkWith(new RequiredSelectableGraduationSubjectsValidator());

        $calculatorMiddleware = new RequiredGraduationSubjectCalculator();
        $calculatorMiddleware->linkWith(new BestRequiredSelectableGraduationSubjectCalculator())
                             ->linkWith(new GraduationSubjectTypeHighCalculator())
                             ->linkWith(new LanguageExamTypeCalculator());


        $this->calculator = new ScoreCalculator($validator, $calculatorMiddleware);
    }

    /**
     * @return array<string, array{0: Student, 1: CalculatorResult}>
     */
    public static function loadSuccessDummyDataSets(): array
    {
        return require __DIR__ . '/fixtures/calculator_success_student_data_sets.php';
    }

    /**
     * @return array<string, array{0: Student, 1: ValidatorResult}>
     */
    public static function loadUnsuccessfulDummyDataSets(): array
    {
        return require __DIR__ . '/fixtures/calculator_unsuccessful_student_data_sets.php';
    }

    #[DataProvider('loadSuccessDummyDataSets')]
    public function testSuccessCalculate(
        Student $student,
        CalculatorResult $expectedCalculatorResult
    ): void {
        $actualCalculatorResult = $this->calculator->calculate($student);

        $this->assertSame(
            $expectedCalculatorResult->getBasicScore(),
            $actualCalculatorResult->getBasicScore(),
            'Basic Score'
        );

        $this->assertSame(
            $expectedCalculatorResult->getBonusScore(),
            $actualCalculatorResult->getBonusScore(),
            'Bonus Score'
        );

        $this->assertSame(
            $expectedCalculatorResult->getTotalScore(),
            $actualCalculatorResult->getTotalScore(),
            'Total Score'
        );
    }

    #[DataProvider('loadSuccessDummyDataSets')]
    public function testSuccessCalculateDoesNotAccumulateAcrossCalls(
        Student $student,
        CalculatorResult $_expectedCalculatorResult
    ): void {
        $firstCalculation = $this->calculator->calculate($student);
        $secondCalculation = $this->calculator->calculate($student);

        $this->assertSame(
            $firstCalculation->getBasicScore(),
            $secondCalculation->getBasicScore(),
            'Basic Score should not accumulate across calculate() calls'
        );

        $this->assertSame(
            $firstCalculation->getBonusScore(),
            $secondCalculation->getBonusScore(),
            'Bonus Score should not accumulate across calculate() calls'
        );

        $this->assertSame(
            $firstCalculation->getTotalScore(),
            $secondCalculation->getTotalScore(),
            'Total Score should not accumulate across calculate() calls'
        );
    }

    #[DataProvider('loadUnsuccessfulDummyDataSets')]
    public function testUnsuccessfulCalculate(
        Student $student,
        ValidatorResult $_expectedValidatorResult
    ): void {
        $this->expectException(ScoreCalculatorException::class);
        $this->calculator->calculate($student);
    }

    #[DataProvider('loadUnsuccessfulDummyDataSets')]
    public function testUnsuccessfulValidatorResult(
        Student $student,
        ValidatorResult $expectedValidatorResult
    ): void {
        $actualValidatorResult = $this->calculator->validate($student);

        $this->assertSame(
            $expectedValidatorResult->isValid(),
            $actualValidatorResult->isValid(),
            'Validator result - is valid'
        );

        $this->assertSame(
            $expectedValidatorResult->getMessage(),
            $actualValidatorResult->getMessage(),
            'Validator result - message'
        );
    }

    #[DataProvider('loadSuccessDummyDataSets')]
    public function testSuccessValidatorResult(
        Student $student,
        CalculatorResult $_expectedCalculatorResult
    ): void {
        $actualValidatorResult = $this->calculator->validate($student);

        $expectedValidatorResult = new ValidatorResult();

        $this->assertSame(
            $expectedValidatorResult->isValid(),
            $actualValidatorResult->isValid(),
            'Validator result - is valid'
        );

        $this->assertSame(
            $expectedValidatorResult->getMessage(),
            $actualValidatorResult->getMessage(),
            'Validator result - message'
        );
    }
}
