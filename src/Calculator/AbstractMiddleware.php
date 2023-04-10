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
            $this->setDefaultCalculatorResult(new CalculatorResult());
        }

        $calculatorResult = $this->doCalculate($student, $this->defaultCalculatorResult);

        if ($this->link === null) {
            return $calculatorResult;
        }

        return $this->link
                    ->setDefaultCalculatorResult($calculatorResult)
                    ->calculate($student);
    }

    public function setDefaultCalculatorResult(CalculatorResult $result): self
    {
        $this->defaultCalculatorResult = $result;

        return $this;
    }

    abstract protected function doCalculate(
        Student $student,
        CalculatorResult $calculatorResult
    ): CalculatorResult;
}
