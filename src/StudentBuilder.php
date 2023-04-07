<?php

namespace GreenZenMonk\SimplifiedScoreCalculator;

use GreenZenMonk\SimplifiedScoreCalculator\StudentBuilderException;
use GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPoint;
use GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPointCollection;
use GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPointParameter\LanguageExamSubject;
use GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPointParameter\LanguageExamType;
use GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPointCategory;
use GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPointParameterName;
use GreenZenMonk\SimplifiedScoreCalculator\Student\GraduationResult;
use GreenZenMonk\SimplifiedScoreCalculator\Student\GraduationResultCollection;

class StudentBuilder
{
    private SchoolCollection $schools;

    public function __construct(SchoolCollection $schools)
    {
        $this->schools = $schools;
    }

    private function buildExtraPointParameters(ExtraPointCategory $extraPointCategory, array $parameters): array
    {
        if ($extraPointCategory->isLanguageExam()) {
            return [
                ExtraPointParameterName::LANGUAGE_EXAM_SUBJECT->value
                                => LanguageExamSubject::from($parameters['nyelv']),
                ExtraPointParameterName::LANGUAGE_EXAM_TYPE->value
                                => LanguageExamType::from($parameters['tipus'])
            ];
        }

        return [];
    }

    private function buildExtraPointCollection(array $datas): ExtraPointCollection
    {
        $datasExtraPoints = $this->getDataValue($datas, 'tobbletpontok.*.kategoria|nyelv|tipus');

        $collection = new ExtraPointCollection();
        foreach ($datasExtraPoints as $data) {
            $extraPointCategory = ExtraPointCategory::from($data['kategoria']);
            $collection[] = new ExtraPoint(
                $extraPointCategory,
                $this->buildExtraPointParameters($extraPointCategory, $data)
            );
        }

        return $collection;
    }

    private function buildGraduationResultCollection(array $datas): GraduationResultCollection
    {
        $dataGraduationResult = $this->getDataValue($datas, 'erettsegi-eredmenyek.*.nev|tipus|eredmeny');

        $collection = new GraduationResultCollection();
        foreach ($dataGraduationResult as $data) {
            $collection[] = new GraduationResult(
                GraduationSubject::from($data['nev']),
                GraduationSubjectType::from($data['tipus']),
                intval($data['eredmeny'])
            );
        }

        return $collection;
    }

    private function getDataValue(array $data, string $key, ?string $parentKeys = null): array|string
    {
        $keyParts = explode('.', $key);

        if (empty($keyParts)) {
            throw new StudentBuilderException('Nincs megadva kulcs az érték kikéréshez!');
        }

        $firstKey = reset($keyParts);
        $isAllKey = $firstKey === '*';
        $isMultiplieKey = strpos($firstKey, '|') !== false;

        if ($isMultiplieKey) {
            $multiplieKeys = explode('|', $firstKey);

            $cachedData = [];
            foreach ($multiplieKeys as $item) {
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

    public function build(array $datas)
    {
        $selectedSchool = $this->schools->findWithCallback(function (School $school) use ($datas) {
            $dataUniversityName = $this->getDataValue($datas, 'valasztott-szak.egyetem');
            $dataFaculty = $this->getDataValue($datas, 'valasztott-szak.kar');
            $dataCurse = $this->getDataValue($datas, 'valasztott-szak.szak');

            return $dataUniversityName === $school->getName()
                    && $dataFaculty === $school->getFaculty()
                    && $dataCurse === $school->getSchoolCurse()->getName();
        });

        return new Student(
            $selectedSchool,
            $this->buildGraduationResultCollection($datas),
            $this->buildExtraPointCollection($datas)
        );
    }
}
