<?php

declare(strict_types=1);

namespace Tests\Integration\Application;

use GreenZenMonk\AdmissionScoreCalculator\Application\Exception\CreateSchoolFromInputException;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\SchoolInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\InputFormat;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\School\ArraySchoolInputMapper;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\School\Contract\SchoolInputMapperInterface;
use GreenZenMonk\AdmissionScoreCalculator\Application\UseCase\CreateSchoolFromInput;
use PHPUnit\Framework\TestCase;
use stdClass;

class SchoolFactoryTest extends TestCase
{
    public function testExecuteBuildsSchoolFromArrayInput(): void
    {
        $school = (new CreateSchoolFromInput())->execute($this->validSchoolInput());

        $this->assertSame('ELTE', $school->getName());
        $this->assertSame('IK', $school->getFaculty());
        $this->assertSame('Programtervezo informatikus', $school->getCourse()->getName());
        $this->assertSame('mathematics', $school->getCourse()->getRequiredGraduationSubject()->getTitle());
        $this->assertCount(2, $school->getCourse()->getRequiredSelectableGraduationSubjects());
    }

    public function testExecuteBuildsSchoolFromJsonInput(): void
    {
        $json = json_encode($this->validSchoolInput());
        $this->assertNotFalse($json);

        $school = (new CreateSchoolFromInput())->execute($json, InputFormat::Json);

        $this->assertSame('ELTE', $school->getName());
        $this->assertSame('IK', $school->getFaculty());
        $this->assertSame('Programtervezo informatikus', $school->getCourse()->getName());
        $this->assertSame('mathematics', $school->getCourse()->getRequiredGraduationSubject()->getTitle());
        $this->assertCount(2, $school->getCourse()->getRequiredSelectableGraduationSubjects());
    }

    public function testObjectInputWithoutCustomMapperThrows(): void
    {
        $this->expectException(CreateSchoolFromInputException::class);

        (new CreateSchoolFromInput())->execute(new stdClass(), InputFormat::Object);
    }

    public function testObjectInputWithCustomMapper(): void
    {
        $customMapper = new class (new ArraySchoolInputMapper()) implements SchoolInputMapperInterface {
            public function __construct(private readonly SchoolInputMapperInterface $delegate)
            {
            }

            public function supports(InputFormat $format, mixed $rawInput): bool
            {
                return $format === InputFormat::Object
                    && $rawInput instanceof stdClass
                    && property_exists($rawInput, 'payload');
            }

            public function map(mixed $rawInput): SchoolInput
            {
                if (!$rawInput instanceof stdClass || !property_exists($rawInput, 'payload')) {
                    throw new CreateSchoolFromInputException('Expected object payload.');
                }

                return $this->delegate->map($rawInput->payload);
            }
        };

        $school = (new CreateSchoolFromInput([$customMapper]))->execute(
            (object) ['payload' => $this->validSchoolInput()],
            InputFormat::Object
        );

        $this->assertSame('ELTE', $school->getName());
        $this->assertSame('IK', $school->getFaculty());
        $this->assertSame('Programtervezo informatikus', $school->getCourse()->getName());
        $this->assertCount(2, $school->getCourse()->getRequiredSelectableGraduationSubjects());
    }

    /**
     * @return array<string, mixed>
     */
    private function validSchoolInput(): array
    {
        return [
            'university' => 'ELTE',
            'faculty' => 'IK',
            'course' => [
                'name' => 'Programtervezo informatikus',
                'required_graduation_subject' => [
                    'subject' => 'mathematics',
                    'type' => 'medium',
                ],
                'required_selectable_graduation_subjects' => [
                    [
                        'subject' => 'physics',
                        'type' => 'high',
                    ],
                    [
                        'subject' => 'biology',
                        'type' => 'medium',
                    ],
                ],
            ],
        ];
    }
}
