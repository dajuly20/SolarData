<?php

require "./phpinc/sqlConn.inc.php";
require "./phpinc/sqlQueries.inc.php";
require "./phpinc/misc.inc.php";








$dataArray  = array();
$errArray   = array();
$errArray[0] = false;
$errArray[1] = "";
$outArray   = array();
$infArray   = array();


$id = 100;

$datePattern = "Y-m-d H:i:s";





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

if  (!isset($_REQUEST["show"])){
	jsonDie("Url Parameter 'show' not set.");
}
	



$sqlOpts = getLimitOptions($_REQUEST["show"], @$_REQUEST["numRows"], @$_REQUEST["order"]);

$sqlQuery  = getSqlQuery($_sqlTableNameFull,$sqlOpts);

$statement = $mysqli->prepare($sqlQuery);

if ( false===$statement ) {
  // and since all the following operations need a valid/ready statement object
  // it doesn't make sense to go on
  // you might want to use a more sophisticated mechanism than die()
  // but's it's only an example
  jsonDie('prepare() failed: ' . htmlspecialchars($mysqli->error));
}




 $rc = $statement->bind_param('sii', $date,$limit["offset"],$sqlOpts["num"]);
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
			$colPos = array_search($key, $wColArr);
			
			if( in_array($key, $wColArr)){
				//Puta in the new array, at the position where the Key was found... 
				// e.g. if wCols=date,chgDiff,linkDate then whe get succession 0210, which is sorted later to be consistend.
				$tmprow[$colPos] = is_numeric($val) ? floatval($val) : $val;
			}
		}
		// Sorts arra by numerical index value so we have normal [0] [1] [2] 
		ksort($tmprow, SORT_NUMERIC);
		$dataArray[] = array_values($tmprow);  //Makes the array indexes numberic		
	}
	
	
	
	//First field is Ticks, second data.
	elseif(isset($ticksNumbers) && count($ticksNumbersArr)){
	   
		
		
		$ticksArr[$i]  	=  is_numeric($row[$ticksNumbersArr[0]]) ? floatval(	 $row[$ticksNumbersArr[0]])    : $row[$ticksNumbersArr[0]];
		$dataArr [$i]  	=  is_numeric($row[$ticksNumbersArr[1]]) ? floatval(	 $row[$ticksNumbersArr[1]])    : $row[$ticksNumbersArr[1]];
		$dataArr2[$i]  	=  is_numeric($row[$ticksNumbersArr[2]]) ? floatval(	 $row[$ticksNumbersArr[2]])    : $row[$ticksNumbersArr[2]];
		$dataArr3[$i]  	=  is_numeric($row[$ticksNumbersArr[3]]) ? floatval(	 $row[$ticksNumbersArr[3]])    : $row[$ticksNumbersArr[3]];
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

