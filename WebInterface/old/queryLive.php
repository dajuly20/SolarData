<?php

require "sqlConn.php";
require "sqlQueries.inc.php";
$dataArray  = array();
$errArray   = array();
$errArray[0] = false;
$errArray[1] = "";
$outArray   = array();
$infArray   = array();


$id = 100;

$datePattern = "Y-m-d H:i:s";
function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}




if(isset($_REQUEST["date"]) && validateDate($_REQUEST["date"],$datePattern)){
	$dateInput = $_REQUEST["date"]; 
	$dateInput = str_replace("\n","",$dateInput);
	$dateInput = htmlspecialchars($dateInput,ENT_QUOTES);
	$date = $dateInput;
}
elseif(!isset($_REQUEST["date"]) || $_REQUEST["date"] == "" || $_REQUEST["date"] == "now"){
	$date =  date($datePattern);  
}

else{
	jsonDie("Date {$_REQUEST["date"]} is invalid. ");
}

$infArray[0] = "Date used: $date";



	  

function jsonDie($msg){
	$errArray[0] = true;
	$errArray[1] = $msg;
	$outArray["err"] = $errArray;
	
	die(json_encode($outArray));
}

if  (!isset($_REQUEST["show"])){
	jsonDie("Url Parameter 'show' not set.");
}
	
	
if (!isset($sql[$_REQUEST["show"]])){
	jsonDie("Url Parameter 'show' has invalid value {$_REQUEST['show']} ");
}


	  

$statement = $mysqli->prepare($sql[$_REQUEST["show"]]);

if ( false===$statement ) {
  // and since all the following operations need a valid/ready statement object
  // it doesn't make sense to go on
  // you might want to use a more sophisticated mechanism than die()
  // but's it's only an example
  jsonDie('prepare() failed: ' . htmlspecialchars($mysqli->error));
}




 $rc = $statement->bind_param('s', $date);
/// bind_param() can fail because the number of parameter doesn't match the placeholders in the statement
// // or there's a type conflict(?), or ....
  if ( false===$rc ) {
   // again execute() is useless if you can't bind the parameters. Bail out somehow.
   jsonDie('bind_param() failed for '.$_REQUEST["show"].': ' . htmlspecialchars($stmt->error));
  }

$rc = $statement->execute();

if($rc){
$result = $statement->get_result();

$wishedCols= $_REQUEST["wCols"];
if(isset($wishedCols)){
	$wColArr = explode (",",$wishedCols);	
}

$ticksNumbers = $_REQUEST["tNum"];
if(isset($ticksNumbers)){
	$ticksNumbersArr = explode (",",$ticksNumbers);		
}

$ticksArr = array();
$dataArr  = array();
$i = 0;
header('Content-Type: application/json');
while($row = $result->fetch_array(MYSQLI_ASSOC)) {
	// If there isnt "wished" colums return them all
	if(isset($wColArr) && count($wColArr)){
		$tmprow = array();
		foreach($row as $key => $val){
			if( in_array($key, $wColArr)){
				$tmprow[$key] = $val;
			}
		}
		$dataArray[] = $tmprow; 
	}
	
	//First field is Ticks, second data.
	elseif(isset($ticksNumbers) && count($ticksNumbersArr)){
	
		$date = $row[$ticksNumbersArr[0]];
		$ticksArr[$i]   =  [$date, floatval(	$row[$ticksNumbersArr[1]])	];
		$dataArr [$i]  	=  [$date, floatval(	$row[$ticksNumbersArr[2]])	];
		$dataArr2[$i]  	=  [$date, floatval(	$row[$ticksNumbersArr[3]])	];
		$dataArr3[$i]  	=  [$date, floatval(	$row[$ticksNumbersArr[4]])	];
		$i++;
		
	}
		
	
	// Otherwise loop through row 
	else{
		$dataArray[] = $row;
	}
	
}

// Array must be reassembled outside the loop, when sorted that way.
if(isset($ticksNumbers) && count($ticksNumbersArr)){
	$dataArray[0] = $ticksArr;
	$dataArray[1] = $dataArr;
	$dataArray[2] = $dataArr2;
	$dataArray[3] = $dataArr3;
}


 }
else{
	$errArray[0] = true;
	$errArray[1] = 'execute() failed: ' . htmlspecialchars($stmt->error);
	$errArray[2] = 'mysqli error: ' . $mysqli->error;
}


$outArray["inf"] = $infArray;
$outArray["err"] = $errArray;
$outArray["res"] = $dataArray;

echo json_encode($outArray);

?>

