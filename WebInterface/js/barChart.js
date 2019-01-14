	
	 
$(document).ready(function(){
	
	// Cick handler for BestDay Bar-Chart. 
	// Refreshes *dayStats* and scrolls up there.
	 $('#bestDayChart, #weekStatsChart').bind('jqplotDataClick', 
		function (ev, seriesIndex, pointIndex, data) {
			var toElement = "dayStatsChart";
			var date = $(this).data("dates")[pointIndex];

			// Change the Datepicker itself (so back arrow will work)
			$("#" + toElement + "DatePicker").val(date);
			// Then fire the function (like the arrow would do) with the new date.
			datePickerChangedDate(date,null,toElement);
			// And Finaly scroll to the element.
			scrollToId(toElement+"Heading");
			closeNav();
	
	});
	
	// initiate Bar-Charts 
	$(".barchart").each(function(index, value){
		var id = $(this).attr("ref");
		triggerRefresh(id+"Chart")
		console.log("hier");
		console.log(id);
		//updateBarChart(id);
		
	});
	
	 // updateBarChart("weekStats");
	 // updateBarChart("dayStats");
	 // updateBarChart("bestDay");

	/*************************
	 ** Week Stats 
	 **
	 **/
	 $('.barchart').bind('jqplotDataMouseOver', 
            function (ev, seriesIndex, pointIndex, data) {
			   var powMin = $(this).data("pow-min")[pointIndex];
			   var powAvg = $(this).data("pow-avg")[pointIndex];
			   var powMax = $(this).data("pow-max")[pointIndex];
			   
			   id = $(this).attr("id");
			   
			   
			   $('#'+id+'Info').html("Leistung ⬇"+powMin+"W  Ø"+powAvg+"W ⬆"+powMax+"W");
            }
        );
             
        $('.barchart').bind('jqplotDataUnhighlight', 
            function (ev) {
				id = $(this).attr("id");
                $('#'+id+'Info').html('&nbsp;');
            }
        );
		
		
		
	 // $('#dayStatsChart').bind('jqplotDataMouseOver', 
	// function (ev, seriesIndex, pointIndex, data) {
	   // var powMin = $(this).data("pow-min")[pointIndex];
	   // var powAvg = $(this).data("pow-avg")[pointIndex];
	   // var powMax = $(this).data("pow-max")[pointIndex];
	   
	   // $('#dayStatsChartInfo').html("Leistung Min: "+powMin+"W Avg: "+powAvg+"W Max: "+powMax+"W");
	// }
// );
             
        // $('#dayStatsChart').bind('jqplotDataUnhighlight', 
            // function (ev) {
                // $('#dayStatsChartInfo').html('&nbsp;');
            // }
        // );

		
});


function renderBarChart(line1,what){
	var chartName =  what + "Chart";
	var chartElem = $("#"+chartName);
	
	if (chartElem.data("plot")) {
		chartElem.data("plot").destroy();
	}
	
	

	
	plot2 = $.jqplot(chartName, [line1], {
		//title:'Stärkste Ladungsdifferenz',
		seriesDefaults:{
			renderer:$.jqplot.BarRenderer,
			rendererOptions: { fillToZero: true },
			pointLabels: { show: true }
		},
		axesDefaults: {
			tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
			tickOptions: {
			  angle: -30,
			  fontSize: '10pt'
			}
		},
		axes:{
			xaxis:{
				renderer: $.jqplot.CategoryAxisRenderer,	
			},
			yaxis:{
				tickOptions: {
					formatter: function(format, value) { return  value.round(2)+ "A/h"; } 
				}
			}
		}
	});
	
	//Asign element to div element.
	chartElem.data("plot",plot2);
}



function updateBarChart(what,date,order,extraCols){
	
		var chart = what+"Chart";
		if(date == undefined) { date = "now";}
		var el = $("#"+chart);	
		
		if(order == undefined){
			order = "";
		}
	
	
		
		var tNum = "date,chgDiff";
		if(extraCols == undefined || extraCols == "") {
			extra = "linkDate,pow-min,pow-avg,pow-max";
		}
		var uri = "query.php?show="+what+"&date="+date+"&wCols="+tNum+","+extra+"&order="+order;
		$.getJSON( uri, function( data ) {
			if(typeof data.err !== 'undefined' && data.err[1] != ""){
				alert(data.err[1] + "\n" + data.err[2]);
				return null;
			}
			else{
				// Return tables have to be split. One Array goes into chart, 
				first2Colums 	= data.res.map(function(value,index) { return  [value[0],value[1]]; });
				renderBarChart(first2Colums, what);
				
				// second is saved as extra data in the Jquer element.
				
				
				$("#"+chart).data("dates"  , data.res.map(function(value,index) { return  value[2]; }));
				$("#"+chart).data("pow-min", data.res.map(function(value,index) { return  value[3]; }));
				$("#"+chart).data("pow-avg", data.res.map(function(value,index) { return  value[4]; }));
				$("#"+chart).data("pow-max", data.res.map(function(value,index) { return  value[5]; }));
				
				
			}
			clearTimeout(el.data("intervalRefreshImage"));
		 
		});
}//f-updateBarchart
	
