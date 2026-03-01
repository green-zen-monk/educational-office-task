<?php

declare(strict_types=1);

namespace Tests;

use GreenZenMonk\AdmissionScoreCalculator\Calculator\CalculatorResult;
use GreenZenMonk\AdmissionScoreCalculator\Calculator\ScoreAccumulator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CalculatorResultTest extends TestCase
{
    public function testCalculatorResultCanBeCreatedWithValidScores(): void
    {
        $result = new CalculatorResult(370, 100, 470);

        $this->assertSame(370, $result->getBasicScore());
        $this->assertSame(100, $result->getBonusScore());
        $this->assertSame(470, $result->getTotalScore());
    }

    public function testCalculatorResultCalculatesTotalWhenNotProvided(): void
    {
        $result = new CalculatorResult(120, 28);

        $this->assertSame(148, $result->getTotalScore());
    }

    public function testCalculatorResultThrowsOnMismatchedTotalScore(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CalculatorResult(120, 28, 200);
    }

    public function testScoreAccumulatorCapsBonusAndBuildsResult(): void
    {
        $result = (new ScoreAccumulator())
            ->addBasicScore(200)
            ->addBonusScore(70)
            ->addBonusScore(70)
            ->toResult();

        $this->assertSame(200, $result->getBasicScore());
        $this->assertSame(100, $result->getBonusScore());
        $this->assertSame(300, $result->getTotalScore());
    }
}
