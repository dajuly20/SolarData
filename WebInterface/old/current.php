<?php

require "sqlConn.php";
 

 
$id = 100;
$sql = "SELECT timediff(now(), curtime) as dateAge, curtime, Round(voltage/100,2) as vlt, round(current/10,2) as cur, round(power/1000,2) AS pow, round(charge/1000,2) as chg, round(work/1000,2) as wrk 
		FROM $_sqlTableNameFull order by curtime desc LIMIT 0,1";
$statement = $mysqli->prepare($sql);
//$statement->bind_param('i', $id);
$statement->execute();
$result = $statement->get_result();
$row = $result->fetch_assoc();
?>
<small></small>
<table class="rwd-table">
  <tr>
    <th>Zeit</th>
    <th>Spannung</th>
    <th>Strom</th>
    <th>Leistung</th>
	<th>Ladung</th>
	<th>Arbeit</th>
  </tr>
  <tr>
  
    <td data-th="Zeit">			<?php echo $row["curtime"]; ?>		</td>
    <td data-th="Spannung">		<?php echo $row["vlt"];     ?>V		</td>
    <td data-th="Leistung">		<?php echo $row["cur"];     ?>A		</td>
    <td data-th="Ladung">	<b>	<?php echo $row["pow"];  	?>W</b>	</td>
	<td data-th="Arbeit">		<?php echo $row["chg"];     ?>A/h	</td>
	<td data-th="Arbeit">		<?php echo $row["wrk"];     ?>W/h	</td>
	
  </tr>
<tr> 
<th colspan="6"><small>Stand von: <?php echo date("H:i:s");?>  vor <?php echo $row["dateAge"];     ?> </small></th>
</tr>
</table>

