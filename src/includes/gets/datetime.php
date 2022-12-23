<?php
    declare(strict_types=1);    

    namespace sbf\includes\gets;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    function GetMonthsArray(bool $abbreviation = false) : array {
        $monthsArray = [
            "Jan" => "January",
            "Feb" => "Februry",
            "Mar" => "March",
            "Apr" => "April",
            "May" => "May",
            "Jun" => "June",
            "Jul" => "July",
            "Aug" => "August",
            "Sep" => "September",
            "Oct" => "October",
            "Nov" => "November",
            "Dec" => "December"
        ];

        return ($abbreviation ? array_combine(range(1,12), array_keys($monthsArray)) : array_combine(range(1,12), array_values($monthsArray)));
    }

    function GetYearsArray($startingYear, $endingYear) {
        return array_combine(range($startingYear, $endingYear), range($startingYear, $endingYear));
    }

    function GetDaysArray($month, $year = 0) {
        $monthDays = [
            1 => 31,
            2 => (($year % 4) == 0 ? 29 : 28),
            3 => 31,
            4 => 30,
            5 => 31,
            6 => 30,
            7 => 31,
            8 => 31,
            9 => 30,
            10 => 31,
            11 => 30,
            12 => 31,
        ];

        return array_combine(
            range(1, $monthDays[min(max(1, $month), 12)]), 
            range(1, $monthDays[min(max(1, $month), 12)])
        );
    }
?>