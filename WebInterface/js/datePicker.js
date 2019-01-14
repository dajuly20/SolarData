 function datePickerChangedDate(dateText,obj,what) {
	
				   var germanDate = DateFormat.format.date(dateText, "dd.MM.yyyy");
				   if(what == undefined){
					   // *this* is the input element of the date picker.
					   // we travers upward to the closest class navi, which has to supply *ref* attribute.
					   what = ($(this).closest(".navi").attr("ref"));
					   //alert("calculated what: "+what);
				   }else{
					  // alert("received what: "+what);
				   }
				   var dateShowDiv = $("#"+what+"Navi .showDate");
				   dateShowDiv.html(germanDate);
				   var oldBgColor = dateShowDiv.css("backgroundColor");
				    dateShowDiv.animate({
					  backgroundColor: "#aa0000",
					  color: "#fff",
					  width: 500
					}, 1000 ).animate({
					  backgroundColor: oldBgColor,
					  color: "#000",
					  width: 500
					}, 1000 );
					//alert("Selected date: " + dateText + "; input's current value: " + this.value);
					
					element = $("#"+what);
					
					// If the element has the class chart, then it is a chart.
					if(element.hasClass("chart")){
						var chartWhat = what;
						newWhat = element.attr("ref");
						
						//updateBarChart(newWhat,dateText);
						// Does Refresh really gather calendar info? 
						triggerRefresh(what);
					}
					
					// Otherwise it's a table
					else{
						getJson2DIV(what,dateText);
					}
	}
	
function resetDatePicker(what){
	var datepicker = $("#"+what+"DataPicker");
	if(!datepicker.length){console.log("resetDatepicker no datePicker found on "+what); return 1;}
	datepicker.datepicker('setDate',null);
}
	
$(document).ready(function(){
	
	var degree = 0, timer;
	var angle = 0;
	var interval;
	/***************************************************
	 * Refresh function ... 
	 * 
	 * basically rather general, but it looks for a
     * date picker value when supplied.
	 */
	$(".liveData, .navi").on("click", "a.refresh",  function(event){
		// GO upwards DOM and find the closest parent with class live data.
		//var id 	= $(this).closest(".liveData").attr("id");
		var id = $(this).attr("ref");
	    var datePickerId = id + "DatePicker";	
		var navi = id + "Navi";
		var mainElement = $("#"+id);
		
		// When there is a datepicker, we find out it's date.
		if($("#"+datePickerId) == undefined){
			dateStr = "now";
		}
		else{
			dateStr = $("#"+datePickerId).val();
		}
		
		// And when there is a Order-chooser, find out its order.
		changeOrder = $("#"+navi+" a.changeOrder");
		//console.log("#"+navi+" a.changeOrder");
		//console.log("ChangeOrderLength" + changeOrder.length);
		if(changeOrder.length > 0){
			order = changeOrder.attr("order");
		}
		else{
			order = "";
		}
		
		
		// if the main element has the class chart, it is a chart.
		if(mainElement.hasClass("chart")){
			what = mainElement.attr("ref");
			
			// i am chart ( ID == what)
			updateBarChart(what,dateStr, order);
		}
		
		// else it will be treated as table.
		else{
			// i am table  (does NOT support dateChange or Order change yet.)
			getJson2DIV(id);
		}
		
		
		var img = $(this).find("img");
		
		// Animate symbol
		interval = setInterval(function(){
		angle+=7;
		img.rotate(angle);
		},50)
		
		mainElement.data("intervalRefreshImage", interval);
		
		
		 
		return false;
	}); // handlerRefreshClick
	
	
	
				
		//#dayStatsDatePicker			 
	$(".datePicker").datepicker({
		buttonImage: '/img/calendar.png',
		dayNamesMin: 		[ "So", "Mo", "Di", "Mi", "Do", "Fr", "Sa" ],
		monthNames: 		[ "Januar", "Februar", "Marts", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember" ],
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
		// Points to element that's beeing refreshed.
		var ref = naviDiv.attr("ref");
		
		var datePicker = $( "#" + ref + "DatePicker" );
		var addDays = clickedImg.attr("addDays");
		var date = datePicker.datepicker("getDate");
		if(null == date){ date = new Date(); }
		
		var _oneDay = (1000*60*60*24);
		date.setTime(date.getTime() + (addDays * _oneDay));
		datePicker.datepicker( "setDate", date );
		var dateString = datePicker.val()
		// ref = id of element that owns the picker... e.g. dayStats
		datePickerChangedDate(dateString, null, ref );
		//alert("ref ist: "+ref+" AddDays: "+addDays);
	});
			
	// Bind Click handler to datepicker symbol aka input.	
	$( ".datepicker" ).datepicker();
});