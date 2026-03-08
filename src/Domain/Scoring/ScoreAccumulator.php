<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Scoring;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Scoring\AdmissionScore;
use InvalidArgumentException;

final class ScoreAccumulator
{
    private const MAX_BONUS_SCORE = 100;

    private int $basicScore = 0;

    private int $bonusScore = 0;

    public function addBasicScore(int $score): self
    {
        if ($score < 0) {
            throw new InvalidArgumentException('The added base score cannot be negative!');
        }

        $this->basicScore += $score;

        return $this;
    }

    public function addBonusScore(int $score): self
    {
        if ($score < 0) {
            throw new InvalidArgumentException('The added bonus score cannot be negative!');
        }

        $calculatedBonusScore = $this->bonusScore + $score;
        if ($calculatedBonusScore > self::MAX_BONUS_SCORE) {
            $score -= ($calculatedBonusScore - self::MAX_BONUS_SCORE);
        }

        $this->bonusScore += $score;

        return $this;
    }

    public function toAdmissionScore(): AdmissionScore
    {
        return new AdmissionScore(
            $this->basicScore,
            $this->bonusScore,
            $this->basicScore + $this->bonusScore
        );
    }
}
