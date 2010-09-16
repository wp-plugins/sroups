<?php

/**
 *
 * @param String $signature
 * @return String
 */
function getConsumerSecretFromSignature($signature) {
    $G = bchexdec(substr($signature,0,10));
    $P = bchexdec(substr($signature,10,16));
    $clientPublicKey = bchexdec(substr($signature,26,18));
    $serverSecureKey = bchexdec(substr($signature,44,12));

    $serverPassBigInt = bcpowmod($clientPublicKey , $serverSecureKey, $P);
    $serverPass = substr(dump($serverPassBigInt , 12), 3, 8);

    return $serverPass;
}

function dump($str, $size) {
    if (strlen($str) > $size) {
        $str = substr($str,0, $size );
    } else {
        while (strlen($str) < $size) {
            $str.= "4";
        }
    }
    return $str;
}

function bchexdec($hex)
{
    $dec = 0;
    $len = strlen($hex);
    for ($i = 1; $i <= $len; $i++) {
        $dec = bcadd($dec, bcmul(strval(hexdec($hex[$i - 1])), bcpow('16', strval($len - $i))));
    }
    return $dec;
}
