<?php
function getSqlQuery($_sqlTableNameFull,$opts){
	

$order = $opts["order"];

	
$sql["dayStats"] = "
SELECT 
timediff(now(), min(curtime)) as 'footer-dateAge', 
DATE_FORMAT(NOW(), '%H:%i:%s') as 'footer-nowTime',
id as _cid, curtime as mynow,
DATE_FORMAT(curtime, '%Y-%m-%d 12:00:00') as linkDate,

DATE_FORMAT(curtime, '%H%:00') as date,
ROUND((select charge from MeasurementData.SolarPower WHERE max(_cid) =  id)/1000,2) as chg,
round((((select charge from MeasurementData.SolarPower WHERE max(_cid) =  id) - charge)/1000),2) as chgDiff,


round(min(current)/10,2) as 'cur-min', 
round(avg(current)/10,2) as 'cur-avg',
round(max(current)/10,2) as 'cur-max', 

round(min(power)/1000,2) as 'pow-min',
round(avg(power)/1000,2) as 'pow-avg', 
round(max(power)/1000,2) as 'pow-max'


FROM $_sqlTableNameFull 
WHERE DATE_FORMAT(curtime, '%Y-%m-%d') = DATE_FORMAT(?, '%Y-%m-%d')
GROUP BY HOUR(curtime) 
ORDER BY mynow $order
LIMIT ?,?;

";


$sql["weekStats"] = "
SELECT 
	
	DATE_FORMAT(NOW(), '%H:%i:%s') as 'footer-nowTime',
	id as _cid, 
	DATE_FORMAT(curtime, '%W') as weekday,
	DATE_FORMAT(curtime, '%d%.%m.%Y') as date,
	DATE_FORMAT(curtime, '%Y-%m-%d 12:00:00') as linkDate,
	curtime as mynow,
	
		ROUND((select charge from MeasurementData.SolarPower WHERE max(_cid) =  id)/1000,2) as chg,
		round((((select charge from MeasurementData.SolarPower WHERE max(_cid) =  id) - charge)/1000),2) as chgDiff,
      
        
       
       round(min(current)/10,2) as 'cur-min', 
	   round(avg(current)/10,2) as 'cur-avg',
       round(max(current)/10,2) as 'cur-max', 
                                       
       round(min(power)/1000,2) as 'pow-min',
       round(avg(power)/1000,2) as 'pow-avg', 
       round(max(power)/1000,2) as 'pow-max',
  
	   min(charge) as 'chg-min',
	   max(charge) as 'chg-max'
      
       
     FROM $_sqlTableNameFull
	
	 WHERE curtime >= DATE_SUB(?, INTERVAL 6 DAY)
	 GROUP BY date ORDER BY mynow $order
	 LIMIT ?,?;
";

$sql["bestDay"] = "
SELECT 
	? as useless,
	DATE_FORMAT(NOW(), '%H:%i:%s') as 'footer-nowTime',
	id as _cid, 
	DATE_FORMAT(curtime, '%W') as weekday,
	DATE_FORMAT(curtime, '%W %d%.%m.%Y') as date,
	DATE_FORMAT(curtime, '%W %d%.%m.%Y') as date,
	DATE_FORMAT(curtime, '%Y-%m-%d 12:00:00') as linkDate,
	
	
	curtime as mynow,
	
	ROUND((select charge from MeasurementData.SolarPower WHERE max(_cid) =  id)/1000,2) as chg,
	round((((select charge from MeasurementData.SolarPower WHERE max(_cid) =  id) - charge)/1000),2) as chgDiff,



    round(min(current)/10,2) as 'cur-min', 
	round(avg(current)/10,2) as 'cur-avg',
    round(max(current)/10,2) as 'cur-max', 

    round(min(power)/1000,2) as 'pow-min',
    round(avg(power)/1000,2) as 'pow-avg',
    round(max(power)/1000,2) as 'pow-max',


	min(charge) as 'chg-min',
	max(charge) as 'chg-max'
       
       
    FROM $_sqlTableNameFull
	GROUP BY date 
	ORDER BY chgDiff $order
    LIMIT ?,?;
";  

// Copy of best day with DESC for Asc
// what day was the most energy consumed?	  
$sql["worstDay"] = "
SELECT 
	
	DATE_FORMAT(?, '%H:%i:%s') as 'footer-nowTime',
	id as _cid, 
	DATE_FORMAT(curtime, '%W') as weekday,
	DATE_FORMAT(curtime, '%W %d%.%m.%Y') as date,
	curtime as mynow,
	
	ROUND((select charge from MeasurementData.SolarPower WHERE max(_cid) =  id)/1000,2) as chg,
	round((((select charge from MeasurementData.SolarPower WHERE max(_cid) =  id) - charge)/1000),2) as chgDiff,



    round(min(current)/10,2) as 'cur-min', 
	round(avg(current)/10,2) as 'cur-avg',
    round(max(current)/10,2) as 'cur-max', 

    round(min(power)/1000,2) as 'pow-min',
    round(avg(power)/1000,2) as 'pow-avg',
    round(max(power)/1000,2) as 'pow-max',


	min(charge) as 'chg-min',
	max(charge) as 'chg-max'
       
       
    FROM $_sqlTableNameFull
	GROUP BY date 
	ORDER BY chgDiff $order
    LIMIT ?,?;
";  




$sql["bestHour"] = "
		SELECT 
		
		DATE_FORMAT(?, '%H:%i:%s') as 'footer-nowTime',
		id as _cid, curtime as mynow,
	
       DATE_FORMAT(curtime, '%d%.%m.%Y %H:00') as date,
       ROUND((select charge from MeasurementData.SolarPower WHERE max(_cid) =  id)/1000,2) as chg,
       round((((select charge from MeasurementData.SolarPower WHERE max(_cid) =  id) - charge)/1000),2) as chgDiff,
      
       
	   round(min(current)/10,2) as 'cur-min', 
	   round(avg(current)/10,2) as 'cur-avg',
       round(max(current)/10,2) as 'cur-max', 
                                       
	   round(min(power)/1000,2) as 'pow-min',
       round(avg(power)/1000,2) as 'pow-avg', 
       round(max(power)/1000,2) as 'pow-max'
       
	   
      FROM $_sqlTableNameFull
	  GROUP BY date ORDER BY chgDiff $order
	  LIMIT ?,?;
	  ";
	  
// Copy of best hour with DESC for ASC.
// what hour was the most energy consumed?	  
$sql["worstHour"] = "
		SELECT 
		
		DATE_FORMAT(?, '%H:%i:%s') as 'footer-nowTime',
		id as _cid, curtime as mynow,
	
       DATE_FORMAT(curtime, '%d%.%m.%Y %H:00') as date,
       ROUND((select charge from MeasurementData.SolarPower WHERE max(_cid) =  id)/1000,2) as chg,
       round((((select charge from MeasurementData.SolarPower WHERE max(_cid) =  id) - charge)/1000),2) as chgDiff,
      
       
	   round(min(current)/10,2) as 'cur-min', 
	   round(avg(current)/10,2) as 'cur-avg',
       round(max(current)/10,2) as 'cur-max', 
                                       
	   round(min(power)/1000,2) as 'pow-min',
       round(avg(power)/1000,2) as 'pow-avg', 
       round(max(power)/1000,2) as 'pow-max'
       
	   
      FROM $_sqlTableNameFull
	  GROUP BY date ORDER BY chgDiff $order
	  LIMIT ?,?;
	  ";
	  
$sql["current"] = "
	SELECT 
	    ? as unused,
		timediff(now(), curtime) as 'footer-dateAge', 
		DATE_FORMAT(NOW(), '%H:%i:%s') as 'footer-nowTime',
		curtime, 
		Round(voltage/100,2) as vlt, 
		round(current/10,2) as cur, 
		round(power/1000,2) AS pow,
		round(charge/1000,2) as chg, round(work/1000,2) as wrk 
	 FROM $_sqlTableNameFull 
	 ORDER BY curtime $order
	 LIMIT ?,?";
	 	
if (!isset($sql[$opts["show"]])){
	jsonDie("Url Parameter 'show' has invalid value {$opts['show']} ");
}

//jsonDie($sql[$opts["show"]]);

return $sql[$opts["show"]];
}
	 ?>