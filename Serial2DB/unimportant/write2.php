#!/usr/bin/php
<?php
include './PHP-Serial/src/PhpSerial.php';

// Let's start the class
$serial = new PhpSerial;

// First we must specify the device. This works on both linux and windows (if
// your linux serial device is /dev/ttyS0 for COM1, etc)
$serial->deviceSet("/dev/ttyUSB0");

// We can change the baud rate, parity, length, stop bits, flow control
$serial->confBaudRate(57600);
$serial->confParity("none");
$serial->confCharacterLength(8);
$serial->confStopBits(2);
$serial->confFlowControl("none");

// Then we need to open it
$serial->deviceOpen('w+');

// We may need to return if nothing happens for 10 seconds
stream_set_timeout($serial->_dHandle, 10);

// SMS inbox query - mode command and list command

echo "\nAA ist: ".chr(hexdec("AA");
echo "\n04 ist: ".chr(hexdec("04");
echo "\n01 ist: ".chr(hexdec("01");
echo "\n01 ist: ".chr(hexdec("01");
echo "\nB0 ist: ".chr(hexdec("B0");

$sendRequest = chr(hexdec("AA")). chr(hexdec("04")) .chr(hexdec("01")) .chr(hexdec("01")). chr(hexdec("B0"));
echo "\nKompletter String ist: $sendRequest";
/*
$serial->sendMessage("AT",1);
var_dump($serial->readPort());
$serial->sendMessage("AT+CMGF=1\n\r",1);
var_dump($serial->readPort());
$serial->sendMessage("AT+CMGL=\"ALL\"\n\r",2);
var_dump($serial->readPort());

// If you want to change the configuration, the device must be closed
*/
$serial->deviceClose();
