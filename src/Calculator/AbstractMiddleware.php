<?php

declare(strict_types=1);

namespace GreenZenMonk\SimplifiedScoreCalculator\Calculator;

use GreenZenMonk\SimplifiedScoreCalculator\Calculator\CalculatorResult;
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
        return $this->calculateChain($student, new CalculatorResult());
    }

    private function calculateChain(Student $student, CalculatorResult $calculatorResult): CalculatorResult
    {
        $calculatorResult = $this->doCalculate($student, $calculatorResult);

        if ($this->link === null) {
            return $calculatorResult;
        }

        return $this->link->calculateChain($student, $calculatorResult);
    }

    abstract protected function doCalculate(
        Student $student,
        CalculatorResult $calculatorResult
    ): CalculatorResult;
}
