<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Calculator;

use InvalidArgumentException;

final class CalculatorResult
{
    private const MAX_BONUS_SCORE = 100;

    private int $totalScore;

    private int $basicScore;

    private int $bonusScore;

    public function __construct(
        int $basicScore = 0,
        int $bonusScore = 0,
        ?int $totalScore = null
    ) {
        if ($basicScore < 0) {
            throw new InvalidArgumentException('A basicScore nem lehet negatív!');
        }
        if ($bonusScore < 0 || $bonusScore > self::MAX_BONUS_SCORE) {
            throw new InvalidArgumentException(
                'A bonusScore értéke 0 és ' . self::MAX_BONUS_SCORE . ' között lehet!'
            );
        }

        $calculatedTotalScore = $basicScore + $bonusScore;
        $resolvedTotalScore = $totalScore ?? $calculatedTotalScore;
        if ($resolvedTotalScore !== $calculatedTotalScore) {
            throw new InvalidArgumentException('A totalScore értéke nem egyezik a basic+bonus összeggel!');
        }

        $this->basicScore = $basicScore;
        $this->bonusScore = $bonusScore;
        $this->totalScore = $resolvedTotalScore;
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
