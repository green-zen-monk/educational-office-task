<?php

namespace GreenZenMonk\SimplifiedScoreCalculator;

use GreenZenMonk\SimplifiedScoreCalculator\AbstractCollection;

final class SchoolCollection extends AbstractCollection
{
    protected function isValidItem($value): bool
    {
        return $value instanceof School;
    }
}
