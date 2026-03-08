<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Input\Mapping\School;

use GreenZenMonk\AdmissionScoreCalculator\Application\Exception\CreateSchoolFromInputException;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\CourseInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\RequiredGraduationSubjectInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\SchoolInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\InputFormat;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\School\Contract\SchoolInputMapperInterface;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\School\SchoolInputMapperChain;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Graduation\GraduationSubjectType;
use PHPUnit\Framework\TestCase;

class SchoolInputMapperChainTest extends TestCase
{
    public function testMapUsesFirstSupportingMapper(): void
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

        $firstSupportingMapper = new class ($expectedSchoolInput) implements SchoolInputMapperInterface {
            public function __construct(private readonly SchoolInput $result)
            {
            }

            public function supports(InputFormat $format, mixed $rawInput): bool
            {
                return $format === InputFormat::ArrayInput;
            }

            public function map(mixed $rawInput): SchoolInput
            {
                return $this->result;
            }
        };

        $secondSupportingMapper = new class () implements SchoolInputMapperInterface {
            public function supports(InputFormat $format, mixed $rawInput): bool
            {
                return true;
            }

            public function map(mixed $rawInput): SchoolInput
            {
                throw new CreateSchoolFromInputException('Second mapper should not be called.');
            }
        };

        $chain = new SchoolInputMapperChain([$firstSupportingMapper, $secondSupportingMapper]);

        $actual = $chain->map(InputFormat::ArrayInput, ['sample' => 'payload']);

        $this->assertSame($expectedSchoolInput, $actual);
    }

    public function testMapThrowsWhenNoMapperSupportsInput(): void
    {
        $chain = new SchoolInputMapperChain([
            new class () implements SchoolInputMapperInterface {
                public function supports(InputFormat $format, mixed $rawInput): bool
                {
                    return false;
                }

                public function map(mixed $rawInput): SchoolInput
                {
                    throw new CreateSchoolFromInputException('This mapper should not be called.');
                }
            },
        ]);

        $this->expectException(CreateSchoolFromInputException::class);
        $this->expectExceptionMessage('Has no mapper for input format: array');

        $chain->map(InputFormat::ArrayInput, ['sample' => 'payload']);
    }
}
