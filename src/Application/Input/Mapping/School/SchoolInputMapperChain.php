<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\School;

use GreenZenMonk\AdmissionScoreCalculator\Application\Exception\CreateSchoolFromInputException;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\SchoolInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\InputFormat;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\School\Contract\SchoolInputMapperInterface;

final readonly class SchoolInputMapperChain
{
    /** @var list<SchoolInputMapperInterface> */
    private array $mappers;

    /**
     * @param iterable<array-key, SchoolInputMapperInterface> $mappers
     */
    public function __construct(iterable $mappers)
    {
        $normalizedMappers = [];
        foreach ($mappers as $mapper) {
            $normalizedMappers[] = $mapper;
        }

        $this->mappers = $normalizedMappers;
    }

    /**
     * @throws CreateSchoolFromInputException
     */
    public function map(InputFormat $format, mixed $rawInput): SchoolInput
    {
        foreach ($this->mappers as $mapper) {
            if ($mapper->supports($format, $rawInput)) {
                return $mapper->map($rawInput);
            }
        }

        throw new CreateSchoolFromInputException('Has no mapper for input format: ' . $format->value);
    }
}
