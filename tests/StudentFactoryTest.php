<?php

declare(strict_types=1);

namespace Tests;

use GreenZenMonk\SimplifiedScoreCalculator\StudentFactory;
use GreenZenMonk\SimplifiedScoreCalculator\StudentFactoryException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * StudentFactoryTest
 *
 * @phpstan-type StudentData array{
 *   'valasztott-szak': array{egyetem: string, kar: string, szak: string},
 *   'erettsegi-eredmenyek': list<array{nev: string, tipus: string, eredmeny: string}>,
 *   'tobbletpontok': list<array{kategoria: string, tipus: string, nyelv: string}>
 * }
 */
class StudentFactoryTest extends TestCase
{
    private StudentFactory $studentFactory;

    protected function setUp(): void
    {
        $schools = require __DIR__ . '/fixtures/schools.php';

        $this->studentFactory = new StudentFactory($schools);
    }

    /**
     * @return array<string, array{0: StudentData}>
     */
    public static function loadDummyValidData(): array
    {
        return require __DIR__ . '/fixtures/builder_valid_students_data.php';
    }

    /**
     * @return array<string, array{0: array<array-key, mixed>}>
     */
    public static function loadDummyInvalidData(): array
    {
        return require __DIR__ . '/fixtures/builder_invalid_students_data.php';
    }

    /**
     * @param StudentData $dataSet
     */
    #[DataProvider('loadDummyValidData')]
    public function testSelectedSchools(array $dataSet): void
    {
        $student = $this->studentFactory->create($dataSet);

        $school = $student->getSelectedSchool();

        $this->assertSame(
            $dataSet['valasztott-szak']['egyetem'],
            $school->getName(),
            'Selected school name'
        );
        $this->assertSame(
            $dataSet['valasztott-szak']['kar'],
            $school->getFaculty(),
            'Selected school faculty'
        );
        $this->assertSame(
            $dataSet['valasztott-szak']['szak'],
            $school->getCourse()->getName(),
            'Selected school course'
        );
    }

    /**
     * @param StudentData $dataSet
     */
    #[DataProvider('loadDummyValidData')]
    public function testGraduationResultCollection(array $dataSet): void
    {
        $student = $this->studentFactory->create($dataSet);

        $collection = $student->getGraduationResultCollection();

        foreach ($collection as $key => $item) {
            $graduationResultData = $dataSet['erettsegi-eredmenyek'][$key];

            $this->assertSame(
                $graduationResultData['nev'],
                $item->getGraduationSubject()->value,
                'Graduation result collection - ' . $key .  ' - name'
            );

            $this->assertSame(
                $graduationResultData['tipus'],
                $item->getGraduationSubjectType()->value,
                'Graduation result collection - ' . $key .  ' - type'
            );

            $this->assertSame(
                $graduationResultData['eredmeny'],
                $item->getResult() . '%',
                'Graduation result collection - ' . $key .  ' - result'
            );
        }

        $expectedDataCount = count($dataSet['erettsegi-eredmenyek']);

        $this->assertCount($expectedDataCount, $collection, 'Graduation result collection count');
    }

    /**
     * @param StudentData $dataSet
     */
    #[DataProvider('loadDummyValidData')]
    public function testExtraPointCollection(array $dataSet): void
    {
        $student = $this->studentFactory->create($dataSet);

        $collection = $student->getLanguageExamCollection();

        foreach ($collection as $key => $item) {
            $extraPointData = $dataSet['tobbletpontok'][$key];
            $this->assertSame(
                $extraPointData['kategoria'],
                $item->getCategory()->value,
                'Extra point collection - ' . $key .  ' - category'
            );

            $this->assertSame(
                $extraPointData['tipus'],
                $item->getType()->value,
                'Extra point collection - ' . $key .  ' - language exam type'
            );

            $this->assertSame(
                $extraPointData['nyelv'],
                $item->getSubject()->value,
                'Extra point collection - ' . $key .  ' - language exam subject'
            );
        }

        $expectedDataCount = count($dataSet['tobbletpontok']);

        $this->assertCount($expectedDataCount, $collection, 'Extra point collection count');
    }

    /**
     * @param array<array-key, mixed> $dataSet
     */
    #[DataProvider('loadDummyInvalidData')]
    public function testInvalidData(array $dataSet): void
    {
        $this->expectException(StudentFactoryException::class);
        $this->studentFactory->create($dataSet);
    }
}
