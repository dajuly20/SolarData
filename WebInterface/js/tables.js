
// Looks for JSON (query.php?what=*what*), looks for template #*what*Template fills it, and copies it to #*what*
function getJson2DIV(what, date){ // <== *what*
if (typeof date === 'undefined') { date = 'now'; }
// what is: * URL parameter to query.php e.g. query.php?what=dayStats
//          * ID  of DIV where filled template is copied to.  e.g. #dayStats
//          * ID+Template = of DIV where template is to finde e.g. #dayStatsTemplate



var dbg = false;
// console.log(" -----------------------------------------------------------------------------------");
// console.log("WHAT: " + what );
// console.log("::");
// console.log("::");
// console.log("::");

	// ID of template is always like: #whatTemplate
	// e.g. #dayStatsTemplate	
	var wholeTemplate = $("#" + what + "Template");
	if(!wholeTemplate.length) console.log("Cant find template #"+what+"Template");
	var row  = wholeTemplate.find(".row").clone();
	//var dayHead = wholeTemplate.find(".head").clone();
	var foot = wholeTemplate.find(".foot").clone();
	
	// only Table of template
	var table = wholeTemplate.contents().clone();//find("table");
	if(!$(table).find(".head").length) console.log("TR with class head cannot be found");
	if(!$(table).find(".row") .length) console.log("TR with class row cannot be found");
	if(!$(table).find(".foot").length) console.log("TR with class foot cannot be found");
	
	table.find(".row").remove();
	
	var numRows = $("#"+what).attr("numRows");
	if(numRows == undefined){
		numRows="";
	}
	
	// the corresponding SQL Query is saved in a lookup array which indexes are named like what
	// e.g. dayStats 
	$.getJSON( "query.php?show="+what+"&date="+date+"&numRows="+numRows, function( data ) {
		
		if(typeof data.err !== 'undefined' && data.err[1] != ""){
			alert(data.err[1] + "\n" + data.err[2]);
		}else{
			var tmpFoot = foot.clone();
			// Loops through the rows.
			for (i = 0; i < data.res.length; i++) { 
				var tmpRow = row.clone();
				//var_dump(data.res[i]);
				
				// Loops through Colums in Joson result
				$.each( data.res[i], function( key, val ) {
					
					// Look if there is a colum having the class ".keyname" 
					var colMatchKey = tmpRow.find("."+key);
					if(colMatchKey.length){
						//Found colum... putting in Value
						colMatchKey.find(".data").html(val);
					}
					
					// If there is none, we examine dashes. 
					// Keys can be like pow-max ... where "pow" is the colum, that contains classes for "max", "avg", "min"
					else{
						var dashFrag = key.split("-");
						var befDash = dashFrag[0];
						var aftDash = dashFrag[1];
						var colMatchKey = tmpRow.find("."+befDash);
						
						// If there is a class with the expression before the dash, we look for the according sub-column
						if(colMatchKey.length){
							subCol = colMatchKey.find("."+aftDash);
							if(subCol.length){
								subCol.append(val);
							}
							else if(dbg){
								console.log("subCol " + aftDash +  " not found!");
							}
							
						}
						
						// usind html() bevause it can occur more than once...
						else if(befDash == "footer"){
							if(tmpFoot.find("."+aftDash).length){
								tmpFoot.find("."+aftDash).html(val);
								//console.log("Found footer key "+key+" after dash: "+aftDash+" !");
							}
							else{
								//console.log("footer key "+key+" not found!");
							}
							
						}
						
						else if(dbg){
							console.log("Column " + key +  " not found!");
						}
					}
			
			
			   
				});//each
				tmpRow.insertBefore( $(table).find(".foot") );
				//tmpRow.insertAfter( $(table).find(".head") );
			
			}//for
			// 
			//console.log(tmpFoot.html());
			table.find(".foot").html( tmpFoot.html() );
			
			//$("#"+what).fadeOut();
			$("#"+what).empty();
			table.appendTo("#"+what);
			$("#"+what).css("visibility","visible");
			//$("#"+what).fadeIn("slow");
		}//else
	});//getJson

}