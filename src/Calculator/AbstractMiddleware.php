<?php

namespace GreenZenMonk\SimplifiedScoreCalculator\Calculator;

use GreenZenMonk\SimplifiedScoreCalculator\Calculator\CalculatorResult;
use GreenZenMonk\SimplifiedScoreCalculator\Student;

abstract class AbstractMiddleware
{
    private ?AbstractMiddleware $link = null;

    private ?CalculatorResult $defaultCalculatorResult = null;

    public function linkWith(AbstractMiddleware $link): AbstractMiddleware
    {
        $this->link = $link;

        return $link;
    }

    public function calculate(Student $student): CalculatorResult
    {
        if ($this->defaultCalculatorResult === null) {
            $this->setDefaultScoreCalculatorResult(new CalculatorResult());
        }

        $calculatorResult = $this->doCalculate($student, $this->defaultCalculatorResult);

        if ($this->link === null) {
            return $calculatorResult;
        }

        return $this->link
                    ->setDefaultScoreCalculatorResult($calculatorResult)
                    ->calculate($student);
    }

    public function setDefaultScoreCalculatorResult(CalculatorResult $scoreCalculatorResult): self
    {
        $this->defaultCalculatorResult = $scoreCalculatorResult;

        return $this;
    }

    abstract protected function doCalculate(
        Student $student,
        CalculatorResult $defaultCalculatorResult
    ): CalculatorResult;
}
