<?php

declare(strict_types=1);

namespace GreenZenMonk\SimplifiedScoreCalculator\Calculator;

use InvalidArgumentException;

final class ScoreAccumulator
{
    private const MAX_BONUS_SCORE = 100;

    private int $basicScore = 0;

    private int $bonusScore = 0;

    public function addBasicScore(int $score): self
    {
        if ($score < 0) {
            throw new InvalidArgumentException('A hozzáadott alappont nem lehet negatív!');
        }

        $this->basicScore += $score;

        return $this;
    }

    public function addBonusScore(int $score): self
    {
        if ($score < 0) {
            throw new InvalidArgumentException('A hozzáadott többletpont nem lehet negatív!');
        }

        $calculatedBonusScore = $this->bonusScore + $score;
        if ($calculatedBonusScore > self::MAX_BONUS_SCORE) {
            $score -= ($calculatedBonusScore - self::MAX_BONUS_SCORE);
        }

        $this->bonusScore += $score;

        return $this;
    }

    public function toResult(): CalculatorResult
    {
        return new CalculatorResult(
            $this->basicScore,
            $this->bonusScore,
            $this->basicScore + $this->bonusScore
        );
    }
}
