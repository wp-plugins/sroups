<?php
  /*
 * Please see Sroups License file
  */

  function getSignature() {
    for($i = 0; $i < 18; $i++) {
      $clientPublicKey .= mt_rand(0,9);
    }
    for($i = 0; $i < 12; $i++) {
      $serverSecureKey .= mt_rand(0,9);
    }
    for($i = 0; $i < 10; $i++) {
      $G .= mt_rand(0,9);
    }
    for($i = 0; $i < 16; $i++) {
      $P .= mt_rand(0,9);
    }
    return $G . $P. $clientPublicKey.$serverSecureKey ;
  }

  function tmp($key, $val) {
    echo $key . ' is: ' . $val. ' <br />';
  }

  function getConsumerSecretFromSignature($signature) {
    $G = substr($signature,0,10);
    echo tmp('G', $G);
    $P = substr($signature,10,16);
    echo tmp('$P', $P);
    $clientPublicKey = substr($signature,26,18);
    echo tmp('$clientPublicKey', $clientPublicKey);
    $serverSecureKey = substr($signature,44,12);
    echo tmp('$serverSecureKey', $serverSecureKey);

    $serverPass2 = bcpowmod($clientPublicKey , $serverSecureKey, $P);
    echo tmp('$serverPass2', $serverPass2);

    $serverPass = substr(dump(dec2hex($serverPass2) , 12), 3, 8);
    echo tmp('$serverPass', $serverPass);

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


  function dec2hex($dec, $digits = false) {
    $hex = '';
    $sign = $dec < 0 ? false : true;
    while ($dec) {
      $hex .= dechex(abs(bcmod($dec, '16')));
      $dec = bcdiv($dec, '16', 0);
    }
    if ($digits) {
      while (strlen($hex) < $digits) {
        $hex .= '0';
      }
    }
    if ($sign) {
      return strrev($hex);
    }
    for ($i = 0; isset($hex[$i]); $i++) {
      $hex[$i] = dechex(15 - hexdec($hex[$i]));
    }
    for ($i = 0; isset($hex[$i]) && $hex[$i] == 'f'; $i++) {
      $hex[$i] = '0';
    }
    if (isset($hex[$i])) {
      $hex[$i] = dechex(hexdec($hex[$i]) + 1);
    }
    return strrev($hex);
  }


  $secretKey = "742a980d88544278bda294c3555f22f171f728547f7341acb6ca7211cb9a9a33";
  $signature = "7f441d9df8f8e1015b5f699285a0a6d3";
  echo getConsumerSecretFromSignature($signature);


