<!DOCTYPE html>
<html lang="en" >

<head>
  <meta charset="UTF-8">
  <title>Solar Leistungsdaten</title>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script type="text/javascript" src="js/jquery.jqplot.min.js"></script>
	<script type="text/javascript" src="js/jplotPlugins/jqplot.barRenderer.js"></script>
	<script type="text/javascript" src="js/jplotPlugins/jqplot.categoryAxisRenderer.js"></script>
	<script type="text/javascript" src="js/jplotPlugins/jqplot.json2.js"></script>
	<script type="text/javascript" src="js/jplotPlugins/jqplot.pointLabels.js"></script>
	<script type="text/javascript" src="js/jplotPlugins/jqplot.highlighter.js"></script>
	<script type="text/javascript" src="js/jplotPlugins/jqplot.dateAxisRenderer.js"></script>
	<script type="text/javascript" src="js/jplotPlugins/jqplot.logAxisRenderer.js"></script>
	<script type="text/javascript" src="js/jplotPlugins/jqplot.canvasTextRenderer.js"></script>
	<script type="text/javascript" src="js/jplotPlugins/jqplot.canvasAxisTickRenderer.js"></script>
	<script type="text/javascript" src="js/jplotPlugins/jqplot.canvasOverlay.min.js"></script>
	<script type="text/javascript" src="js/jplotPlugins/jqplot.canvasAxisLabelRenderer.js"></script>
	<script type="text/javascript" src="js/jplotPlugins/jqplot.canvasAxisTickRenderer.js"></script>
	<script type="text/javascript" src="js/jplotPlugins/jqplot.dateAxisRenderer.js"></script>
	<script type="text/javascript" src="js/jplotPlugins/jqplot.barRenderer.js"></script>
	<script type="text/javascript" src="js/jplotPlugins/jqplot.pointLabels.min.js"></script>
	<script type="text/javascript" src="js/dateFormat.min.js"></script>
	<script type="text/javascript" src="js/jQueryRotate.js"></script>
	<script  src="js/my.js"></script>
	<script  src="js/graphConfig.js"></script>
	

 <style>

    .foot div,
    .row div { 
      float: left;
    }
	
	div.template{
		visibility: hidden;
	}

	.ui-datepicker-trigger,
	.dateArrow	{ 
	 height:70px;
	 }

	 
	 .dateForth{
		 transform: scaleX(-1);
	}

	.ui-datepicker-trigger,
	.dateArrow{
				cursor:pointer
	}
	
	a:link {
    text-decoration: none;
	}

	a:visited {
		text-decoration: none;
	}
	
	.center,
	div.chart,
	div.liveData,
	div.navi,
	div.heading
	{
    margin: auto;
    width: 50%;
    
    padding: 10px;
   }

    </style>
  <script type="text/javascript">
  var plot2;
  
