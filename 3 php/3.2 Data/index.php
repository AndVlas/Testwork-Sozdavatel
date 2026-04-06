<?php

function formatDate($day, $month)
{
    $daysInMonth = [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

    if ($day < 1 || $day > $daysInMonth[$month - 1]) {
        return "Ошибка: неверный день для указанного месяца";
    }

    $months = [
        1 => 'января',
        2 => 'февраля',
        3 => 'марта',
        4 => 'апреля',
        5 => 'мая',
        6 => 'июня',
        7 => 'июля',
        8 => 'августа',
        9 => 'сентября',
        10 => 'октября',
        11 => 'ноября',
        12 => 'декабря'
    ];

    return $day . ' ' . $months[$month];
}

echo formatDate(1, 1) . "\n";   // 1 января
echo formatDate(9, 5) . "\n";   // 9 мая
echo formatDate(25, 12) . "\n"; // 25 декабря
echo formatDate(31, 12) . "\n"; // 31 декабря
echo formatDate(30, 2) . "\n"; // Ошибка