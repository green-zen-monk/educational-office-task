<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Application\Input;

enum InputFormat: string
{
    case ArrayInput = 'array';
    case Json = 'json';
    case Object = 'object';
}
