<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Calculator\Validator;

use GreenZenMonk\AdmissionScoreCalculator\Calculator\ValidatorResult;
use GreenZenMonk\AdmissionScoreCalculator\Student;

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
