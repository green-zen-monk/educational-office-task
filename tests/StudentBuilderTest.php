<?php

namespace Tests;

use GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPointParameterName;
use GreenZenMonk\SimplifiedScoreCalculator\StudentBuilder;
use GreenZenMonk\SimplifiedScoreCalculator\StudentBuilderException;
use PHPUnit\Framework\TestCase;

/**
 * StudentBuilderTest
 */
class StudentBuilderTest extends TestCase
{
    private StudentBuilder $studentBuilder;

    protected function setUp(): void
    {
        $schools = require __DIR__ . '/fixtures/schools.php';

        $this->studentBuilder = new StudentBuilder($schools);
    }

    public static function loadDummyValidDatas(): array
    {
        return require __DIR__ . '/fixtures/builder_valid_students_data.php';
    }

    public static function loadDummyInvalidDatas(): array
    {
        return require __DIR__ . '/fixtures/builder_invalid_students_data.php';
    }

    /**
     * @dataProvider loadDummyValidDatas
     */
    public function testSelectedSchools(array $datas)
    {
        $student = $this->studentBuilder->build($datas);

        $school = $student->getSelectedSchool();

        $this->assertSame(
            $datas['valasztott-szak']['egyetem'],
            $school->getName(),
            'Selected school name'
        );
        $this->assertSame(
            $datas['valasztott-szak']['kar'],
            $school->getFaculty(),
            'Selected school faculty'
        );
        $this->assertSame(
            $datas['valasztott-szak']['szak'],
            $school->getSchoolCurse()->getName(),
            'Selected school curse'
        );
    }

    /**
     * @dataProvider loadDummyValidDatas
     */
    public function testGraduationResultCollection(array $datas)
    {
        $student = $this->studentBuilder->build($datas);

        $collection = $student->getGraduationResultCollection();

        foreach ($collection as $key => $item) {
            $dataGraduationResult = $datas['erettsegi-eredmenyek'][$key];

            $this->assertSame(
                $dataGraduationResult['nev'],
                $item->getGraduationSubject()->value,
                'Graduation result collection - ' . $key .  ' - name'
            );

            $this->assertSame(
                $dataGraduationResult['tipus'],
                $item->getGraduationSubjectType()->value,
                'Graduation result collection - ' . $key .  ' - type'
            );

            $this->assertSame(
                $dataGraduationResult['eredmeny'],
                $item->getResult() . '%',
                'Graduation result collection - ' . $key .  ' - result'
            );
        }

        $expectedDatasCount = count($datas['erettsegi-eredmenyek']);

        $this->assertCount($expectedDatasCount, $collection, 'Graduation result collection count');
    }

    /**
     * @dataProvider loadDummyValidDatas
     */
    public function testExtraPointCollection(array $datas)
    {
        $student = $this->studentBuilder->build($datas);

        $collection = $student->getExtraPointCollection();

        foreach ($collection as $key => $item) {
            $dataGraduationResult = $datas['tobbletpontok'][$key];
            $this->assertSame(
                $dataGraduationResult['kategoria'],
                $item->getCategory()->value,
                'Extra point collection - ' . $key .  ' - category'
            );

            $this->assertSame(
                $dataGraduationResult['tipus'],
                $item->getParameter(ExtraPointParameterName::LANGUAGE_EXAM_TYPE)->value,
                'Extra point collection - ' . $key .  ' - language exam type'
            );

            $this->assertSame(
                $dataGraduationResult['nyelv'],
                $item->getParameter(ExtraPointParameterName::LANGUAGE_EXAM_SUBJECT)->value,
                'Extra point collection - ' . $key .  ' - language exam subject'
            );
        }

        $expectedDatasCount = count($datas['tobbletpontok']);

        $this->assertCount($expectedDatasCount, $collection, 'Extra point collection count');
    }

    /**
     * @dataProvider loadDummyInvalidDatas
     */
    public function testInvalidData(array $datas)
    {
        $this->expectException(StudentBuilderException::class);
        $this->studentBuilder->build($datas);
    }
}
