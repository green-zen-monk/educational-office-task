<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Model\Scoring;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Scoring\AdmissionScore;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class AdmissionScoreTest extends TestCase
{
    public function testCanBeCreatedWithValidValuesAndExplicitTotal(): void
    {
        $score = new AdmissionScore(370, 100, 470);

        $this->assertSame(370, $score->getBasicScore());
        $this->assertSame(100, $score->getBonusScore());
        $this->assertSame(470, $score->getTotalScore());
    }

    public function testCalculatesTotalWhenNotProvided(): void
    {
        $score = new AdmissionScore(120, 28);

        $this->assertSame(148, $score->getTotalScore());
    }

    public function testThrowsWhenBasicScoreIsNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new AdmissionScore(-1, 0, -1);
    }

    public function testThrowsWhenBonusScoreIsAboveMaximum(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new AdmissionScore(100, 101, 201);
    }

    public function testThrowsWhenBonusScoreIsNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new AdmissionScore(100, -1, 99);
    }

    public function testThrowsOnMismatchedTotalScore(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new AdmissionScore(120, 28, 200);
    }
}
