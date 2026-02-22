<?php

declare(strict_types=1);

namespace GreenZenMonk\SimplifiedScoreCalculator;

use GreenZenMonk\SimplifiedScoreCalculator\StudentBuilderException;
use GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPointParameter\LanguageExamSubject;
use GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPointParameter\LanguageExamType;
use GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPointCategory;
use GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPoint\LanguageExamExtraPoint;
use GreenZenMonk\SimplifiedScoreCalculator\Student\GraduationResult;
use GreenZenMonk\SimplifiedScoreCalculator\Student\GraduationResultCollection;
use GreenZenMonk\SimplifiedScoreCalculator\Student\LanguageExamExtraPointCollection;

class StudentBuilder
{
    private SchoolCollection $schools;

    public function __construct(SchoolCollection $schools)
    {
        $this->schools = $schools;
    }

    private function buildLanguageExamExtraPointCollection(array $data): LanguageExamExtraPointCollection
    {
        $dataExtraPoints = $this->getDataValue($data, 'tobbletpontok.*.kategoria|nyelv|tipus');

        $collection = new LanguageExamExtraPointCollection();
        foreach ($dataExtraPoints as $extraPointData) {
            $extraPointCategory = ExtraPointCategory::from($extraPointData['kategoria']);

            if ($extraPointCategory->isLanguageExam()) {
                $collection[] = new LanguageExamExtraPoint(
                    $extraPointCategory,
                    LanguageExamSubject::from($extraPointData['nyelv']),
                    LanguageExamType::from($extraPointData['tipus'])
                );
            }
        }

        return $collection;
    }

    private function buildGraduationResultCollection(array $data): GraduationResultCollection
    {
        $graduationResultDataList = $this->getDataValue($data, 'erettsegi-eredmenyek.*.nev|tipus|eredmeny');

        $collection = new GraduationResultCollection();
        foreach ($graduationResultDataList as $graduationResultData) {
            $collection[] = new GraduationResult(
                GraduationSubject::from($graduationResultData['nev']),
                GraduationSubjectType::from($graduationResultData['tipus']),
                intval($graduationResultData['eredmeny'])
            );
        }

        return $collection;
    }

    private function getDataValue(array $data, string $key, ?string $parentKeys = null): array|string
    {
        if ($key === '') {
            throw new StudentBuilderException('Nincs megadva kulcs az érték kikéréshez!');
        }
        $keyParts = explode('.', $key);

        $firstKey = reset($keyParts);
        $isAllKey = $firstKey === '*';
        $isMultipleKey = strpos($firstKey, '|') !== false;

        if ($isMultipleKey) {
            $multipleKeys = explode('|', $firstKey);

            $cachedData = [];
            foreach ($multipleKeys as $item) {
                $cachedData[$item] = $this->getDataValue($data, $item, $parentKeys);
            }

            return $cachedData;
        }

        if (!$isAllKey && !array_key_exists($firstKey, $data)) {
            throw new StudentBuilderException(
                'A megadott kulcs nem létezik: ' . ($parentKeys !== null ? $parentKeys . '.' : '') . $firstKey
            );
        }

        unset($keyParts[0]);

        $dataValue = $isAllKey ? $data : $data[$firstKey];

        if (!empty($keyParts)) {
            if (!is_array($dataValue)) {
                throw new StudentBuilderException(
                    'A megadott kulcs alatt nem tömbérték található: ' .
                    ($parentKeys !== null ? $parentKeys . '.' : '') . $firstKey
                );
            }

            $parentKeys = $parentKeys === null ? $firstKey : $parentKeys . '.' . $firstKey;
            $key = implode('.', $keyParts);

            if ($isAllKey) {
                $dataValue = [];
                foreach ($data as $item) {
                    $dataValue[] = $this->getDataValue($item, $key, $parentKeys);
                }

                return $dataValue;
            }

            return $this->getDataValue($dataValue, $key, $parentKeys);
        }

        return $dataValue;
    }

    public function build(array $data): Student
    {
        $selectedSchool = $this->schools->findWithCallback(function (School $school) use ($data) {
            $dataUniversityName = $this->getDataValue($data, 'valasztott-szak.egyetem');
            $dataFaculty = $this->getDataValue($data, 'valasztott-szak.kar');
            $dataCourse = $this->getDataValue($data, 'valasztott-szak.szak');

            return $dataUniversityName === $school->getName()
                    && $dataFaculty === $school->getFaculty()
                    && $dataCourse === $school->getCourse()->getName();
        });

        return new Student(
            $selectedSchool,
            $this->buildGraduationResultCollection($data),
            $this->buildLanguageExamExtraPointCollection($data)
        );
    }
}
