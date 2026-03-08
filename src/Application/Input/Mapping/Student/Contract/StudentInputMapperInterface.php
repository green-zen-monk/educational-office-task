<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\Contract;

use GreenZenMonk\AdmissionScoreCalculator\Application\Exception\CreateStudentFromInputException;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\StudentInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\InputFormat;

interface StudentInputMapperInterface
{
    public function supports(InputFormat $format, mixed $rawInput): bool;

    /**
     * @throws CreateStudentFromInputException
     */
    public function map(mixed $rawInput): StudentInput;
}
