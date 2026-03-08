<?php

declare(strict_types=1);

namespace Tests\Integration\Application;

use GreenZenMonk\AdmissionScoreCalculator\Application\Exception\CreateStudentFromInputException;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Dto\StudentInput;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\InputFormat;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\Contract\StudentInputMapperInterface;
use GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\Homework\HomeworkStudentInputMapper;
use GreenZenMonk\AdmissionScoreCalculator\Application\UseCase\CreateStudentFromInput;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\ExtraPoint\LanguageExamExtraPoint;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

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
    private CreateStudentFromInput $studentFactory;

    protected function setUp(): void
    {
        $schools = require __DIR__ . '/../../Fixtures/schools.php';

        $this->studentFactory = new CreateStudentFromInput($schools);
    }

    /**
     * @return array<string, array{0: StudentData}>
     */
    public static function loadDummyValidData(): array
    {
        return require __DIR__ . '/../../Fixtures/builder_valid_students_data.php';
    }

    /**
     * @return array<string, array{0: array<array-key, mixed>}>
     */
    public static function loadDummyInvalidData(): array
    {
        return require __DIR__ . '/../../Fixtures/builder_invalid_students_data.php';
    }

    /**
     * @param StudentData $dataSet
     */
    #[DataProvider('loadDummyValidData')]
    public function testSelectedSchools(array $dataSet): void
    {
        $student = $this->studentFactory->execute($dataSet);

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
        $student = $this->studentFactory->execute($dataSet);

        $collection = $student->getGraduationResultCollection();

        foreach ($collection as $key => $item) {
            $graduationResultData = $dataSet['erettsegi-eredmenyek'][$key];

            $this->assertSame(
                $this->mapGraduationSubject($graduationResultData['nev']),
                $item->getGraduationSubject()->value,
                'Graduation result collection - ' . $key .  ' - name'
            );

            $this->assertSame(
                $this->mapGraduationSubjectType($graduationResultData['tipus']),
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
        $student = $this->studentFactory->execute($dataSet);

        $collection = $student->getExtraPointCollection();

        foreach ($collection as $key => $item) {
            $extraPointData = $dataSet['tobbletpontok'][$key];
            $this->assertSame(
                $this->mapExtraPointCategory($extraPointData['kategoria']),
                $item->getCategory()->value,
                'Extra point collection - ' . $key .  ' - category'
            );

            if (!$item instanceof LanguageExamExtraPoint) {
                continue;
            }

            $this->assertSame(
                $extraPointData['tipus'],
                $item->getType()->value,
                'Extra point collection - ' . $key .  ' - language exam type'
            );

            $this->assertSame(
                $this->mapLanguage($extraPointData['nyelv']),
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
        $this->expectException(CreateStudentFromInputException::class);
        $this->studentFactory->execute($dataSet);
    }

    /**
     * @param StudentData $dataSet
     */
    #[DataProvider('loadDummyValidData')]
    public function testExecuteWithJsonInput(array $dataSet): void
    {
        $jsonData = json_encode($dataSet);
        $this->assertNotFalse($jsonData);

        $student = $this->studentFactory->execute($jsonData, InputFormat::Json);
        $selectedSchool = $student->getSelectedSchool();

        $this->assertSame($dataSet['valasztott-szak']['egyetem'], $selectedSchool->getName());
        $this->assertSame($dataSet['valasztott-szak']['kar'], $selectedSchool->getFaculty());
        $this->assertSame($dataSet['valasztott-szak']['szak'], $selectedSchool->getCourse()->getName());
    }

    public function testObjectInputWithoutCustomMapperThrows(): void
    {
        $this->expectException(CreateStudentFromInputException::class);
        $this->studentFactory->execute(new stdClass(), InputFormat::Object);
    }

    public function testObjectInputWithCustomMapper(): void
    {
        $validDataSet = $this->firstValidDataSet();
        $schools = require __DIR__ . '/../../Fixtures/schools.php';

        $customMapper = new class (new HomeworkStudentInputMapper()) implements StudentInputMapperInterface {
            public function __construct(private readonly StudentInputMapperInterface $delegate)
            {
            }

            public function supports(InputFormat $format, mixed $rawInput): bool
            {
                return $format === InputFormat::Object
                    && $rawInput instanceof stdClass
                    && property_exists($rawInput, 'payload');
            }

            public function map(mixed $rawInput): StudentInput
            {
                if (!$rawInput instanceof stdClass || !property_exists($rawInput, 'payload')) {
                    throw new CreateStudentFromInputException('Expected object payload.');
                }

                return $this->delegate->map($rawInput->payload);
            }
        };

        $studentFactory = new CreateStudentFromInput($schools, [$customMapper]);
        $student = $studentFactory->execute((object) ['payload' => $validDataSet], InputFormat::Object);
        $selectedSchool = $student->getSelectedSchool();

        $this->assertSame($validDataSet['valasztott-szak']['egyetem'], $selectedSchool->getName());
        $this->assertSame($validDataSet['valasztott-szak']['kar'], $selectedSchool->getFaculty());
        $this->assertSame($validDataSet['valasztott-szak']['szak'], $selectedSchool->getCourse()->getName());
    }

    /**
     * @return StudentData
     */
    private function firstValidDataSet(): array
    {
        $allDataSet = self::loadDummyValidData();
        $firstDataSet = reset($allDataSet);

        if ($firstDataSet === false) {
            $this->fail('Has no valid data set.');
        }

        return $firstDataSet[0];
    }

    private function mapGraduationSubject(string $subject): string
    {
        return match ($subject) {
            'magyar nyelv és irodalom' => 'hungarian language and literature',
            'történelem' => 'history',
            'matematika' => 'mathematics',
            'informatika' => 'information technology',
            'fizika' => 'physics',
            'biológia' => 'biology',
            'kémia' => 'chemistry',
            'angol nyelv' => 'english language',
            'olasz nyelv' => 'italian language',
            'német nyelv' => 'german language',
            'francia nyelv' => 'french language',
            'orosz nyelv' => 'russian language',
            'spanyol nyelv' => 'spanish language',
            default => throw new InvalidArgumentException('Unexpected graduation subject: ' . $subject),
        };
    }

    private function mapGraduationSubjectType(string $type): string
    {
        return match ($type) {
            'közép' => 'medium',
            'emelt' => 'high',
            default => throw new InvalidArgumentException('Unexpected graduation subject type: ' . $type),
        };
    }

    private function mapExtraPointCategory(string $category): string
    {
        return match ($category) {
            'Nyelvvizsga' => 'language exam',
            default => throw new InvalidArgumentException('Unexpected extra point category: ' . $category),
        };
    }

    private function mapLanguage(string $language): string
    {
        return match ($language) {
            'angol' => 'english',
            'olasz' => 'italian',
            'német' => 'german',
            'francia' => 'french',
            'orosz' => 'russian',
            'spanyol' => 'spanish',
            default => throw new InvalidArgumentException('Unexpected language: ' . $language),
        };
    }
}
