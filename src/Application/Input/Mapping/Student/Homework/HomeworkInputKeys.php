<?php

declare(strict_types=1);

namespace GreenZenMonk\AdmissionScoreCalculator\Application\Input\Mapping\Student\Homework;

final class HomeworkInputKeys
{
    public const SELECTED_PROGRAM = 'valasztott-szak';
    public const SELECTED_PROGRAM_UNIVERSITY = 'egyetem';
    public const SELECTED_PROGRAM_FACULTY = 'kar';
    public const SELECTED_PROGRAM_COURSE = 'szak';

    public const GRADUATION_RESULTS = 'erettsegi-eredmenyek';
    public const GRADUATION_RESULT_SUBJECT = 'nev';
    public const GRADUATION_RESULT_TYPE = 'tipus';
    public const GRADUATION_RESULT_SCORE = 'eredmeny';

    public const EXTRA_POINTS = 'tobbletpontok';
    public const EXTRA_POINT_CATEGORY = 'kategoria';
    public const EXTRA_POINT_LANGUAGE = 'nyelv';
    public const EXTRA_POINT_TYPE = 'tipus';
}
