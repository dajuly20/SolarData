Number.prototype.round = function(places) {
  return +(Math.round(this + "e+" + places)  + "e-" + places);
}

function isNumeric(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}


function var_dump(obj, element)
{
    var logMsg = objToString(obj, 0);
    if (element) // set innerHTML to logMsg
    {
        var pre = document.createElement('pre');
        pre.innerHTML = logMsg;
        element.innerHTML = '';
        element.appendChild(pre);
    }
    else // write logMsg to the console
    {
        console.log(logMsg);
    }
}

function objToString(obj, level)
{
    var out = '';
    for (var i in obj)
    {
        for (loop = level; loop > 0; loop--)
        {
            out += "    ";
        }
        if (obj[i] instanceof Object)
        {
            out += i + " (Object):\n";
            out += objToString(obj[i], level + 1);
        }
        else
        {
            out += i + ": " + obj[i] + "\n";
        }
    }
    return out;
}

	
function scrollToId(id){
	var idTag = $("#"+ id);

	$('html,body').animate({scrollTop: idTag.offset().top},'slow');
}
	

		
function mostRecentValues(element){
	var values = $(element).data("values");
	var lastValue     = values[1].length -1;
	var numCategories = values   .length -1;
	var retArr = [];
	for(var i =0; i <= numCategories; i++){
		retArr.push(values[i][lastValue]);
	}
	return retArr;
}
	

// For navigation	
function openNav() {
    document.getElementById("myNav").style.width = "270px";
}

function closeNav() {
    document.getElementById("myNav").style.width = "0px";
}

function triggerRefresh(id){
	var refNavi = $("#"+id+"Navi");
	if(!refNavi.length){console.log("TriggerRefresh: no Navi found on "+id);return 1;}
	var refresh = refNavi.find("a.refresh");
	if(!refresh.length){console.log("Trigger Refresh: no Refresh icon found on "+id);return 1;}
	refresh.trigger("click");
}
     
  
$(document).ready(function(){

	// Initialisation for BarChart.
	 updateLiveChart();

	// counts up the Time, since new data was received.
	setInterval(function () {
		updateLiveCounter();
	}, 1000);
	

	// Each element that has the class liveData also has a Id, which then will be loaded.
	$(".liveData").each(function(index, value){
		var id = $(this).attr("id");
		getJson2DIV(id);
	});
   

	 // There is a Eye icon new to each heading. So a section can be hidden.
	$("a.changeOrder").click(function(){
		var lnk 	= $(this);
		var refDiv 	= $("#"+lnk.attr("ref"));
		var refId   = lnk.attr("ref");
		var refNavi = $("#"+lnk.attr("ref")+"Navi");
		var order 	= lnk.attr("order");
		
		
		// If show is true, then it WAS showed. ON-Click its hiding
		if(order == "desc"){
			// shown on default!
			lnk.find("img").attr("src","img/like.png");
			lnk.attr("order","asc");
			// refDiv.fadeIn();
			// refNavi.fadeIn();
		}
		else{
			lnk.find("img").attr("src","img/flop.png");
			lnk.attr("order","desc");
			// refDiv.fadeOut();
			// refNavi.fadeOut();  //ASC = top5
		}
		
		// var refresh = refNavi.find("a.refresh");
		// refresh.trigger("click");
		triggerRefresh(refId);

		return false;
	});//clickhandler
	

   
    // There is a Eye icon new to each heading. So a section can be hidden.
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
	
	
	$(".scrollTo").click(function(){
		var id = $(this).attr("ref");
		resetDatePicker(id);
		triggerRefresh(id);
		scrollToId(id+"Heading");
		
		closeNav();
	});
			
			
	$("#refreshAll").click(function(){
		// real reload or rather trigger refresh id? 
		window.location.reload();
	});	
			
});//ready
