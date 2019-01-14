<!DOCTYPE html>
<html lang="en" >

<?php

$js[] = "./js/modernizr.min.js";
$js[] = "./js/jquery-3.3.1.min.js";
$js[] = "./js/jquery-ui.js";
$js[] = "./js/jquery.jqplot.min.js";
$js[] = "./js/jplotPlugins/jqplot.barRenderer.js";
$js[] = "./js/jplotPlugins/jqplot.categoryAxisRenderer.js";
$js[] = "./js/jplotPlugins/jqplot.json2.js";
$js[] = "./js/jplotPlugins/jqplot.pointLabels.js";
$js[] = "./js/jplotPlugins/jqplot.highlighter.js";
$js[] = "./js/jplotPlugins/jqplot.dateAxisRenderer.js";
$js[] = "./js/jplotPlugins/jqplot.logAxisRenderer.js";
$js[] = "./js/jplotPlugins/jqplot.canvasTextRenderer.js";
$js[] = "./js/jplotPlugins/jqplot.canvasAxisTickRenderer.js";
$js[] = "./js/jplotPlugins/jqplot.canvasOverlay.min.js";
$js[] = "./js/jplotPlugins/jqplot.canvasAxisLabelRenderer.js";
$js[] = "./js/jplotPlugins/jqplot.canvasAxisTickRenderer.js";
$js[] = "./js/jplotPlugins/jqplot.dateAxisRenderer.js";
$js[] = "./js/jplotPlugins/jqplot.barRenderer.js";
$js[] = "./js/jplotPlugins/jqplot.pointLabels.min.js";
$js[] = "./js/dateFormat.min.js";
$js[] = "./js/moment.js";
$js[] = "./js/jQueryRotate.js";
$js[] = "./js/my.js";
$js[] = "./js/datePicker.js";
$js[] = "./js/graphConfig.js";
$js[] = "./js/liveGraph.js";
$js[] = "./js/barChart.js";
$js[] = "./js/tables.js";

$css[] = "./css/normalize.min.css";
$css[] = "./css/jquery-ui.css";
$css[] = "./css/jquery.jqplot.min.css";
$css[] = "./css/style.css";
$css[] = "./css/myStyles.css";

?>

<head>
  <meta charset="UTF-8">
  <title>Solar Leistungsdaten</title>

	
	<?php // put in all javascript files.
	foreach($js as $url){
		$filetime      = filemtime($url);
		$url = $url .  "?version=" . $filetime;
		?><script type="text/javascript" src="<?php echo$url;?>" ></script><?php echo"\n";
	}
	
	// Put in all styles.
	foreach($css as $url){
		$filetime = filemtime($url);
		$url 	 .=  "?version=".$filetime;
		?><link rel="stylesheet" href="<?php echo$url;?>" /><?php  echo"\n";
	}
	?>
	

</head>

<body>

<span style="font-size:30px;cursor:pointer" id="opener" onclick="openNav()">&#9776; Menü</span>


 <!-- Im invisible at first... -->
<div id="myNav" class="overlay">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
  <div class="overlay-content">
    <a class="scrollTo" ref="liveChart" href="#">		<img src="img/live.png"/>Live</a>
    <a class="scrollTo" ref="weekStatsChart" href="#">	<img src="img/week.png"/>Woche</a>
    <a class="scrollTo" ref="dayStatsChart" href="#">	<img src="img/day.jpg"/>Tag</a>
	<a class="scrollTo" ref="bestDayChart" href="#">		<img src="img/top.png"/>Top-5</a>
    <a class="scrollTo" ref="tables" href="#">				<img src="img/table.png"/>Tabellen</a>
    <a id="refreshAll"  ref="" href="#">				        <img src="img/refresh.png"/>Neu-Laden</a>
	
  </div>
</div>

<br />
<!--<img src="https://preview.ibb.co/j5qUwx/aussenansicht.jpg" width="25%">-->


 <p>
<div id="liveChartHeading" class="heading"> 
	<h1>Live-Daten
		<a href="#javascript" class="hideOrShow" ref="liveChart" show="true">
			<img src="img/hide.png" border="0">
		</a> 
		<div id="liveChartNavi"  class="navi" ref="liveChart">
			<!--<a class="refresh" ref="liveChart" href="#javascript" />
				<img src="img/refresh.png" title="jetzt aktualisieren" border="0">
			</a>-->
		</div>
	</h1>
	<div id="liveChartInfo">
		<div class="text"></div>
		<div class="counter">
			<div style="float:left;">aktuellster Wert&nbsp;</div><div style="float:left;" class="value" seconds="0">00:00</div>
		</div>
	</div>
	
