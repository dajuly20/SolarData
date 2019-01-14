#!/usr/bin/php
<?php
//Throws error and shuts down on it's own if not found.
require 'PHP-Serial/src/PhpSerial.php';

$continueLoop = true;

// Verwendung von Ticks; wird seit PHP 4.3.0. benötigt
declare(ticks = 1);

// Signalverarbeitungsfunktion
function sig_handler($signo) 
{

     switch ($signo) {
         case SIGTERM:
             // Aufgaben zum Beenden bearbeiten
			 echo "SIGTERM abgefangen...\n";
             $GLOBALS["continueLoop"] = false;
             break;
         case SIGHUP:
             // Aufgaben zum Neustart bearbeiten
			 echo "SIGHUP abgefangen...\n";
             $GLOBALS["continueLoop"] = false;
			break;
         case SIGUSR1:
             echo "SIGUSR1 abgefangen...\n";
			 $GLOBALS["continueLoop"] = false;
             break;
		case SIGINT:
			 echo "\nSIGINT abgefangen... exit nicely! :-)\n";
			 $GLOBALS["continueLoop"] = false;
             break;
         default:
             echo "anderes signal abgefangen...\n";
			 $GLOBALS["continueLoop"] = false;
             break;
     }

}

// Signalverarbeitung einrichten
pcntl_signal(SIGTERM, "sig_handler");
pcntl_signal(SIGHUP,  "sig_handler");
pcntl_signal(SIGUSR1, "sig_handler");
pcntl_signal(SIGINT,  "sig_handler");



//Wenn das Display mit-angesclossen ist auf false,
// sonst auf true;
$requestData = true; 

// Let's start the class
$serial = new PhpSerial;

// First we must specify the device. This works on both linux and windows (if
// your linux serial device is /dev/ttyS0 for COM1, etc)
$serialPortUri = "/dev/ttyUSB0";
$serial->deviceSet($serialPortUri);

// We can change the baud rate, parity, length, stop bits, flow control
$serial->confBaudRate(57600);
$serial->confParity("none");
$serial->confCharacterLength(8);
$serial->confStopBits(2);
$serial->confFlowControl("none");

// Then we need to open it
$serial->deviceOpen('w+');
$sendRequestStr = chr(hexdec("AA")).chr(hexdec("04")).chr(hexdec("01")) .chr(hexdec("01")). chr(hexdec("B0"))."";

// Verbosity levels:
// 0 		only startup and closure outputs + errors
// 1 (V) 	+ output on discarded values
// 2 (VV)	+ output on changed values 
$verbosity = 0;
$verbosityStr = "";

if(isset($argv[1]) && isset($argv[2]) && ($argv[1] == "-v" || $argv[1] == "--verbosity")){
	$verbosityStr = $argv[2];
	$verbosity = strlen($verbosityStr);
	array_splice($argv, 0, 2);
}

if(isset($argv[1]) && ($argv[1] == "-h" || $argv[1] == "--help")){
	echo "Reads from serial Port and pushes into database\n use -v or --verbose VV for biggest verbosity level.\n\n";
	array_splice($argv, 0, 2);
	die();
}


trigger_error (date("m.d.y H:i:s"). " Started Script as user ".exec("whoami")." Verbosity: $verbosityStr Sending AA040101B0 to shunt module.  " , E_USER_NOTICE );
echo "\n\nStarted & Running!\nCTRL + C to quit.\n";

//aa 00 01 af
//$sendRequestStr = chr(hexdec("AA")).chr(hexdec("00")).chr(hexdec("01")) .chr(hexdec("AF"))."";

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


// Switch that is enabled when our startcode "aa" appears
// and is disabled either when the end code "c0"?  is reached
// or when the counter exceeds 50 chars.
$readSwitch = false;
$charsRead = 0;

// raw data (byte by byte)
$data = array();

// Processed Data 
$lastDataSet = array();
$lastDataSet["vlt"]    = "";
$lastDataSet["cur"]    = "";
$lastDataSet["pow"]    = "";
$lastDataSet["chg"]    = "";
$lastDataSet["wrk"]    = "";
$lastDataSet["time"]   = ""; 

