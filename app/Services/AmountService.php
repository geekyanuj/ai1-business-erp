<?php

namespace App\Services;

class AmountService
{
public function amountInWordsIndian($number) {
    $no = floor($number);
    $decimal = round(($number - $no) * 100);
    $words = array(
        0 => '', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
        7 => 'seven', 8 => 'eight', 9 => 'nine', 10 => 'ten', 11 => 'eleven', 12 => 'twelve',
        13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen', 16 => 'sixteen', 17 => 'seventeen',
        18 => 'eighteen', 19 => 'nineteen', 20 => 'twenty', 30 => 'thirty', 40 => 'forty',
        50 => 'fifty', 60 => 'sixty', 70 => 'seventy', 80 => 'eighty', 90 => 'ninety'
    );
    $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');

    $getWords = function($num) use ($words) {
        $num = (int)$num;
        $str = '';
        if ($num > 19) {
            $str .= $words[(int)($num / 10) * 10] . ' ';
            $str .= $words[$num % 10] . ' ';
        } else {
            $str .= $words[$num] . ' ';
        }
        return $str;
    };

    $result = '';
    if ($no > 0) {
        $crore = floor($no / 10000000);
        $no = $no % 10000000;
        $lakh = floor($no / 100000);
        $no = $no % 100000;
        $thousand = floor($no / 1000);
        $no = $no % 1000;
        $hundred = floor($no / 100);
        $no = $no % 100;

        if ($crore > 0) $result .= $getWords($crore) . 'crore ';
        if ($lakh > 0) $result .= $getWords($lakh) . 'lakh ';
        if ($thousand > 0) $result .= $getWords($thousand) . 'thousand ';
        if ($hundred > 0) $result .= $getWords($hundred) . 'hundred ';
        if ($no > 0) $result .= 'and ' . $getWords($no);
        $result .= 'rupees';
    }

    if ($decimal > 0) {
        $result .= ' and ' . $getWords($decimal) . 'paise';
    }

    return ucfirst(trim($result)) . ' only';
}






}