</div>
</p>
<p>
<div id="liveChart" class="chart" ref="current"></div>
</p>


<div class="spacer">&nbsp;</div>


<div id="weekStatsChartHeading" class="heading"> 
	<h1>Ladung - Wochenstatistik (letzte 7 Tage) 
		<a href="#javascript" class="hideOrShow" ref="weekStatsChart" show="true">
			<img src="img/hide.png" border="0">
		</a> <!-- weekStatsChartNavi-->
		<div id="weekStatsChartNavi"  class="navi" ref="weekStatsChart">
			<a class="refresh" ref="weekStatsChart" href="#javascript" />
				<img src="img/refresh.png" title="jetzt aktualisieren" border="0">
			</a>
		</div>
	</h1>
	<div id="weekStatsChartInfo"></div>
</div>
<div id="weekStatsChart" class="chart barchart" ref="weekStats"></div>

<div class="spacer">&nbsp;</div>



<div id="dayStatsChartHeading" class="heading"> <h1>Tages-Statistik (je Tag) <a href="#javascript" class="hideOrShow" ref="dayStatsChart" show="true"><img src="img/hide.png" border="0"></a></h1>
<div id="dayStatsChartInfo"></div> 
</div>
  
<div id="dayStatsChartNavi" class="navi" ref="dayStatsChart">
	<div class="showDate">heute</div>
	<img src="img/calendarLast.png"  class="dateArrow dateBack" addDays="-1">
	<input type="hidden" id="dayStatsChartDatePicker" class="datePicker"/>
	<img src="img/calendarLast.png"  class="dateArrow dateForth" addDays="1">
	<a class="refresh" ref="dayStatsChart" href="#javascript"><img src="img/refresh.png" title="jetzt aktualisieren" border="0"></a>
  </div>
  
  <div id="dayStatsChart" class="chart barchart" ref="dayStats" ></div>
  
  
  
  <div class="spacer">&nbsp;</div>

  
   <div id="bestDayChartHeading" class="heading"> <h1>Tage mit der stärksten Ladung <a href="#javascript" class="hideOrShow" ref="bestDayChart" show="true"><img src="img/hide.png" border="0"></a></h1>
   	<div id="bestDayChartInfo"></div></div>
  <div id="bestDayChartNavi"  class="navi"  ref="bestDayChart">
			<a class="refresh" ref="bestDayChart" href="#javascript" />
				<img src="img/refresh.png" title="jetzt aktualisieren" border="0">
			</a>
			<a class="changeOrder" ref="bestDayChart" href="#javascript" />
				<img src="img/like.png" title="zur Flop5 wechseln." border="0">
			</a>
		</div>
  
  <div id="bestDayChart" class="chart barchart" ref="bestDay"></div>
  
<div class="spacer">&nbsp;</div>


  

  <div id="current" class="liveDataINACTIVE"></div>
  

  <div id="tablesHeading">&nbsp;</div>
  
  
  <br />
   <div  class="heading"> <h1>Wochenstatistik (je Tag) <a href="#javascript" class="hideOrShow" ref="weekStats" show="true"><img src="img/hide.png" border="0"></a></h1></div>
  <div id="weekStatsNavi"  class="navi" ref="weekStatsDay"></div>
  
  <div id="weekStats" class="liveData"></div>
  
  


  <br />
 <div  class="heading"> <h1>Tages-Statistik (je Stunde ) <a href="#javascript" class="hideOrShow" ref="dayStats" show="true"><img src="img/hide.png" border="0"></a></h1></div>
  
  <div id="dayStatsNavi" class="navi" ref="dayStats">
	<div class="showDate">heute</div>
	<img src="img/calendarLast.png"  class="dateArrow dateBack" addDays="-1">
	<input type="hidden" id="dayStatsDatePicker" class="datePicker"/>
	<img src="img/calendarLast.png"  class="dateArrow dateForth" addDays="1">
  </div>
  <!-- DataTable is generated into this div -->
  <div class="tablecontainer">
  <div id="dayStats" class="liveData"></div>
  </div>
 <br />
