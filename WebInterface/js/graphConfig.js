var liveGraphConf = {
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
				   pointLabels: { show: true }
				},
				
axes: {
		xaxis: {
					//ticks:["eins","zwei","drei"],
					tickRenderer: $.jqplot.CanvasAxisTickRenderer,
					showTicks:false,															
					numberTicks: 20,
					drawMajorGridlines: false,
					min:(
						4
								//alert( ($("#liveChart").data("values") != undefined) ? chart.data("values").length : "undefined")
						
						
					),
					max:40,
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
						
						formatter: function(format, value) { return  value.round(2)+ "W"; }, 
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
			 };
	