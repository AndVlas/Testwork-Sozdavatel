<?php

function calculateDepositSimple($sum, $months, $percent)
{
    $totalPercent = ($percent / 12) * $months;

    $result = $sum * (1 + $totalPercent / 100);

    return round($result, 2);
}

echo calculateDepositSimple(1000, 12, 12); // 1120 руб.