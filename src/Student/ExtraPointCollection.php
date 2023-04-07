<?php

namespace GreenZenMonk\SimplifiedScoreCalculator\Student;

use GreenZenMonk\SimplifiedScoreCalculator\AbstractCollection;

class ExtraPointCollection extends AbstractCollection
{
    protected function isValidItem($item): bool
    {
        return $item instanceof ExtraPoint;
    }
}
