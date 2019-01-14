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
$serial->deviceOpen();

/*
    while(1){
		// read from it,
        $read = $serial->readPort();
			// and loop through the window byte by byte
		    for($i = 0; $i < strlen($read); $i++){  
		        $hexByte =  dechex (ord($read[$i]))."";
			    echo $hexByte;
		    }
	}
*/

/*
 
 WARNING !!! LINE 1 - 20 wird direkt in LaTeX übernommen! 

*/



// To write into
//$serial->sendMessage("Hello !");
//$sql = "UPDATE tabelle SET spalte = 'Wert' WHERE id = 1";
//$mysqli->query($sql);


// Switch that is enabled when our startcode "aa" appears
// and is disabled either when the end code "c0"?  is reached
// or when the counter exceeds 50 chars.
$readSwitch = false;

$charsRead = 0;

$data = array();

$hexstrVolt = "";
$hexstrAmp  = "";
$hexstrWatt = "";
$hexstrLoad = "";
$hexstrWork = "";
$hexstrTime = "";
$hexstrTimeOld ="";

$mesVolt    ="";
$mesAmp     ="";
$mesWatt    ="";
$mesLoad    ="";
$mesWork    ="";
$mesTime    ="";
$mesMin     ="";

$debHex = false;

$mysqli = new mysqli("localhost", "solar", "solar", "SolarMeasurement");
if ($mysqli->connect_errno) {
    die("Verbindung fehlgeschlagen: " . $mysqli->connect_error);
}
echo "Test2rgreg";
$sql = "INSERT INTO MeasurementData (voltage, current, power, charge, work, time) VALUES (?,?,?,?,?,?)";
$statement = $mysqli->prepare($sql);
$statement->bind_param('iiiiii', $mesVolt, $mesAmp, $mesWatt, $mesLoad, $mesWork, $mesTime);
 
//Determine if a variable is set and is not NULL
if(isset($read)){
   while(1){
       $read = $serial->readPort();
       print_r(" (size ".strlen($read). " ) ");
	   
	   // If Data Overlaps the strlen, the current byte is rejected.
	   $readSwitch = false;
	   $charsRead  = 0;
	   $data       = array();
       for($i = 0; $i < strlen($read); $i++)
       {  
          $hexByte =  dechex (ord($read[$i]))."";
	// Wir lesen sobald das Startbyte "aa" erkannt wird.
          if($hexByte == "aa"){
		  $readSwitch = true;
          }

	// Erstes Byte war AA, zweites Byte muss 1c sein, sonst verwerfen.
	 if($charsRead == 1 && $hexByte != "1c"){
		$charsRead = 0; 
	 	$readSwitch = false;
		$data       = array();
	 } 
	 if($charsRead >= 29){
	 	$readSwitch = false;
		$charsRead  = 0;
		
		echo "\n\n";
		$hexstrVolt = $data[4].$data[5];
		$mesVolt = hexdec($hexstrVolt);
		if($debHex) echo "Spannung: 0x $hexstrVolt $mesVolt V ";
		echo "Spannung ".($mesVolt/100)." V";		

		$hexstrAmp = $data[6].$data[7];
		$mesAmp    = hexdec($hexstrAmp);
		if($debHex) echo "Strom: 0x".$hexstrAmp." $mesAmp A ";
		echo "Strom: ".($mesAmp/10)." A";

		$hexstrWatt = $data[10].$data[11];
		$mesWatt    = hexdec($hexstrWatt);
		if($debHex) echo "Leistung: 0x".$hexstrWatt." $mesWatt W ";
		echo "Leistung: ".($mesWatt/100)." W";

		$hexstrLoad = $data[14].$data[15];
		$mesLoad    = hexdec($hexstrLoad);
        if($debHex) echo "Ladung: 0x".$hexstrLoad." $mesLoad Ah ";
		echo "Ladung ".($mesLoad/1000)." Ah";

		
		$hexstrWork = $data[18].$data[19];
		$mesWork    = hexdec($hexstrWork);
		if($debHex) echo "Arbeit: 0x".$hexstrWork." $mesWork Wh ";
 		echo "Arbeit: ".($mesWork/1000)." Wh";

		$hexstrTimeOld = $hexstrTime;
		$hexstrTime = $data[22].$data[23];
		$mesTime    = hexdec($hexstrTime);
		
		
		$mesTimeStr = gmdate("H:i:s", $mesTime);
		echo "Zeit: $mesTimeStr";

		
		//Hier wird die Zeile in die Datenbank eingefügt!
		$statement->execute();
		$new_id = $statement->insert_id;
		echo "Neue id= ".$new_id." \n ";

		//if($hexstrTimeOld == $hexstrTime){
		//	break;
		//}
						

		
		//echo "\n";			
//		var_dump($data);

		echo "\n";
		$data = array();
         }

	if($readSwitch){
		echo $hexByte." "; 
		$charsRead++;
		array_push($data,$hexByte);
        }


}
       print_r("\n");
       sleep(1);
  }// end while
}// end if  


// If you want to change the configuration, the device must be closed
$serial->deviceClose();

// We can change the baud rate
//$serial->confBaudRate(2400);

// etc...
//
//
/* Notes from Jim :
> Also, one last thing that would be good to document, maybe in example.php:
>  The actual device to be opened caused me a lot of confusion, I was
> attempting to open a tty.* device on my system and was having no luck at
> all, until I found that I should actually be opening a cu.* device instead!
>  The following link was very helpful in figuring this out, my USB/Serial
> adapter (as most probably do) lacked DTR, so trying to use the tty.* device
> just caused the code to hang and never return, it took a lot of googling to
> realize what was going wrong and how to fix it.
>
> http://lists.apple.com/archives/darwin-dev/2009/Nov/msg00099.html

Riz comment : I've definately had a device that didn't work well when using cu., but worked fine with tty. Either way, a good thing to note and keep for reference when debugging.
 */
