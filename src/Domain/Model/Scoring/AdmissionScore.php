<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Scoring;

use InvalidArgumentException;

final class AdmissionScore
{
    private const MAX_BONUS_SCORE = 100;

    public function __construct(
        private int $basicScore = 0,
        private int $bonusScore = 0,
        private ?int $totalScore = null
    ) {
        if ($basicScore < 0) {
            throw new InvalidArgumentException('A basicScore nem lehet negatív!');
        }
        if ($bonusScore < 0 || $bonusScore > self::MAX_BONUS_SCORE) {
            throw new InvalidArgumentException(
                'The bonusScore value must be between 0 and ' . self::MAX_BONUS_SCORE . '!'
            );
        }

        $calculatedTotalScore = $basicScore + $bonusScore;
        $resolvedTotalScore = $totalScore ?? $calculatedTotalScore;
        if ($resolvedTotalScore !== $calculatedTotalScore) {
            throw new InvalidArgumentException('The totalScore value does not match the sum of basicScore and bonusScore!');
        }

        $this->basicScore = $basicScore;
        $this->bonusScore = $bonusScore;
        $this->totalScore = $resolvedTotalScore;
    }

    public function getTotalScore(): ?int
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
