<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Input\Mapping\Student;

use GreenZenMonk\AdmissionScoreCalculator\Application\Exception\CreateStudentFromInputException;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\SelectedProgramInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\StudentInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\InputFormat;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\Contract\StudentInputMapperInterface;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\StudentInputMapperChain;
use PHPUnit\Framework\TestCase;

class StudentInputMapperChainTest extends TestCase
{
    public function testMapUsesFirstSupportingMapper(): void
    {
        $expectedStudentInput = new StudentInput(
            new SelectedProgramInput('Example University', 'Example Faculty', 'Example Course'),
            [],
            []
        );

        $firstSupportingMapper = new class ($expectedStudentInput) implements StudentInputMapperInterface {
            public function __construct(private readonly StudentInput $result)
            {
            }

            public function supports(InputFormat $format, mixed $rawInput): bool
            {
                return $format === InputFormat::ArrayInput;
            }

            public function map(mixed $rawInput): StudentInput
            {
                return $this->result;
            }
        };

        $secondSupportingMapper = new class () implements StudentInputMapperInterface {
            public function supports(InputFormat $format, mixed $rawInput): bool
            {
                return true;
            }

            public function map(mixed $rawInput): StudentInput
            {
                throw new CreateStudentFromInputException('Second mapper should not be called.');
            }
        };

        $chain = new StudentInputMapperChain([$firstSupportingMapper, $secondSupportingMapper]);

        $actual = $chain->map(InputFormat::ArrayInput, ['sample' => 'payload']);

        $this->assertSame($expectedStudentInput, $actual);
    }

    public function testMapThrowsWhenNoMapperSupportsInput(): void
    {
        $chain = new StudentInputMapperChain([
            new class () implements StudentInputMapperInterface {
                public function supports(InputFormat $format, mixed $rawInput): bool
                {
                    return false;
                }

                public function map(mixed $rawInput): StudentInput
                {
                    throw new CreateStudentFromInputException('This mapper should not be called.');
                }
            },
        ]);

        $this->expectException(CreateStudentFromInputException::class);
        $this->expectExceptionMessage('Has no mapper for input format: array');

        $chain->map(InputFormat::ArrayInput, ['sample' => 'payload']);
    }
}
