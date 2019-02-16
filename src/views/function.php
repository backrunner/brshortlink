<?php
//sql
$mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

//display
function displayTitle() {
    if (defined('SITE_NAME')) {
        if (SITE_NAME != 'BackRunner\'s ShortLink') {
            echo SITE_NAME;
            return;
        }
    }
    echo "B<mhide>ack</mhide>R<mhide>unner</mhide>'s ShortLink";
}

//shortlink
function f10to62($num) {
    $to = 62;
    $dict = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $ret = '';
    do {
        $ret = $dict[bcmod($num, $to)] . $ret;
        $num = bcdiv($num, $to);
    } while ($num > 0);
    return $ret;
}

function f62to10($num) {
    $from = 62;
    $num = strval($num);
    $dict = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $len = strlen($num);
    $dec = 0;
    for($i = 0; $i < $len; $i++) {
        $pos = strpos($dict, $num[$i]);
        $dec = bcadd(bcmul(bcpow($from, $len - $i - 1), $pos), $dec);
    }
    return $dec;
}