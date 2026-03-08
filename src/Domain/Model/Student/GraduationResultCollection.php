<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student;

use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course\RequiredGraduationSubject;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\School\Course\RequiredGraduationSubjectCollection;
use GreenZenMonk\AdmissionScoreCalculator\Domain\Model\Student\GraduationResult;
use GreenZenMonk\AdmissionScoreCalculator\Shared\Domain\Collection\AbstractCollection;

/**
 * @extends AbstractCollection<int, GraduationResult>
 */
class GraduationResultCollection extends AbstractCollection
{
    protected function isValidItem(mixed $item): bool
    {
        return $item instanceof GraduationResult;
    }

    public function findRequiredGraduationSubjectResult(RequiredGraduationSubject $requiredSubject): ?GraduationResult
    {
        $bestMatchedResult = null;
        foreach ($this as $item) {
            $graduationSubject = $item->getGraduationSubject();
            $graduationSubjectType = $item->getGraduationSubjectType();
            if (!$requiredSubject->isAvailable($graduationSubject, $graduationSubjectType)) {
                continue;
            }

            if ($bestMatchedResult === null || $item->getResult() > $bestMatchedResult->getResult()) {
                $bestMatchedResult = $item;
            }
        }

        return $bestMatchedResult;
    }

    /**
     * @return list<GraduationResult>
     */
    public function filterRequiredSelectableGraduationSubjectResults(
        RequiredGraduationSubjectCollection $requiredSelectableSubjects
    ): array {
        /** @var list<GraduationResult> $filteredCollection */
        $filteredCollection = [];
        foreach ($requiredSelectableSubjects as $requiredGraduationSubject) {
            $graduationSubjectResult = $this->findRequiredGraduationSubjectResult($requiredGraduationSubject);
            if ($graduationSubjectResult) {
                $filteredCollection[] = $graduationSubjectResult;
            }
        }

        return $filteredCollection;
    }
}
