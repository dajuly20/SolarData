#!/usr/bin/php
<?php

foreach($argv as $value)
{
  echo "$value\n";
}



function _bin16dec($bin) {
    // Function to convert 16bit binary numbers to integers using two's complement
    $num = bindec($bin);
    if($num > 0xFFFF) { return false; }
    if($num >= 0x8000) {
        return -(($num ^ 0xFFFF)+1);
    } else {
        return $num;
    }
}

function _bin32dec($bin) {
    // Function to convert 32bit binary numbers to integers using two's complement
    $num = bindec($bin);
    if($num > 0xFFFFFFFF) { return false; }
    if($num >= 0x80000000) {
        return -(($num ^ 0xFFFFFFFF)+1);
    } else {
        return $num;
    }
}

function _bin8dec($bin) {
    // Function to convert 8bit binary numbers to integers using two's complement
    $num = bindec($bin);
    if($num > 0xFF) { return false; }
    if($num >= 0x80) {
        return -(($num ^ 0xFF)+1);
    } else {
        return $num;
    }
	
}

echo "Benutze Zahl: $argv[1]\n";
$binNum = string base_convert ( "FF" , 16 , 2 ) 
echo "Binär $binNum \n";

//echo "Ergebnis: "._bi

?>
