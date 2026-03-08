<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Scoring;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring\ScoreAccumulator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ScoreAccumulatorTest extends TestCase
{
    public function testStartsFromZeroValues(): void
    {
        $score = (new ScoreAccumulator())->toAdmissionScore();

        $this->assertSame(0, $score->getBasicScore());
        $this->assertSame(0, $score->getBonusScore());
        $this->assertSame(0, $score->getTotalScore());
    }

    public function testAddsBasicScoreAndBonusScore(): void
    {
        $score = (new ScoreAccumulator())
            ->addBasicScore(150)
            ->addBasicScore(50)
            ->addBonusScore(30)
            ->addBonusScore(20)
            ->toAdmissionScore();

        $this->assertSame(200, $score->getBasicScore());
        $this->assertSame(50, $score->getBonusScore());
        $this->assertSame(250, $score->getTotalScore());
    }

    public function testCapsBonusScoreAtHundred(): void
    {
        $score = (new ScoreAccumulator())
            ->addBonusScore(70)
            ->addBonusScore(70)
            ->toAdmissionScore();

        $this->assertSame(100, $score->getBonusScore());
        $this->assertSame(100, $score->getTotalScore());
    }

    public function testThrowsWhenBasicScoreIsNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new ScoreAccumulator())->addBasicScore(-1);
    }

    public function testThrowsWhenBonusScoreIsNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new ScoreAccumulator())->addBonusScore(-1);
    }
}