<br >
 
   <div  class="heading"> <h1>Stärkste Ladung<a href="#javascript" class="hideOrShow" ref="bestDay" show="true"><img src="img/hide.png" border="0"></a></h1></div>
   <br />
  <div id="info2" class=""></div><br />

  <div id="bestDayNavi" class="navi" ref="bestDay"></div>
  <div class="tablecontainer">
  <div id="bestDay" class="liveData"></div>
  </div>
  
<br >
  <br />
  
   <div  class="heading"> <h1>Stärkste Entladung <a href="#javascript" class="hideOrShow" ref="worstDay" show="true"><img src="img/hide.png" border="0"></a></h1></div>
     <br />
  <div id="worstDayNavi" class="navi" ref="worstDay"></div>
  <div class="tablecontainer">
  <div id="worstDay" class="liveData"></div>
  </div>

  
  <br >
  <br />
   <div  class="heading"> 
  <h1>Beste Stunden Top10 <a href="#javascript" class="hideOrShow" ref="bestHour" show="true"><img src="img/hide.png" border="0"></a></h1>
  </div>
 
  <div id="bestHourNavi" class="navi" ref="bestHour"></div>
  <div class="tablecontainer">
  <div id="bestHour" numRows="10" class="liveData"></div>
  </div>
  
  
  
  
  
  
  
  
  
  
  
 
  
  <div id="dayStatsTemplate" class="template">
	<table class="rwd-table">
	  <tr class="head">
		<th>Stunde</th>
		<th>Strom (Min; Ø; Max)</th>
		<th>Leistung (Min; Ø; Max)</th>
		<th>Gesamt-Ladung</th>
		<th>Ladung in dieser Stunde</th>
	  </tr>

	  <tr class="row">
		<td class="date"     data-th="Stunde">				  <div class="data"></div></td>
		<td class="cur"     data-th="Strom (Min; Ø; Max)">	  <div class="min"></div> <div class="unit">A&nbsp; </div>
															  <div class="max"></div> <div class="unit">A&nbsp; </div>
															  <div class="avg"></div> <div class="unit">A&nbsp; </div>
															  
															  </td>
															  
		<td class="pow"      data-th="Leistung (Min; Ø; Max)"> 	<div class="min"></div> <div class="unit">W&nbsp; </div>
																<div class="avg"></div> <div class="unit">W&nbsp; </div>
																<div class="max"></div> <div class="unit">W&nbsp; </div>
																</td>
		<td class="chg" 	 data-th="Gesamt-Ladung"> 			<div class="data"></div><div class="unit">A/h</div></td>	
		<td class="chgDiff" data-th="Ladung in dieser Stunde">	<div class="data"></div><div class="unit">A/h</div></td>
	  </tr>
	 
	<tr class="foot"> 
		<th colspan="5"><div>Stand von:&nbsp;</div><div class="nowTime"></div><div>&nbsp;vor&nbsp;</div> <div class="dateAge"></div><div>&nbsp;<a class="refresh" ref="dayStats" href="#javascript"><img src="img/refresh.png" width="20px" title="jetzt aktualisieren" border="0"></a></div></th>
  </tr>
  </table>
 </div>
 
 
 
 
 
 <div id="weekStatsTemplate" class="template">
 <small>Hinweis: Negative Zahlen = Zugeführte Energie/Strom;</small>

<table class="rwd-table">
  <tr class="head">
	<th>Wochentag</th>
    <th>Tag</th>
    <th>Strom (Min; Ø; Max)</th>
    <th>Leistung (Min; Ø; Max)</th>
    <th>Gesamt-Ladung</th>
	<th>Ladung an diesem Tag</th>
  </tr>
  

  <tr class="row">
	<td class="weekday" data-th="Wochentag">		       
		<div class="data"></div>		
	</td>
	
	<td class="date" data-th="Tag">						
		<div class="data"></div>
	</td>
	
	<td class="cur"     data-th="Strom (Min; Ø; Max)">	  	
		<div class="min"></div> <div class="unit">A&nbsp; </div>
		<div class="avg"></div> <div class="unit">A&nbsp; </div>
		<div class="max"></div> <div class="unit">A&nbsp; </div>
	</td>
	
	<td class="pow"      data-th="Leistung (Min; Ø; Max)"> 		
		<div class="min"></div> <div class="unit">W&nbsp; </div>
		<div class="avg"></div> <div class="unit">W&nbsp; </div>
		<div class="max"></div> <div class="unit">W&nbsp; </div>
	</td>
	
	<td class="chg"  data-th="Gesamt-Ladung">
		<div class="data"></div>
		<div class="unit">A/h</div>
	</td>
	
	<td class="chgDiff" data-th="Ladung an diesem Tag">	
		<div class="data"></div>
		<div class="unit">A/h</div>
	</td>
	
  </tr>
 
 
    <tr class="foot"> 
	<th colspan="6"><div>Stand von:&nbsp;</div><div class="nowTime"></div><div>&nbsp;vor&nbsp;</div> <div class="dateAge"></div>&nbsp;<a class="refresh" ref="weekStats" href="#javascript"><img src="img/refresh.png" width="20px" title="jetzt aktualisieren" border="0"></a></div></th>
  </tr>

