<?php

function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}	  

function jsonDie($msg){
	$errArray[0] = true;
	$errArray[1] = $msg;
	$outArray["err"] = $errArray;
	
	die(json_encode($outArray));
}


function getLimitOptions($show, $limitWished = -1, $orderWhished){

	$orderWhished = strtoupper($orderWhished);
	
	if(isset($orderWhished) && $orderWhished != "DESC" &&  $orderWhished != "ASC"){
		trigger_error ( date("m.d.y H:i:s") . " getLimitOptions, Parameter orderWhished is neither desc nor asc. Using default value  (instead of $orderWhished )"  , E_USER_WARNING );
	}

	if($limitWhished > 0 && is_numeric($limitWhished) && !is_int($limitWhished)){
			trigger_error ( date("m.d.y H:i:s") . " getLimitOptions, Parameter whished is not integer. Using default value instead of $limitWhished"  , E_USER_WARNING );
	}
	
	$limit["show"] = $show;
	
	$limit["wished"] = intval( $limitWished ); // limitWhished now santinized 
	$limit["offset"] = 0 ;
	
	// Getting the defs and min max boundaries.
	switch ($show){
		case "worstHour":
			$limit["stdNum"] = 50;
			$limit["max"]    = 400;
			$limit["min"]    = 1;
			$limit["order"]  = "desc";
		break;
		
		case "bestHour": 
			$limit["stdNum"] = 50;
			$limit["max"]    = 400;
			$limit["min"]    = 1;
			$limit["order"]  = "asc";	
		break;
		
		case "current":
			$limit["max"]    = 1;
			$limit["min"]    = 1;
			$limit["stdNum"] = 1;
			$limit["order"]  = "desc";
		break;
		
		case "dayStats":
		case "weekStats":
			$limit["max"]    = 50;
			$limit["min"]    = 1;
			$limit["stdNum"] = 24; //The query should not return more more rows anyway.
			$limit["order"]  = "desc";
		break;
			
		case "bestDay":
			
			$limit["max"]    = 50;
			$limit["min"]    = 1;
			$limit["stdNum"] = 5;
			$limit["order"]  = "asc";
			break;
		case "worstDay":
			$limit["max"]    = 50;
			$limit["min"]    = 1;
			$limit["stdNum"] = 5;
			$limit["order"]  = "desc";
		break;	
	}
	
	
	// Is there a wished value, and if so, is it within boundaries?
	if( isset($limit["wished"] ) && ( $limit["wished"] >= $limit["min"] ) && ( $limit["wished"] <= $limit["max"] ) ){
		$limit["num"] = $limit["wished"];	
		
	}
	
	else{
		$limit["num"] = $limit["stdNum"];
	}
	
	if($orderWhished == "ASC" || $orderWhished == "DESC"){
		$limit["order"] = $orderWhished;
	}
	
	
	
	return $limit;			 
}	 
?>