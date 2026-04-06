<?php

function calculateClockAngle($hour, $minute)
{
    $hour = $hour % 12;

    $minuteAngle = $minute * 6;

    $hourAngle = ($hour * 30) + ($minute * 0.5);

    $angle = abs($hourAngle - $minuteAngle);

    return min($angle, 360 - $angle);
}

echo calculateClockAngle(0, 10); //55