$currentDataSet = array();
$currentDataSet["vlt"]    = "";
$currentDataSet["cur"]    = "";
$currentDataSet["pow"]    = "";
$currentDataSet["chg"]    = "";
$currentDataSet["wrk"]    = "";
$currentDataSet["time"]   = ""; 
$currentDataSet["dbId"]   = "";

$hexstrVolt = "";
$hexstrAmp  = "";
$hexstrWatt = "";
$hexstrLoad = "";
$hexstrWork = "";
$hexstrTime = "";
$hexstrTimeOld ="";

$debHex = false;

//AlternateOutput
$a = 0;

$mysqli = new mysqli("localhost", "solar", "solar", "MeasurementData");
if ($mysqli->connect_errno) {
    die("Database connection failed!" . $mysqli->connect_error);
}




if($requestData) $serial->sendMessage($sendRequestStr,1);
$read = $serial->readPort();
//Determine if a variable is set and is not NULL
$isFirstDataset = true;

$iUnchanged = 0;
$iInsane    = 0;

if(!isset($read)){
	die("Cannot read from serial Port $serialPortUri");
}

// when signal is detected, loop is exited.
while($continueLoop){
	if($requestData) $serial->sendMessage($sendRequestStr,1);
	$read = $serial->readPort();
	if($verbosity >=3){ print_r(" (size ".strlen($read). " ) ");}

	// If Data Overlaps the strlen, the current dataset is rejected.
	$readSwitch = false;
	$charsRead  = 0;
	$data       = array();
	for($i = 0; $i < strlen($read); $i++)
	{  
		$hexByte =  dechex (ord($read[$i]))."";
		// When byte has only 1 digit, a 0 is put in front.
		if(strlen($hexByte) == 1){
			$hexByte = "0".$hexByte;
		}
	  
		// When the first byte is AA, we start listening...
		if($hexByte == "aa"){
			$readSwitch = true;
		}

		// ... but immediately discard it, when the second byte is not 1c
		if($charsRead == 1 && $hexByte != "1c"){
			$charsRead = 0; 
			$readSwitch = false;
			$data       = array();
		} 
		
		// As long as the reasSwitch is turned on, we push each byte to the
		// $data array, and increment $charsRead.
		if($readSwitch){
			// echo $hexByte." "; 
			$charsRead++;
			array_push($data,$hexByte);
		}
		
		// When chars read exeeds 27 the dataset is complete.
		if($charsRead >= 28){
			$readSwitch = false;
			$charsRead  = 0;
			$currentDataSet = evaluateData($data);
	
			// Check whether the data has changed	
			if(!dataHasChanged($currentDataSet,$lastDataSet)){
				$iUnchanged++;
				if($verbosity >=3){echo( $iUnchanged % 2) ? "%":"*";}
				//echo "DATEN sind gleich geblieben (wird nicht eingetragen!)\n";
				//echo "Last Volt: ".$lastDataSet["vlt"]." NowVolt: $mesVolt\n";
				//echo "LastCur: ".  $lastDataSet["cur"]." NowCurr: $mesAmp\n";
			}
	
			// And if so, if the data is correct.
			elseif(!sanityCheck($currentDataSet,$lastDataSet,$isFirstDataset)){
				if($verbosity >=3){ echo( $iInsane % 2) ? " xxx ":" XXX ";}
				$iInsane++;
				//echo "Daten sind ungültig und werden verworfen";
			}
		
			// If data has changed, and is correct it will be pushed to the database.
			else{
				$currentDataSet["stats"] = array();
				$currentDataSet["stats"]["insane"] = $iInsane;
				$currentDataSet["stats"]["unchanged"] = $iUnchanged;
				
				$currentDataSet["dbId"] = insertIntoDatabase($currentDataSet,$mysqli);
				
				if($verbosity >= 2) printCurrent($currentDataSet);
				
				//Finally a copy of current data is made and saved as lastDataSet.
				$lastDataSet = $currentDataSet;
	
				// And counters are beeind reset.
				$iInsane = 0;
				$iUnchanged = 0;
				$isFirstDataset =false;
			}
		
			//Empty Array
			$data = array();
		}
	}
	
   //print_r("\n");
   if($continueLoop) sleep(1);
}// end while


// Close Database connection.
mysqli_close($mysqli);

