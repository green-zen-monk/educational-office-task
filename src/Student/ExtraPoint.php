<?php

namespace GreenZenMonk\SimplifiedScoreCalculator\Student;

use GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPointCategory;
use GreenZenMonk\SimplifiedScoreCalculator\Student\ExtraPointParameterName;

final class ExtraPoint
{
    private ExtraPointCategory $extraPointCategory;

    private array $parameters;

    public function __construct(ExtraPointCategory $extraPointCategory, array $parameters)
    {
        $this->extraPointCategory = $extraPointCategory;
        $this->parameters = $parameters;
    }

    public function hasParameter(ExtraPointParameterName $extraPointParameterName)
    {
        return array_key_exists($extraPointParameterName->value, $this->parameters);
    }

    public function getParameter(ExtraPointParameterName $extraPointParameterName)
    {
        return $this->hasParameter($extraPointParameterName) ?
                $this->parameters[$extraPointParameterName->value] : null;
    }

    public function getCategory(): ExtraPointCategory
    {
        return $this->extraPointCategory;
    }
}
