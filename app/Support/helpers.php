<?php

if (! function_exists('inr_format')) {
    function inr_format($number)
    {
        $number = (string) $number;

        if (strpos($number, '.') !== false) {
            [$integer, $decimal] = explode('.', $number, 2);
            $decimal = '.' . substr($decimal, 0, 2);
        } else {
            $integer = $number;
            $decimal = '';
        }

        if (strlen($integer) <= 3) {
            return $integer . $decimal;
        }

        $lastThree = substr($integer, -3);
        $remaining = substr($integer, 0, -3);

        $remaining = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $remaining);

        return $remaining . ',' . $lastThree . $decimal;
    }
}