// If you want to change the configuration, the device must be closed
$serial->deviceClose();


echo "Closed Database connection and serial device.\nShut down complete.\n";



function printCurrent($currentDataSet){
	echo "\nVeränderung erkannt. in Datenbank eintragen! \n";
	echo "Vorher ".$currentDataSet["stats"]["unchanged"]." (unveränderte) und ".$currentDataSet["stats"]["insane"]." (out of bounds) Datensätze verworfen!\n";
	echo "Strom: ".   ($currentDataSet["cur"]/10)."A";
	echo "Spannung ". ($currentDataSet["vlt"]/100)."V";		
	echo "Leistung: ".($currentDataSet["pow"]/1000)."W";
	echo "Ladung ".   ($currentDataSet["chg"]/1000)."Ah";
	echo "Arbeit: ".  ($currentDataSet["wrk"]/1000)."Wh";
	echo "Neue id= ".$currentDataSet["dbId"]."\n\n ";
	//echo "Zeit: $mesTimeStr\n";
}

function insertIntoDatabase($currentDataSet,&$mysqli){						
	//Hier wird die Zeile in die Datenbank eingefügt!
	$sql = "INSERT INTO SolarPower (voltage, current, power, charge, work, time) VALUES (?,?,?,?,?,?)";
	$statement = $mysqli->prepare($sql);
	$statement->bind_param('iiiiii', 
							$currentDataSet["vlt"],
							$currentDataSet["cur"],
							$currentDataSet["pow"],
							$currentDataSet["chg"],
							$currentDataSet["wrk"],
							$currentDataSet["time"]);
	$statement->execute();
	$new_id = $statement->insert_id;
	return $new_id;	
}

// Data is bytewisly extracted and processed depending on type of value.
// e.g.  byte 0 is AA, 1 is 1C .. 
function evaluateData($data){
	$debHex = false;
	$hexstrVolt = $data[4]  . $data[5];
	$hexstrAmp 	= $data[6]  . $data[7];
	$hexstrWatt = $data[9]  . $data[10] . $data[11];
	$hexstrLoad = $data[12] . $data[13] . $data[14] . $data[15]; 
	$hexstrWork = $data[16] . $data[17] . $data[18] . $data[19];
	$hexstrTime = $data[20] . $data[21] . $data[22] . $data[23];
	
	$currentDataSet["cur"]    = _hex16dec($hexstrAmp, 16);
	$currentDataSet["vlt"]    =  hexdec($hexstrVolt);
	$currentDataSet["pow"]    =  hexdec($hexstrWatt);
	$currentDataSet["chg"]    = _hex32dec($hexstrLoad, 16);
	$currentDataSet["wrk"]    =  hexdec($hexstrWork);
	$currentDataSet["time"]   =  hexdec($hexstrTime); 		
	
	// If the current is negative, make Power negative too! 
	if($currentDataSet["cur"] < 0){
		$currentDataSet["pow"] = $currentDataSet["pow"] * -1;
	}
	
	//$mesTimeStr 			  =  gmdate("H:i:s", $mesTime);
	
	// Debugging only
	if($debHex){
		echo "Strom: 0x"    . $hexstrAmp;
		echo "Spannung: 0x" . $hexstrVolt;
		echo "Leistung: 0x" . $hexstrWatt;
		echo "Ladung: 0x"   . $hexstrLoad; 
		echo "Arbeit: 0x"   . $hexstrWork;
	}
	
return $currentDataSet;
}


		
		
	


function _hex16dec($str, $base) {
    // Function to convert 16bit binary numbers to integers using two's complement
    //$num = bindec($bin);
	$bin = base_convert ( $str , $base , 2 );
	$num = bindec($bin);
    if($num > 0xFFFF) { return false; }
    if($num >= 0x8000) {
        return -(($num ^ 0xFFFF)+1);
    } else {
        return $num;
    }
}

	
function _hex32dec($str, $base) {
  
  // Function to convert 32bit binary numbers to integers using two's complement
    //$num = intval($str, $base);
	$bin = base_convert ( $str , $base , 2 );
	$num = bindec($bin);
    if($num > 0xFFFFFFFF) { return false; }
    if($num >= 0x80000000) {
        return -(($num ^ 0xFFFFFFFF)+1);
    } else {
        return $num;
    }
}


