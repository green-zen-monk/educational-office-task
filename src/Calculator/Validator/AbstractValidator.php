<?php

declare(strict_types=1);

namespace GreenZenMonk\SimplifiedScoreCalculator\Calculator\Validator;

use GreenZenMonk\SimplifiedScoreCalculator\Calculator\ValidatorResult;
use GreenZenMonk\SimplifiedScoreCalculator\Student;

abstract class AbstractValidator
{
    private ?AbstractValidator $link = null;

    public function linkWith(AbstractValidator $link): AbstractValidator
    {
        $this->link = $link;

        return $link;
    }

    public function check(Student $student): ValidatorResult
    {
        $validatorResult = $this->doCheck($student);

        if ($this->link !== null && $validatorResult->isValid()) {
            $validatorResult = $this->link->check($student);
        }

        return $validatorResult;
    }

    abstract protected function doCheck(Student $student): ValidatorResult;
}
