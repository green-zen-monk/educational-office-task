<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\School;

use GreenZenMonk\AdmissionScoreCalculator\Application\Exception\CreateSchoolFromInputException;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\SchoolInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\InputFormat;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\School\Contract\SchoolInputMapperInterface;
use JsonException;

final readonly class JsonSchoolInputMapper implements SchoolInputMapperInterface
{
    public function __construct(private SchoolInputMapperInterface $arrayMapper)
    {
    }

    public function supports(InputFormat $format, mixed $rawInput): bool
    {
        return $format === InputFormat::Json && is_string($rawInput);
    }

    /**
     * @throws CreateSchoolFromInputException
     */
    public function map(mixed $rawInput): SchoolInput
    {
        if (!is_string($rawInput)) {
            throw new CreateSchoolFromInputException(
                'Expected string input for format: ' . InputFormat::Json->value
            );
        }

        try {
            $decoded = json_decode($rawInput, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new CreateSchoolFromInputException('Invalid JSON input: ' . $e->getMessage(), 0, $e);
        }

        if (!is_array($decoded)) {
            throw new CreateSchoolFromInputException('JSON root must decode to an array.');
        }

        return $this->arrayMapper->map($decoded);
    }
}