// checks if *relevant* information has changed 
function dataHasChanged($o,$n){

	return 
		!(
		$o["vlt"]    ==  $n["vlt"] &&
		$o["cur"]    ==  $n["cur"]
		);
}


// Checks Values to be within a numerical range.
function sanityCheck($c,$o,&$isFirst){
	//d = current dataset, $o= old dataset
	$dbg  = false;
	$eAdd = "";
			
	$eVlt  = !($c["vlt"] >=     5*100 	&& 	$c["vlt"] <= 25  *100)  ;	 	// Min Volt = 5 | Max Volt 25  (multiplikator 100)
	$eCur  = !($c["cur"] >=   -12*10  	&&	$c["cur"] <= 200 *10)   ;  		// 140Wp Panel /12V = -11A  | 1500W Inverter /12 + x = 200 Amps
	$ePow  = !($c["pow"] >=  -200*1000	&&	$c["pow"] <= 2000*1000) ;  		// Min is 140W Panel + x = 200W . Max: 1500W Inverter + x = 2000   
	$eChg  = !($c["chg"] >=  -500*1000	&&	$c["chg"] <=  500*1000) ;  		// Capacity of Battery is 250 Ah. this value doubled should be sufficient.     
	$eMul  = !(($c["vlt"] *  abs($c["cur"])) == abs($c["pow"] ))    ;  		// Voltage times *absolute* current must equal the power.

	
	//If any error occurs, dataset will be discarded, and warning is printed.
	if($eVlt || $eCur || $ePow || $eChg ||$eMul){
		if($eVlt) $eAdd .= " volts: "				.$c["vlt"];
		if($eCur) $eAdd .= " current: "				.$c["cur"];
		if($ePow) $eAdd .= " power: "				.$c["pow"];
		if($eChg) $eAdd .= " charge: "				.$c["chg"];
		if($eMul) $eAdd .= " Amp X Volts = Watts : ".$c["cur"] . "x" . $c["vlt"] . "!=" . $c["pow"];
		
		if($GLOBALS["verbosity"] <= 1) trigger_error (date("m.d.y H:i:s")." dataset discarded. Did not pass sanity check on: $eAdd " , E_USER_WARNING );
		return false;
	}
	
	//if this is the first Dataset, we cannot compare old and new... 
	if($isFirst) return true;
	
	//Volts: Absolute min Value is 1224 absolute max is 1311 which is 7% ... so 15 shoud do the trick.
	//       also it cant be negative, so zero range is 0.
	$eVlt = !percentCompare($c,$o,"vlt",$driftRange = 15, $zeroRange = 0);
	
	// Current can drop or raise rapidly, if consumers are switched on.. 
    //$eCur = percentCompare($c,$o,"cur",$driftRange = 60, $zeroRange = 10)
    
	//Power: same for power
	//$ePow  = percentCompare($c,$o,"pow",$driftRange = 15, $zeroRange = 0);
    
	//Charge: Large values (e.g.  -54500, [Battery holds] -200Ah ), 
	//         normally battery should be empty before values can be positive..
	//         we started with an empty battery... When its full, we maybe set it to zero.. 
	//		   Seeting to zero happened accidentially ... now trying to fix ;) 
	$eChr = !percentCompare($c,$o,"chg",$driftRange = 5, $zeroRange = 125000);
	
	//eWork Large values normally it cant  
	//$eWrk = !percentCompare($c,$o,"wrk",$driftRange = 15, $zeroRange = 30);
	
	if($eVlt || $eChr){ //|| $eWrk ){
		$eAdd = "";
	
		// Error is thrown in percentageCompare! 
		//if($eVlt) $eAdd .= " volts: "	.$c["vlt"];
		//if($eChr) $eAdd .= " charge: "	.$c["cur"];
		//if($eWrk) $eAdd .= " work: "	.$c["wrk"];
		//trigger_error (date("m.d.y H:i:s")." dataset discarded. Did not pass sanity check on: %%% $eAdd %%%" , E_USER_WARNING );
		
	return false;
	}
	
	
return true;


	
	 
}


