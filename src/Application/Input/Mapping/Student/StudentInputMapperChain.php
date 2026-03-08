<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student;

use GreenZenMonk\AdmissionScoreCalculator\Application\Exception\CreateStudentFromInputException;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\StudentInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\InputFormat;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\Contract\StudentInputMapperInterface;

final readonly class StudentInputMapperChain
{
    /** @var list<StudentInputMapperInterface> */
    private array $mappers;

    /**
     * @param iterable<array-key, StudentInputMapperInterface> $mappers
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
     * @throws CreateStudentFromInputException
     */
    public function map(InputFormat $format, mixed $rawInput): StudentInput
    {
        foreach ($this->mappers as $mapper) {
            if ($mapper->supports($format, $rawInput)) {
                return $mapper->map($rawInput);
            }
        }

        throw new CreateStudentFromInputException('Has no mapper for input format: ' . $format->value);
    }
}