$(document).ready(function(){

// var ajaxDataRenderer = function(url, plot, options) {
// var ret = null;
// }
		
	// A Bar chart from a single series will have all the bar colors the same.
   // var ticks = ['Donnerstag 01.01.0000','Donnerstag 01.01.0000','Donnerstag 01.01.0000'];
	//var line1 = [-10,-8,-6];

	
	// Cick handler for BestDay Bar-Chart. 
	// Refreshes *dayStats* and scrolls up there.
	 $('#bestDayChart').bind('jqplotDataClick', 
		function (ev, seriesIndex, pointIndex, data) {
			var toElement = "dayStats";
			var date = $(this).data("dates")[pointIndex];

			// Change the Datepicker itself (so back arrow will work)
			$("#" + toElement + "DatePicker").val(date);
			// Then fire the function (like the arrow would do) with the new date.
			datePickerChangedDate(date);
			// And Finaly scroll to the element.
			scrollToId(toElement);
	
	});


	// Initialisation for BarChart.
	 updateBarChart();
	 updateLiveChart();

	/* store empty array or array of original data to plot on page load */
	var storedData = 	[0];
	var storedVoltage = [0];
	var storedCur = 	[0];
	var storedChg = 	[0];
	var storedAll = [storedData,storedVoltage,storedCur];
	var plot1;
	//renderLiveGraph("liveChart");
	
	
	
	function renderLiveGraph(what) {
		if(what == undefined){
			alert("renderGraph, what undefined");
		}
		var element = $("#"+what);
		
		if (element.data("plot")) {
			element.data("plot").destroy();
		}
		plot1 = $.jqplot(what, storedAll, liveGraphConf);
		element.data("plot",plot1);
	}
	
	
	// Updates the live chart, then calls itself after x ms 
	function updateLiveChart() {
		var onlyOnChange = true;
		var refreshAfter = 500; // refresh in x ms
        var what = "current";
		var date = "now";
		var tNum = "pow,vlt,cur"
		console.log("Here i am ");
		$.getJSON( "query.php?show="+what+"&date="+date+"&tNum="+tNum, function( data ) {
			if(typeof data.err !== 'undefined' && data.err[1] != ""){
				alert(data.err[1] + "\n" + data.err[2]);
			}else{
				var newVal  = data.res[0][0]; /* update storedData array*/
				var newVolt = data.res[1][0]; // <-- TODO (wieso verschachteltes Array?)
				var newCur  = data.res[2][0];
			    
				oldVal = storedAll[0];
			    oldVolt = storedAll[1];
				oldCur = storedAll[2];
				
				// If any of those vals change...
				eq  = (oldVal [oldVal.length -1]   == data.res[0])
				eq &= (oldVolt[oldVolt.length-1]   == data.res[1])
				eq &= (oldCur [oldCur.length -1]   == data.res[2])
		
				// We push a new val-set on stack.
				// TODO RENAME Var
				if(!eq){
					storedAll[0].push(newVal); 
					storedAll[1].push(newVolt);
					storedAll[2].push(newCur);
					renderLiveGraph("liveChart");
				}
				setTimeout(updateLiveChart, refreshAfter)	
			}
		});
	}

	// Each element that has the class liveData also has a Id, which then will be loaded.
	$(".liveData").each(function(index, value){
		var id = $(this).attr("id");
		getJson2DIV(id);
	});
   
	var angle = 0;
	var interval;

	function datePickerChangedDate(dateText) {
				   var germanDate = DateFormat.format.date(dateText, "dd.MM.yyyy");
				   $("#dayStatsNavi .showDate").html(germanDate);
					//alert("Selected date: " + dateText + "; input's current value: " + this.value);
					getJson2DIV("dayStats",dateText);
	}
    		
	// Let the live table refresh every 5 seconds.			
	setInterval(function() {
		getJson2DIV("current");
	}, 5000); 
	  
	var degree = 0, timer;
	var angle = 0;
	var interval;
	$(".liveData").on("click", "a.refresh",  function(event){
		// GO upwards DOM and find the closest parent with class live data.
		var id 	= $(this).closest(".liveData").attr("id");
		var img = $(this).find("img");
		
		// Animate symbol
		interval = setInterval(function(){
		angle+=7;
		img.rotate(angle);
		},50)
		 
		getJson2DIV(id);
		 
		return false;
	});
		
			
			
			
			 
	$("#dayStatsDatePicker").datepicker({
		buttonImage: '/img/calendar.png',
		dayNamesMin: 		[ "So", "Mo", "Di", "Mi", "Do", "Fr", "Sa" ],
		monthNames: 		[ "Januar", "Februar", "Marts", "April", "Maj", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember" ],
		minDate: 			new Date(2018, 4 - 1, 1),
		maxDate: 			0,
		firstDay: 			1,
		buttonImageOnly: 	true,
		changeMonth: 		true,
		changeYear: 		true,
		showOn: 			'both',
		dateFormat:			'yy-mm-dd 12:00:00',
		onSelect: 			datePickerChangedDate,
	});

			
	$(".navi .dateArrow").click(function(){
		var clickedImg = $(this);
		var naviDiv = clickedImg.closest(".navi");
		var ref = naviDiv.attr("ref");
		var datePicker = $( "#" + ref + "DatePicker" );
		var addDays = clickedImg.attr("addDays");
		var date = datePicker.datepicker("getDate");
		if(null == date){ date = new Date(); }
		
		var _oneDay = (1000*60*60*24);
		date.setTime(date.getTime() + (addDays * _oneDay));
		datePicker.datepicker( "setDate", date );
		var dateString = datePicker.val()
		datePickerChangedDate(dateString);
		//alert("ref ist: "+ref+" AddDays: "+addDays);
	});
			
	// Bind Click handler to datepicker symbol aka input.	
	$( ".datepicker" ).datepicker();
			
	$("a.hideOrShow").click(function(){
		var lnk 	= $(this);
		var refDiv 	= $("#"+lnk.attr("ref"));
		var refNavi = $("#"+lnk.attr("ref")+"Navi");
		var show 	= lnk.attr("show");
		
		// If show is true, then it WAS showed. ON-Click its hiding
		if(show == "true"){
			lnk.find("img").attr("src","img/show.png");
			lnk.attr("show","false");
			refDiv.fadeOut();
			refNavi.fadeOut();
		}
		else{
			lnk.find("img").attr("src","img/hide.png");
			lnk.attr("show","true");
			refDiv.fadeIn();
			refNavi.fadeIn();
		}

		return false;
	});//clickhandler
			
});//ready

</script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="css/jquery.jqplot.min.css" />
    <link rel="stylesheet" href="css/style.css">

  
</head>

<body>


<br />
<img src="https://preview.ibb.co/j5qUwx/aussenansicht.jpg" width="25%">


  
  
 <div class="heading"> <h1>Live-Daten</h1></div>
  <div id="liveChart" class="chart" style="height:300px;width:900px"></div>
  <div id="current" class="liveData"></div>
  

  
  <br />
   <div  class="heading"> <h1>Wochenstatistik (je Tag) <a href="#javascript" class="hideOrShow" ref="weekStats" show="true"><img src="img/hide.png" border="0"></a></h1></div>
  <div id="weekStatsNavi" class="navi" ref="weekStatsDay"></div>
  <div id="weekStats" class="liveData"></div>
  


  <br />
 <div  class="heading"> <h1>Tages-Statistik (je Stunde ) <a href="#javascript" class="hideOrShow" ref="dayStats" show="true"><img src="img/hide.png" border="0"></a></h1></div>
  <div id="dayStatsNavi" class="navi" ref="dayStats">
	<div class="showDate">heute</div>
	<img src="img/calendarLast.png"  class="dateArrow dateBack" addDays="-1">
	<input type="hidden" id="dayStatsDatePicker"/>
	<img src="img/calendarLast.png"  class="dateArrow dateForth" addDays="1">
  </div>
  <!-- DataTable is generated into this div -->
  <div id="dayStats" class="liveData"></div>
  
 <br />

 
   <div  class="heading"> <h1>Stärkste Ladung Top3 <a href="#javascript" class="hideOrShow" ref="bestDay" show="true"><img src="img/hide.png" border="0"></a></h1></div>
  <div id="info2" class="heading"></div><br />
  <div id="bestDayChart" class="chart" style="height:300px;width:500px"></div>

  <div id="bestDayNavi" class="navi" ref="bestDay"></div>
  <div id="bestDay" class="liveData"></div>
  

  
   <div  class="heading"> <h1>Stärkste Entladung <a href="#javascript" class="hideOrShow" ref="worstDay" show="true"><img src="img/hide.png" border="0"></a></h1></div>
  <div id="worstDayNavi" class="navi" ref="worstDay"></div>
  <div id="worstDay" class="liveData"></div>

  
  
  <br />
   <div  class="heading"> 
  <h1>Beste Stunden Top10 <a href="#javascript" class="hideOrShow" ref="bestHour" show="true"><img src="img/hide.png" border="0"></a></h1>
  </div>
  <div id="bestHourNavi" class="navi" ref="bestHour"></div>
  <div id="bestHour" numRows="10" class="liveData"></div>
  
  
  
  
  
  
  
  
  
  
  
  
 
  
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
		<th colspan="6"><div>Stand von:&nbsp;</div><div class="nowTime"></div><div>&nbsp;vor&nbsp;</div> <div class="dateAge"></div><div>&nbsp;<a class="refresh" href="#javascript"><img src="img/refresh.png" width="20px" title="jetzt aktualisieren" border="0"></a></div></th>
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
	<th colspan="6"><div>Stand von:&nbsp;</div><div class="nowTime"></div><div>&nbsp;vor&nbsp;</div> <div class="dateAge"></div>&nbsp;<a class="refresh" href="#javascript"><img src="img/refresh.png" width="20px" title="jetzt aktualisieren" border="0"></a></div></th>
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
	<th colspan="6"><div>Stand von:&nbsp;</div><div class="nowTime"></div><div>&nbsp;vor&nbsp;</div> <div class="dateAge"></div>&nbsp;<a class="refresh" href="#javascript"><img src="img/refresh.png" width="20px" title="jetzt aktualisieren" border="0"></a></div></th>
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
	<th colspan="6"><div>Stand von:&nbsp;</div><div class="nowTime"></div><div>&nbsp;vor&nbsp;</div> <div class="dateAge"></div>&nbsp;<a class="refresh" href="#javascript"><img src="img/refresh.png" width="20px" title="jetzt aktualisieren" border="0"></a></div></th>
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
	<th colspan="5"><div>Stand von:&nbsp;</div><div class="nowTime"></div><div>&nbsp;vor&nbsp;</div> <div class="dateAge"></div>&nbsp;<a class="refresh" href="#javascript"><img src="img/refresh.png" width="20px" title="jetzt aktualisieren" border="0"></a></div></th>
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
    <td data-th="Leistung"	class="cur";>
		<div class="data"></div>
		<div class="unit">A</div>	
	</td>
    <td data-th="Ladung"	class="pow";>
		<div class="data"></div>
		<div class="unit">W</div>	
	</td>
	<td data-th="Arbeit"	class="chg";>  
		<div class="data"></div>
		<div class="unit">A/h</div>	
	</td>
	<td data-th="Arbeit"	class="wrk";>   
		<div class="data"></div>
		<div class="unit">W/h</div>	
	</td>

	
  </tr>
 
 
  <tr class="foot"> 
	<th colspan="6"><div>Stand von:&nbsp;</div><div class="nowTime"></div><div>&nbsp;vor&nbsp;</div> <div class="dateAge"></div>&nbsp;<a class="refresh" href="#javascript"><img src="img/refresh.png" width="20px" title="jetzt aktualisieren" border="0"></a></div></th>
  </tr>

</table>
 </div>
 
 
 

 
 


<!--  <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>-->

  

    <script  src="js/index.js"></script>




</body>

</html>