//                                                                          NOW offset per Hour  1500W / 12V = 125 A/h /h => 125 000    
function percentCompare($currentArray,$oldArray,$index,$allowedDriftRange = 30, $zeroRange = 125000){
	// Was wenn der erste Datensatz bereits korrupt ist? 
	// welcher Datensatz ist der letzte? Der Letzte Valide, oder der absolut letzte?
	//
	// Was passiert beim überschreiten von null ? 
	
	// übernächster wert:  33772
	// cuttent = ,2816 old= 3,4021  range = 30% => 70% - 130% percent 
	//  old*0.7 = 2,38147      old*1.3 = 4,42273
	//    größer als     3,4021   kleiner als .... ALLES OK !!!
	
	// cuttent = -,2816 old= -3,4021  range = 30% => 70% - 130% percent 
	// old*0,7 = -2,38147     old*1.3 = -4,42273
	//                 -3,3772
	
	//get Time offset between datasets.
	$timeOffset = abs($oldArray["time"] - $currentArray["time"]);
	
	$eAdd = "";
	// if this Variable is still true at the end, the value is in range.
	$isValid = true; 
	
	//Range in percent e.g. 30 
	$allowedDriftRange = abs($allowedDriftRange);
	
	// Extracting *O*ld and *c*urrent values.
	$oVal = 	$oldArray    [$index];	
	$cVal = 	$currentArray[$index];
   
	// When identical, no calculation  is needed. 
	
	if($oVal == $cVal){
	//trigger_error (date("m.d.y H:i:s")." value <$index> '$cVal' is identical to '$oVal' [LastVal: $oVal; drift range: $allowedDriftRange; zero range: $zeroRange]" , E_USER_NOTICE );
	return true; }// DO NOT REMOVE DEV by zero hazard. 
	
	// When the value is in zeroRange distance to Zero
	if((abs($cVal) - $zeroRange < 0) || (abs($oVal) - $zeroRange < 0 )){
		$nearZero = true;
		$eAdd .= " near Zero";
	}
	else{
		$nearZero = false;
	}	
	
	// Are we crossing zero?
	$crossZero = (($oVal < 0) XOR ($cVal < 0)) ? true : false;
    
	
	// We're not crossing zero and none value is zero.
	// So we take the *absolute* value, and compare it on percentage to the newer value.
	//if($oVal != 0 && $cVal != 0 && !$crossZero && !$nearZero){  
	if($zeroRange == 0){
		$oValAbs =  abs($oVal);
		$cValAbs =  abs($cVal);
		$eAdd .= ">percent<";
		
		// Sort for bigger and smaller.
		if($oValAbs > $cValAbs) {
			$valBig   = $oValAbs;
			$valSmall = $cValAbs;
		}
		else{
			$valBig   = $cValAbs;
			$valSmall = $oValAbs;
		}
		$driftPerc = ((($valBig / $valSmall) - 1) * 100);
		
		//Add time multiplikator
		if($driftPerc  >= $allowedDriftRange){
			$eAdd .= " drift: $driftPerc";
			$isValid = false;
		}		
	}
	
	// (Cross)Zero Values must be treated seperatly and compared numerically. Imagin a one Value 10 and the other 0... 
	// how much percent is that?  (Furthermore it avoids devision by zero)
	else{
	if($crossZero){ $eAdd .="crossZero";}
	$eAdd .= " *zero* (ALL IS ZERO NOW)";	
	
	// Zero Range is given in "perHour"
	$zeroRangePerSecond = abs($zeroRange) / 60 / 60;
	$zeroRangeSeconds = $zeroRangePerSecond * $timeOffset;
	if($oVal > $cVal){
		$valBig 	= $oVal;
		$valSmall 	= $cVal;
	}
	else{
		$valBig 	= $cVal;
		$valSmall 	= $oVal;
	}	
	$isValid =  ($valSmall + $zeroRangeSeconds) >= $valBig;
	}
	
	
	if(!$isValid){
		if($GLOBALS["verbosity"] >= 1) trigger_error (date("m.d.y H:i:s")." value <$index> '$cVal' discarded for variation on $eAdd range. [LastVal: $oVal; TimeOffset: $timeOffset; drift range: $allowedDriftRange; zero range: $zeroRange]" , E_USER_WARNING );
		return false;
	}else{
		return true;
	}
	
	
}

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
