<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Input\Mapping\Student;

use GreenZenMonk\AdmissionScoreCalculator\Application\Exception\CreateStudentFromInputException;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\SelectedProgramInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\StudentInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\InputFormat;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\Contract\StudentInputMapperInterface;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\JsonStudentInputMapper;
use PHPUnit\Framework\TestCase;

class JsonStudentInputMapperTest extends TestCase
{
    public function testMapDelegatesDecodedJsonToArrayMapper(): void
    {
        $expectedStudentInput = new StudentInput(
            new SelectedProgramInput('Example University', 'Example Faculty', 'Example Course'),
            [],
            []
        );

        $mapper = new JsonStudentInputMapper(new class ($expectedStudentInput) implements StudentInputMapperInterface {
            public function __construct(private readonly StudentInput $result)
            {
            }

            public function supports(InputFormat $format, mixed $rawInput): bool
            {
                return $format === InputFormat::ArrayInput && is_array($rawInput);
            }

            public function map(mixed $rawInput): StudentInput
            {
                return $this->result;
            }
        });

        $json = '{"valasztott-szak":{"egyetem":"ELTE","kar":"IK","szak":"Programtervezo informatikus"},"erettsegi-eredmenyek":[],"tobbletpontok":[]}';

        $actual = $mapper->map($json);

        $this->assertSame($expectedStudentInput, $actual);
    }

    public function testMapThrowsForInvalidJson(): void
    {
        $mapper = new JsonStudentInputMapper($this->createArrayMapperStub());

        $this->expectException(CreateStudentFromInputException::class);
        $this->expectExceptionMessage('Invalid JSON input:');

        $mapper->map('{invalid json}');
    }

    public function testMapThrowsWhenJsonRootIsNotArray(): void
    {
        $mapper = new JsonStudentInputMapper($this->createArrayMapperStub());

        $this->expectException(CreateStudentFromInputException::class);
        $this->expectExceptionMessage('JSON root must decode to an array.');

        $mapper->map('"student"');
    }

    private function createArrayMapperStub(): StudentInputMapperInterface
    {
        return new class () implements StudentInputMapperInterface {
            public function supports(InputFormat $format, mixed $rawInput): bool
            {
                return true;
            }

            public function map(mixed $rawInput): StudentInput
            {
                throw new CreateStudentFromInputException('Array mapper should not be called.');
            }
        };
    }
}
