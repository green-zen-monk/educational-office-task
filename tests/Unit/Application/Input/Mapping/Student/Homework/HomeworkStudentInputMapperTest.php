<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Input\Mapping\Student\Homework;

use GreenZenMonk\AdmissionScoreCalculator\Application\Exception\CreateStudentFromInputException;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\InputFormat;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\Homework\HomeworkStudentInputMapper;
use PHPUnit\Framework\TestCase;

class HomeworkStudentInputMapperTest extends TestCase
{
    public function testSupportsReturnsTrueOnlyForArrayInput(): void
    {
        $mapper = new HomeworkStudentInputMapper();

        $this->assertTrue($mapper->supports(InputFormat::ArrayInput, []));
        $this->assertFalse($mapper->supports(InputFormat::Json, []));
        $this->assertFalse($mapper->supports(InputFormat::ArrayInput, 'not-an-array'));
    }

    public function testMapBuildsStudentInputFromValidArray(): void
    {
        $mapper = new HomeworkStudentInputMapper();

        $studentInput = $mapper->map($this->validInput());

        $this->assertSame('ELTE', $studentInput->getSelectedProgram()->getUniversity());
        $this->assertSame('IK', $studentInput->getSelectedProgram()->getFaculty());
        $this->assertSame('Programtervezo informatikus', $studentInput->getSelectedProgram()->getCourse());
        $this->assertCount(1, $studentInput->getGraduationResults());
        $this->assertCount(1, $studentInput->getExtraPoints());
        $this->assertSame(91, $studentInput->getGraduationResults()[0]->getResult());
    }

    public function testMapThrowsWhenGraduationResultScoreIsProvidedAsInteger(): void
    {
        $mapper = new HomeworkStudentInputMapper();

        $this->expectException(CreateStudentFromInputException::class);
        $this->expectExceptionMessage(
            'Invalid graduation result value. Value: 91 Path: erettsegi-eredmenyek[0].eredmeny'
        );

        $mapper->map($this->validInput(score: 91));
    }

    public function testMapThrowsWhenInputIsNotArray(): void
    {
        $mapper = new HomeworkStudentInputMapper();

        $this->expectException(CreateStudentFromInputException::class);
        $this->expectExceptionMessage('Expected array input for format: array');

        $mapper->map('invalid');
    }

    public function testMapThrowsWhenInputIsEmpty(): void
    {
        $mapper = new HomeworkStudentInputMapper();

        $this->expectException(CreateStudentFromInputException::class);
        $this->expectExceptionMessage('Has no data to create student!');

        $mapper->map([]);
    }

    public function testMapThrowsWhenGraduationResultScoreFormatIsInvalid(): void
    {
        $mapper = new HomeworkStudentInputMapper();

        $this->expectException(CreateStudentFromInputException::class);
        $this->expectExceptionMessage(
            'Invalid graduation result value. Value: 105 Path: erettsegi-eredmenyek[0].eredmeny'
        );

        $mapper->map($this->validInput(score: '105'));
    }

    public function testMapThrowsWhenExtraPointCategoryIsInvalid(): void
    {
        $mapper = new HomeworkStudentInputMapper();

        $this->expectException(CreateStudentFromInputException::class);
        $this->expectExceptionMessage(
            'Invalid extra point category. Value: Invalid Category Path: tobbletpontok[0].kategoria'
        );

        $mapper->map($this->validInput(extraPointCategory: 'Invalid Category'));
    }

    public function testMapThrowsWhenExtraPointLanguageIsInvalid(): void
    {
        $mapper = new HomeworkStudentInputMapper();

        $this->expectException(CreateStudentFromInputException::class);
        $this->expectExceptionMessage(
            'Invalid extra point language. Value: Klingon Path: tobbletpontok[0].nyelv'
        );

        $mapper->map($this->validInput(extraPointLanguage: 'Klingon'));
    }

    public function testMapThrowsWhenExtraPointTypeIsInvalid(): void
    {
        $mapper = new HomeworkStudentInputMapper();

        $this->expectException(CreateStudentFromInputException::class);
        $this->expectExceptionMessage(
            'Invalid extra point type. Value: Invalid Type Path: tobbletpontok[0].tipus'
        );

        $mapper->map($this->validInput(extraPointType: 'Invalid Type'));
    }

    /**
     * @return array<string, mixed>
     */
    private function validInput(
        int|string $score = '91%',
        string $extraPointCategory = 'Nyelvvizsga',
        string $extraPointLanguage = 'angol',
        string $extraPointType = 'B2'
    ): array {
        return [
            'valasztott-szak' => [
                'egyetem' => 'ELTE',
                'kar' => 'IK',
                'szak' => 'Programtervezo informatikus',
            ],
            'erettsegi-eredmenyek' => [
                [
                    'nev' => 'matematika',
                    'tipus' => 'közép',
                    'eredmeny' => $score,
                ],
            ],
            'tobbletpontok' => [
                [
                    'kategoria' => $extraPointCategory,
                    'nyelv' => $extraPointLanguage,
                    'tipus' => $extraPointType,
                ],
            ],
        ];
    }
}
