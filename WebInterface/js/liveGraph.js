	 
$(document).ready(function(){
	 $('#liveChart').bind('jqplotDataMouseOver', 
            function (ev, seriesIndex, pointIndex, data) {
			   var values = $(this).data("values");
			   var pow  = values[0][pointIndex];
			   var vlt  = values[1][pointIndex];
			   var cur  = values[2][pointIndex];
			   var time = values[3][pointIndex];
			   
			   $('#liveChartInfo div.text').html("Wert von: "+time+" <b>"+pow+"W "+cur+"A "+vlt+"V </b>");
			   $('#liveChartInfo div.counter').css("visibility","hidden");
			   
            }
        );
             
        $('#liveChart').bind('jqplotDataUnhighlight', 
            function (ev) {
			   var values = $(this).data("values");
			   var lastVal = values[0].length -1;
			   var pow  = values[0][lastVal];
			   var vlt  = values[1][lastVal];
			   var cur  = values[2][lastVal];
			   var time = values[3][lastVal];
			   $('#liveChartInfo div.text').html("Wert von: "+time+" <b>"+pow+"W "+cur+"A "+vlt+"V </b>");
			   $('#liveChartInfo div.counter').css("visibility","visible");
            }
        );
});

// For the display of seconds that passed since last value
moment.updateLocale('en', {
	relativeTime : {
		future: "in %s",
		past:   "seit %s",
		s  : '%d Sekunden',
		ss : '%d Sekunden',
		m:  "einer Minute",
		mm: "%d Minuten",
		h:  "einer Stunde",
		hh: "%d Stunden",
		d:  "einem Tag",
		dd: "%d Tagen",
		M:  "einem Monat",
		MM: "%d Monaten",
		y:  "einem Jahr",
		yy: "%d Jahren"
	}
});

function updateLiveChartInfo(element, date){
		 var mrv = mostRecentValues(element);
		 $('#liveChartInfo div.text').html('Wert von: '+mrv[3]+' <b>'+mrv[0].round(2)+'W '+mrv[2]+'A '+mrv[1]+'V</b>');
		 $('#liveChartInfo div.counter div.value').attr("seconds",0);
		 $('#liveChartInfo div.counter div.value').attr("date",date);
		 
}

