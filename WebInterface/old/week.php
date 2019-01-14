<?php

require "sqlConn.php";

$id = 100;
 



$sql = "SELECT id, 
	DATE_FORMAT(curtime, '%d%.%m.%Y') as date,
	curtime as mynow,
	
        
       
       round(min(current)/10,2) as 'min-cur', 
	   round(avg(current)/10,2) as 'avg-cur',
       round(max(current)/10,2) as 'max-cur', 
       
       round(min(power)/1000,2) as 'min-pow',
       round(avg(power)/1000,2) as 'avg-pow', 
       round(max(power)/1000,2) as 'max-pow',
       
       round(min(charge)/1000,2) as 'charge',
	min(charge), max(charge),
       round((min(charge) - max(charge))/1000,2) as chargedThisDay
       
      FROM $_sqlTableNameFull
	 WHERE WEEK(curtime) = WEEK(now())
	 GROUP BY DAY(curtime) ORDER BY mynow desc

";

$sql = "

SELECT id as _cid, 
	DATE_FORMAT(curtime, '%W') as weekday,
	DATE_FORMAT(curtime, '%d%.%m.%Y') as date,
	curtime as mynow,
	
		ROUND((select charge from MeasurementData.SolarPower WHERE max(_cid) =  id)/1000,2) as charge,
		round((((select charge from MeasurementData.SolarPower WHERE max(_cid) =  id) - charge)/1000),2) as chgDiff,
      
        
       
       round(min(current)/10,2) as 'min-cur', 
	   round(avg(current)/10,2) as 'avg-cur',
       round(max(current)/10,2) as 'max-cur', 
       
       round(min(power)/1000,2) as 'min-pow',
       round(avg(power)/1000,2) as 'avg-pow', 
       round(max(power)/1000,2) as 'max-pow',
       
       #round(min(charge)/1000,2) as 'charge',
	   min(charge), max(charge)
       #round((min(charge) - max(charge))/1000,2) as chargedThisDay
       
      FROM $_sqlTableNameFull
	 #WHERE WEEK(curtime) = WEEK(now())
	 WHERE curtime >= DATE_SUB(now(), INTERVAL 6 DAY)
	 GROUP BY date ORDER BY mynow desc;

";
$statement = $mysqli->prepare($sql);

if ( false===$statement ) {
  // and since all the following operations need a valid/ready statement object
  // it doesn't make sense to go on
  // you might want to use a more sophisticated mechanism than die()
  // but's it's only an example
  die('prepare() failed: ' . htmlspecialchars($mysqli->error));
}


//$rc = $statement->bind_param('i', $id);
// bind_param() can fail because the number of parameter doesn't match the placeholders in the statement
// or there's a type conflict(?), or ....
// if ( false===$rc ) {
  // // again execute() is useless if you can't bind the parameters. Bail out somehow.
  // die('bind_param() failed: ' . htmlspecialchars($stmt->error));
// }

//Sets Locale to german. 
$mysqli->query("SET lc_time_names = 'de_DE';");

if($statement->execute()){
	
$result = $statement->get_result();

?>

<small>Hinweis: Negative Zahlen = Zugeführte Energie/Strom;</small>
<table class="rwd-table">
  <tr>
	<th>Wochentag</th>
    <th>Tag</th>
    <th>Strom (Min; Ø; Max)</th>
    <th>Leistung (Min; Ø; Max)</th>
    <th>Gesamt-Ladung</th>
	<th>Ladung an diesem Tag</th>
	
  </tr>

  <?php 
  $chgLast = 0;
  while($row = $result->fetch_assoc())
	  {
	  ?>
  <tr>
	<td data-th="Wochentag">				<?php echo $row["weekday"];  	?></td>
	<td data-th="Tag">						<?php echo $row["date"];  	?></td>
    <td data-th="Strom (Min; Ø; Max)">		<?php echo $row["min-cur"]; ?> A; <?php echo $row["avg-cur"]; ?> A; <?php echo $row["max-cur"]; ?></td>		</td>
    <td data-th="Leistung (Min; Ø; Max)">	<?php echo $row["min-pow"]; ?> W; <?php echo $row["avg-pow"]; ?> W; <?php echo $row["max-pow"]; ?>		</td>
    <td data-th="Gesamt-Ladung">			<?php echo $row["charge"];  ?>A/h</b>	</td>
	<td data-th="Ladung an diesem Tag">		<?php   /*if($chgLast != 0){ echo round($chgLast-$row["charge"],2); }else{ echo " --- ";} */ echo $row["chgDiff"];     ?>A/h	</td>
	<!--<td data-th="Ladung in dieser Stunde">	<?php echo $row["chargedThisDay"];     ?>A/h	</td>-->
  </tr>
  <?php
 $chgLast = $row["charge"];
  } ?>
<tr> 
<th colspan="6"><small>Stand: <?php echo date("H:i:s");?></small></th>
</tr>
</table>
<?php
}
else{
	die('execute() failed: ' . htmlspecialchars($stmt->error))."<br>";
	echo $mysqli->error;
}
?>

