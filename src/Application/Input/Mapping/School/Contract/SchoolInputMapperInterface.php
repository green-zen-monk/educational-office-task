<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\School\Contract;

use GreenZenMonk\AdmissionScoreCalculator\Application\Exception\CreateSchoolFromInputException;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\SchoolInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\InputFormat;

interface SchoolInputMapperInterface
{
    public function supports(InputFormat $format, mixed $rawInput): bool;

    /**
     * @throws CreateSchoolFromInputException
     */
    public function map(mixed $rawInput): SchoolInput;
}
