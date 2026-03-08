<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Input\Mapping\School;

use GreenZenMonk\AdmissionScoreCalculator\Application\Exception\CreateSchoolFromInputException;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\CourseInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\RequiredGraduationSubjectInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\SchoolInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\InputFormat;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\School\Contract\SchoolInputMapperInterface;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\School\JsonSchoolInputMapper;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubjectType;
use PHPUnit\Framework\TestCase;

class JsonSchoolInputMapperTest extends TestCase
{
    public function testMapDelegatesDecodedJsonToArrayMapper(): void
    {
        $expectedSchoolInput = new SchoolInput(
            'Example University',
            'Example Faculty',
            new CourseInput(
                'Example Course',
                new RequiredGraduationSubjectInput(
                    GraduationSubject::Mathematics,
                    GraduationSubjectType::Medium
                ),
                []
            )
        );

        $mapper = new JsonSchoolInputMapper(new class ($expectedSchoolInput) implements SchoolInputMapperInterface {
            public function __construct(private readonly SchoolInput $result)
            {
            }

            public function supports(InputFormat $format, mixed $rawInput): bool
            {
                return $format === InputFormat::ArrayInput && is_array($rawInput);
            }

            public function map(mixed $rawInput): SchoolInput
            {
                return $this->result;
            }
        });

        $json = '{"university":"ELTE","faculty":"IK","course":{"name":"Programtervezo informatikus","required_graduation_subject":{"subject":"mathematics","type":"medium"},"required_selectable_graduation_subjects":[]}}';

        $actual = $mapper->map($json);

        $this->assertSame($expectedSchoolInput, $actual);
    }

    public function testMapThrowsForInvalidJson(): void
    {
        $mapper = new JsonSchoolInputMapper($this->createArrayMapperStub());

        $this->expectException(CreateSchoolFromInputException::class);
        $this->expectExceptionMessage('Invalid JSON input:');

        $mapper->map('{invalid json}');
    }

    public function testMapThrowsWhenJsonRootIsNotArray(): void
    {
        $mapper = new JsonSchoolInputMapper($this->createArrayMapperStub());

        $this->expectException(CreateSchoolFromInputException::class);
        $this->expectExceptionMessage('JSON root must decode to an array.');

        $mapper->map('"school"');
    }

    private function createArrayMapperStub(): SchoolInputMapperInterface
    {
        return new class () implements SchoolInputMapperInterface {
            public function supports(InputFormat $format, mixed $rawInput): bool
            {
                return true;
            }

            public function map(mixed $rawInput): SchoolInput
            {
                throw new CreateSchoolFromInputException('Array mapper should not be called.');
            }
        };
    }
}