</table>
 </div>
 
 
 
 <div id="bestDayTemplate" class="template">
 <small>Hinweis: Negative Zahlen = Zugeführte Energie/Strom;</small>

<table class="rwd-table">
  <tr class="head">
	<!--<th>Wochentag</th>-->
    <th>Tag</th>
    <th>Strom (Min; Ø; Max)</th>
    <th>Leistung (Min; Ø; Max)</th>
    <th>Gesamt-Ladung</th>
	<th>Ladung an diesem Tag</th>
  </tr>
  

  <tr class="row">
<!--	<td class="weekday" data-th="Wochentag">		       
		<div class="data"></div>		
	</td>
	-->
	
	<td class="date" data-th="Tag">						
		<div class="data"></div>
	</td>
	
	<td class="cur"     data-th="Strom (Min; Ø; Max)">	  	
		<div class="min"></div> <div class="unit">A&nbsp; </div>
		<div class="avg"></div> <div class="unit">A&nbsp; </div>
		<div class="max"></div> <div class="unit">A&nbsp; </div>
	</td>
	
	<td class="pow"      data-th="Leistung (Min; Ø; Max)"> 		
		<div class="min"></div> <div class="unit">W&nbsp; </div>
		<div class="avg"></div> <div class="unit">W&nbsp; </div>
		<div class="max"></div> <div class="unit">W&nbsp; </div>
	</td>
	
	<td class="chg"  data-th="Gesamt-Ladung">
		<div class="data"></div>
		<div class="unit">A/h</div>
	</td>
	
	<td class="chgDiff" data-th="Ladung an diesem Tag">	
		<div class="data"></div>
		<div class="unit">A/h</div>
	</td>
	
  </tr>
 
 
  <tr class="foot"> 
	<th colspan="5"><div>Stand von:&nbsp;</div><div class="nowTime"></div><div>&nbsp;vor&nbsp;</div> <div class="dateAge"></div>&nbsp;<a class="refresh" ref="bestDay" href="#javascript"><img src="img/refresh.png" width="20px" title="jetzt aktualisieren" border="0"></a></div></th>
  </tr>

</table>
 </div>
 
 
 
 
 
 
 <div id="worstDayTemplate" class="template">
 <small>Hinweis: Negative Zahlen = Zugeführte Energie/Strom;</small>

<table class="rwd-table">
  <tr class="head">
	<!--<th>Wochentag</th>-->
    <th>Tag</th>
    <th>Strom (Min; Ø; Max)</th>
    <th>Leistung (Min; Ø; Max)</th>
    <th>Gesamt-Ladung</th>
	<th>Ladung an diesem Tag</th>
  </tr>
  

  <tr class="row">
<!--	<td class="weekday" data-th="Wochentag">		       
		<div class="data"></div>		
	</td>
	-->
	
	<td class="date" data-th="Tag">						
		<div class="data"></div>
	</td>
	
	<td class="cur"     data-th="Strom (Min; Ø; Max)">	  	
		<div class="min"></div> <div class="unit">A&nbsp; </div>
		<div class="avg"></div> <div class="unit">A&nbsp; </div>
		<div class="max"></div> <div class="unit">A&nbsp; </div>
	</td>
	
	<td class="pow"      data-th="Leistung (Min; Ø; Max)"> 		
		<div class="min"></div> <div class="unit">W&nbsp; </div>
		<div class="avg"></div> <div class="unit">W&nbsp; </div>
		<div class="max"></div> <div class="unit">W&nbsp; </div>
	</td>
	
	<td class="chg"  data-th="Gesamt-Ladung">
		<div class="data"></div>
		<div class="unit">A/h</div>
	</td>
	
	<td class="chgDiff" data-th="Ladung an diesem Tag">	
		<div class="data"></div>
		<div class="unit">A/h</div>
	</td>
	
  </tr>
 
 
  <tr class="foot"> 
	<th colspan="5"><div>Stand von:&nbsp;</div><div class="nowTime"></div><div>&nbsp;vor&nbsp;</div> <div class="dateAge"></div>&nbsp;<a class="refresh" ref="worstDay" href="#javascript"><img src="img/refresh.png" width="20px" title="jetzt aktualisieren" border="0"></a></div></th>
  </tr>

