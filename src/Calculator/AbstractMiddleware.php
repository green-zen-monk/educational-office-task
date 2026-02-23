<?php

declare(strict_types=1);

namespace GreenZenMonk\SimplifiedScoreCalculator\Calculator;

use GreenZenMonk\SimplifiedScoreCalculator\Calculator\ScoreAccumulator;
use GreenZenMonk\SimplifiedScoreCalculator\Student;

abstract class AbstractMiddleware
{
    private ?AbstractMiddleware $link = null;

    public function linkWith(AbstractMiddleware $link): AbstractMiddleware
    {
        $this->link = $link;

        return $link;
    }

    public function calculate(Student $student): CalculatorResult
    {
        $scoreAccumulator = $this->calculateChain($student, new ScoreAccumulator());

        return $scoreAccumulator->toResult();
    }

    private function calculateChain(Student $student, ScoreAccumulator $scoreAccumulator): ScoreAccumulator
    {
        $scoreAccumulator = $this->doCalculate($student, $scoreAccumulator);

        if ($this->link === null) {
            return $scoreAccumulator;
        }

        return $this->link->calculateChain($student, $scoreAccumulator);
    }

    abstract protected function doCalculate(
        Student $student,
        ScoreAccumulator $scoreAccumulator
    ): ScoreAccumulator;
}
