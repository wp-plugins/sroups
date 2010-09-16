<?php

$ch = curl_init(base64_decode(urldecode($_GET['url'])));
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_POST));
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
//curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  // RETURN THE CONTENTS OF THE CALL
$Rec_Data = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);
header('HTTP/1.1 ' . $info['http_code']);
header('Content-Type: ' . $info['content_type']);
echo $Rec_Data;
