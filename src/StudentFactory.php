<?php

declare(strict_types=1);

namespace GreenZenMonk\SimplifiedScoreCalculator;

use GreenZenMonk\SimplifiedScoreCalculator\StudentFactoryException;
use GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPointParameter\LanguageExamSubject;
use GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPointParameter\LanguageExamType;
use GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPointCategory;
use GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPoint\LanguageExamExtraPoint;
use GreenZenMonk\SimplifiedScoreCalculator\Student\GraduationResult;
use GreenZenMonk\SimplifiedScoreCalculator\Student\GraduationResultCollection;
use GreenZenMonk\SimplifiedScoreCalculator\Student\LanguageExamExtraPointCollection;

class StudentFactory
{
    private SchoolCollection $schools;

    public function __construct(SchoolCollection $schools)
    {
        $this->schools = $schools;
    }

    /**
     * @param array<array-key, mixed> $data
     */
    public function create(array $data): Student
    {
        return new Student(
            $this->findSelectedSchool($data),
            $this->createGraduationResultCollection($data),
            $this->createLanguageExamExtraPointCollection($data)
        );
    }

    /**
     * @param array<array-key, mixed> $data
     */
    private function findSelectedSchool(array $data): ?object
    {
        /** @var string $dataUniversityName */
        $dataUniversityName = $this->getDataValue($data, 'valasztott-szak.egyetem');
        /** @var string $dataFaculty */
        $dataFaculty = $this->getDataValue($data, 'valasztott-szak.kar');
        /** @var string $dataCourse */
        $dataCourse = $this->getDataValue($data, 'valasztott-szak.szak');

        return $this->schools->findWithCallback(function (School $school) use (
            $dataUniversityName,
            $dataFaculty,
            $dataCourse
        ) {
            return $dataUniversityName === $school->getName()
                && $dataFaculty === $school->getFaculty()
                && $dataCourse === $school->getCourse()->getName();
        });
    }

    /**
     * @param array<array-key, mixed> $data
     */
    private function createGraduationResultCollection(array $data): GraduationResultCollection
    {
        /** @var list<array{nev: string, tipus: string, eredmeny: string}> $graduationResultDataList */
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

    /**
     * @param array<array-key, mixed> $data
     */
    private function createLanguageExamExtraPointCollection(array $data): LanguageExamExtraPointCollection
    {
        /** @var list<array{kategoria: string, nyelv: string, tipus: string}> $dataExtraPoints */
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

    /**
     * @param array<array-key, mixed> $data
     * @return array<array-key, mixed>|string
     */
    private function getDataValue(array $data, string $key, ?string $parentKeys = null): array|string
    {
        if ($key === '') {
            throw new StudentFactoryException('Nincs megadva kulcs az érték kikéréshez!');
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
            throw new StudentFactoryException(
                'A megadott kulcs nem létezik: ' . ($parentKeys !== null ? $parentKeys . '.' : '') . $firstKey
            );
        }

        unset($keyParts[0]);

        $dataValue = $isAllKey ? $data : $data[$firstKey];

        if (!empty($keyParts)) {
            if (!is_array($dataValue)) {
                throw new StudentFactoryException(
                    'A megadott kulcs alatt nem tömbérték található: ' .
                    ($parentKeys !== null ? $parentKeys . '.' : '') . $firstKey
                );
            }

            $parentKeys = $parentKeys === null ? $firstKey : $parentKeys . '.' . $firstKey;
            $key = implode('.', $keyParts);

            if ($isAllKey) {
                $dataValue = [];
                foreach ($data as $item) {
                    if (!is_array($item)) {
                        throw new StudentFactoryException(
                            'A megadott kulcs alatt nem tömbérték található: ' .
                            $parentKeys
                        );
                    }

                    $dataValue[] = $this->getDataValue($item, $key, $parentKeys);
                }

                return $dataValue;
            }

            return $this->getDataValue($dataValue, $key, $parentKeys);
        }

        return $dataValue;
    }
}
