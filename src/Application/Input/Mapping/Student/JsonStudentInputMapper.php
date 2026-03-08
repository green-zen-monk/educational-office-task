<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student;

use GreenZenMonk\AdmissionScoreCalculator\Application\Exception\CreateStudentFromInputException;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\StudentInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\InputFormat;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\Contract\StudentInputMapperInterface;
use JsonException;

final readonly class JsonStudentInputMapper implements StudentInputMapperInterface
{
    public function __construct(private StudentInputMapperInterface $arrayMapper)
    {
    }

    public function supports(InputFormat $format, mixed $rawInput): bool
    {
        return $format === InputFormat::Json && is_string($rawInput);
    }

    /**
     * @throws CreateStudentFromInputException
     */
    public function map(mixed $rawInput): StudentInput
    {
        if (!is_string($rawInput)) {
            throw new CreateStudentFromInputException(
                'Expected string input for format: ' . InputFormat::Json->value
            );
        }

        try {
            $decoded = json_decode($rawInput, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new CreateStudentFromInputException('Invalid JSON input: ' . $e->getMessage(), 0, $e);
        }

        if (!is_array($decoded)) {
            throw new CreateStudentFromInputException('JSON root must decode to an array.');
        }

        return $this->arrayMapper->map($decoded);
    }
}