</table>
 </div>
 
 
 
 
 
 
 
 
 
 
 
 
 
 
  <div id="bestHourTemplate" class="template">
 <small>Hinweis: Negative Zahlen = Zugeführte Energie/Strom;</small>

<table class="rwd-table">
  <tr class="head">
	<!--<th>Wochentag</th>-->
    <th>Stunde</th>
    <th>Strom (Min; Ø; Max)</th>
    <th>Leistung (Min; Ø; Max)</th>
    <th>Gesamt-Ladung</th>
	<th>Ladung in dieser Stunde</th>
  </tr>
  

  <tr class="row">
<!--	<td class="weekday" data-th="Wochentag">		       
		<div class="data"></div>		
	</td>
	-->
	
	<td class="date" data-th="Tag">						
		<div class="data"></div>
	</td>
	
	<td class="cur"     data-th="Strom (Min; Ø; Max)">	  	
		<div class="min"></div> <div class="unit">A&nbsp; </div>
		<div class="avg"></div> <div class="unit">A&nbsp; </div>
		<div class="max"></div> <div class="unit">A&nbsp; </div>
	</td>
	
	<td class="pow"      data-th="Leistung (Min; Ø; Max)"> 		
		<div class="min"></div> <div class="unit">W&nbsp; </div>
		<div class="avg"></div> <div class="unit">W&nbsp; </div>
		<div class="max"></div> <div class="unit">W&nbsp; </div>
	</td>
	
	<td class="chg"  data-th="Gesamt-Ladung">
		<div class="data"></div>
		<div class="unit">A/h</div>
	</td>
	
	<td class="chgDiff" data-th="Ladung an diesem Tag">	
		<div class="data"></div>
		<div class="unit">A/h</div>
	</td>
	
  </tr>
 
 
   <tr class="foot"> 
	<th colspan="5"><div>Stand von:&nbsp;</div><div class="nowTime"></div><div>&nbsp;vor&nbsp;</div> <div class="dateAge"></div>&nbsp;<a class="refresh" ref="bestHour" href="#javascript"><img src="img/refresh.png" width="20px" title="jetzt aktualisieren" border="0"></a></div></th>
  </tr>

</table>
 </div>
 
 
 
 
 
 
 
  <div id="currentTemplate" class="template">
 

<table class="rwd-table">
  <tr class="head">
	<th>Zeit</th>
    <th>Spannung</th>
    <th>Strom</th>
    <th>Leistung</th>
	<th>Ladung</th>
	<th>Arbeit</th>
  </tr>
  

  <tr class="row">
	
	<td data-th="Zeit" class="curtime">		
		<div class="data"></div>
	</td>
    <td data-th="Spannung" 	class ="vlt">
		<div class="data"></div>
		<div class="unit">V</div>	
	</td>
    <td data-th="Leistung"	class="cur">
		<div class="data"></div>
		<div class="unit">A</div>	
	</td>
    <td data-th="Ladung"	class="pow">
		<div class="data"></div>
		<div class="unit">W</div>	
	</td>
	<td data-th="Arbeit"	class="chg">  
		<div class="data"></div>
		<div class="unit">A/h</div>	
	</td>
	<td data-th="Arbeit"	class="wrk">   
		<div class="data"></div>
		<div class="unit">W/h</div>	
	</td>

	
  </tr>
 
 
  <tr class="foot"> 
	<th colspan="6"><div>Stand von:&nbsp;</div><div class="nowTime"></div><div>&nbsp;vor&nbsp;</div> <div class="dateAge"></div>&nbsp;<a class="refresh" ref="current" href="#javascript"><img src="img/refresh.png" width="20px" title="jetzt aktualisieren" border="0"></a></div></th>
  </tr>

</table>
 </div>
 
 
 

 
 
 
 

 
 


<!--  <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>-->

  

    <script  src="js/index.js"></script>




</body>

</html>