function updateLiveCounter(){
		var valueElement = $('#liveChartInfo div.counter div.value');
		var value = valueElement.attr("seconds");
		var date  = valueElement.attr("date");
		
		
		
		//New parsing by moment.
		var now = moment();
		var lastUpdate = moment(date);
		
		var secDiff = (now.diff(lastUpdate)/1000).round(0);
		var stringDiff = lastUpdate.fromNow();
		
		
		// Old Second Parsing since last update
		//value = parseInt(value) + 1;
		//valueElement.attr("seconds",value);
		value = secDiff; 
	
		var date = new Date(null);
		var firstLevel =  60;
		var secondLevel = 60*60*0.5;
		var thirdLevel  = 60*60*5;
		
		
		// When the level is exceeded, blink.
		if(value == firstLevel || value == secondLevel){
			valueElement.fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
		}
		
		if(value >= firstLevel){
			// Yellowisch when 1 min to 30 min
			valueElement.css("color","#00A2E8");
			valueElement.css("font-weight",'400');
		}else if(value >= secondLevel){
			// And Red-isch when over 30 mins 
			valueElement.css("color","#F7D358");
			valueElement.css("font-weight",'700');
		}else if(value >= thirdLevel){
			valueElement.css("color","#F7D358");
		    valueElement.fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
			valueElement.css("font-weight",'900');
		}else{
			// greenisch when 0 - 60
			valueElement.css("color","black");
			valueElement.css("font-weight",'normal');
		}
		
		date.setSeconds(value); // specify value for SECONDS here
		//var result = date.toISOString().substr(11, 8);
		//valueElement.html(result);
		valueElement.html(stringDiff);
	}


  function renderLiveGraph(what) {
		if(what == undefined){
			alert("renderGraph, what undefined");
		}
		var element = $("#"+what);
		var allVals = element.data("values");
		
		if (element.data("plot")) {
			element.data("plot").destroy();
		}
		
		
		
		plot1 = $.jqplot(what, [allVals[0], allVals[1], allVals[2]],  {
seriesColors: ["rgb(0, 255, 0)", "rgb(211, 235, 59)", "rgb(237, 28, 36)"],
highlighter: {
	show: true,
	sizeAdjust: 1,
	tooltipOffset: 29
},
labelOptions:{
	fontFamily:'Helvetica',
	fontSize: '14pt'
},
 legend: {
		show: true,
		
		location: 'sw', 
		placement: 'outside',
		marginRight: '50px',
		
	},
 series: [
			{
				label: 'Watt',
				fill: false,
				
				xaxis:'xaxis',
				
			},
			{
				label: 'Volt',
				yaxis:'y2axis',
			},
			{
				label: 'Ampere',
				yaxis:'y3axis',
			}
		
			
		],
//title: 'Live-Werte der Solaranlage',
seriesDefaults:{
				   showMarker:false,
				   pointLabels: { 
				   show: false,
				   location: 'e', 
				   edgeTolerance: 5,
				  
				   }
				},
				
axes: {
		xaxis: {
					//ticks:["eins","zwei","drei"],
					tickRenderer: $.jqplot.CanvasAxisTickRenderer,
					showTicks:true,															
					numberTicks: 10,
					drawMajorGridlines: false,
					min:(
						
								
								($("#liveChart").data("values")[0] != undefined && ($("#liveChart").data("values")[0].length -40) > 0) ?
										($("#liveChart").data("values")[0].length -40)
								: 0
								
						
						
					),
					max:(
					($("#liveChart").data("values")[0] == undefined || ($("#liveChart").data("values")[0].length ) < 40) ?
								40
								: ($("#liveChart").data("values")[0].length-1)
					
					),
					tickInterval:5,
					//max:(storedData.length+5)
					},
					
				
		 y2axis: {
					
					//autoscale:true,
					min:6,
					max:18,
					tickOptions:{
						labelPosition: 'middle', 
						formatter: function(format, value) { return  value.round(2)+ "V"; },
						angle:-30
					},
					tickRenderer: $.jqplot.CanvasAxisTickRenderer,
					labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
					labelOptions:{
						fontFamily:'Helvetica',
						fontSize: '14pt'
					},
					label: 'Spannung in V',		
					
				},
		yaxis: {
					
					autoscale:true,
					labelOptions:{
						fontFamily:'Helvetica',
						fontSize: '14pt'
					},
					labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
					tickRenderer: $.jqplot.CanvasAxisTickRenderer,
					tickOptions: {
						
						formatter: function(format, value) { return  (typeof value !== 'undefined' && isNumeric(value)) ? value.round(2)+ "W" : ""; }, 
						angle: 30
					},
					label: 'Leistung in W',
				},
		y3axis: {
				//min: -10,
				//max: 10,
				autoscale:true,
				pad:5,
				autoscale:true,
				labelOptions:{
					fontFamily:'Helvetica',
					fontSize: '14pt'
				},
				labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
				tickRenderer: $.jqplot.CanvasAxisTickRenderer,
				tickOptions: {
					
					formatter: function(format, value) { return  value.round(2)+ "A"; }, 
					angle: 30
				},
				label: 'Strom in A',
			}
		
	  },
		/*canvasOverlay: {
		  show: true,
		  objects: [
			{dashedHorizontalLine: {
			  name: '12 V',
			  y: 12,
			  lineWidth: 2,
			  dashPattern: [8, 16],
			  lineCap: 'round',
			  xOffset: '25',
			  color: 'rgb(66, 98, 144)',
			  shadow: false
			}}
		  ]
		}*/					
			 }
	);
		element.data("plot",plot1);
	}
		

	
	// Updates the live chart, then calls itself after x ms 
	function updateLiveChart() {
		var onlyOnChange = true;
		var refreshAfter = 500; // refresh in x ms
        var what = "current";
		var date = "now";
		var tNum = "pow,vlt,cur,curtime"
		
		var chart = "liveChart";
		var jqChart = $("#"+chart);

		
		if(jqChart.data("values") == undefined){
			jqChart.data("values",  [[0],[0],[0],[0],["2018-06-13 19:52:32"]]);
		}
		var allValues = jqChart.data("values");
		
		
		$.getJSON( "query.php?show="+what+"&date="+date+"&wCols="+tNum, function( data ) {
			if(typeof data.err !== 'undefined' && data.err[1] != ""){
				alert(data.err[1] + "\n" + data.err[2]);
			}else{
				
				var firstRow = data.res[0];
				// Zero'd row, zero'd first and second colum.
				var newVal  = firstRow[0]; /* update storedData array*/
				var newVolt = firstRow[1]; // <-- TODO (wieso verschachteltes Array?)
				var newCur  = firstRow[2];
				var newDate = firstRow[3]; // Is not matched ... 

				oldVal  = allValues[0];
			    oldVolt = allValues[1];
				oldCur  = allValues[2];
				
				// If any of those vals change...
				eq  = (oldVal [oldVal.length -1]   == firstRow[0])
				eq &= (oldVolt[oldVolt.length-1]   == firstRow[1])
				eq &= (oldCur [oldCur.length -1]   == firstRow[2])
		
				// We push a new val-set on stack.
				// TODO RENAME Var
				
				if(!eq){
					allValues[0].push(newVal); 
					allValues[1].push(newVolt);
					allValues[2].push(newCur);
					allValues[3].push(newDate);
					renderLiveGraph(chart);
					

					updateLiveChartInfo(jqChart,newDate);
				}
				
				setTimeout(updateLiveChart, refreshAfter)	
			}
		});
	}