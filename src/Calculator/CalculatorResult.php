<?php

namespace GreenZenMonk\SimplifiedScoreCalculator\Calculator;

final class CalculatorResult
{
    private const MAX_BONUS_SCORE = 100;

    private int $totalScore = 0;

    private int $basicScore = 0;

    private int $bonusScore = 0;

    public function __construct(
        int $basicScore = 0,
        int $bonusScore = 0,
        int $totalScore = 0
    ) {
        $this->basicScore = $basicScore;
        $this->bonusScore = $bonusScore;
        $this->totalScore = $totalScore;
    }

    public function addBasicScore(int $score): self
    {
        $this->basicScore += $score;
        $this->totalScore += $score;

        return $this;
    }

    public function addBonusScore(int $score): self
    {
        $calculatedBonusScore = $this->bonusScore + $score;
        if ($calculatedBonusScore > self::MAX_BONUS_SCORE) {
            $score -= ($calculatedBonusScore - self::MAX_BONUS_SCORE);
        }

        $this->bonusScore += $score;
        $this->totalScore += $score;

        return $this;
    }

    public function getTotalScore(): int
    {
        return $this->totalScore;
    }

    public function getBonusScore(): int
    {
        return $this->bonusScore;
    }

    public function getBasicScore(): int
    {
        return $this->basicScore;
    }
}
