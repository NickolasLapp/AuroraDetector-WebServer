<?php
	include_once 'constants.php';
        require_once("./include/membersite_config.php");
?>


<script type="text/javascript">
	$( "#processdates" ).click(function() {
            if(validateDateRange())
		{
                    var startDate = $("#startdate").datepicker( "getDate" );
                    var endDate = $("#enddate").datepicker( "getDate" );
                    
                    var xmlhttp=new XMLHttpRequest();
                    xmlhttp.onreadystatechange=function(){
                    if (xmlhttp.readyState===4 && xmlhttp.status===200)
                        {
                          document.getElementById("secondtab").innerHTML=xmlhttp.responseText;
                        }
                    };
                    xmlhttp.open("POST","returnArchivedData.php?startdate="+startDate+"&endDate="+endDate,true);
                    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                    xmlhttp.send();
                    }
		else
			alert( "Invalid Date Range. Please specify a valid date range");
 	});

	function validateDateRange(){
		return true;
	}

	function amChartInited(chart_id){
		flashMovie = document.getElementById(chart_id);
		flashMovie.reloadSettings(selected_settings);
	}
	
	// www.sean.co.uk
	function pausecomp(millis) {
		var date = new Date();
		var curDate = null;
		
		do { curDate = new Date(); }
		while(curDate-date < millis);
	} 
	
	$(function(){
		$( "#tab2_content" ).accordion({ animated: false, autoHeight: false });
/*		$( "#selector" ).load('archive.php', function() {
 			selected_date = $(".chart_link:first").text();
			$("#tab2_content").accordion( "activate", 1 );
			$('#current_date').text( selected_date +' UTC');
			
			selected_settings = 'chart-settings.php?date='+selected_date;
				
			$("a.chart_link").click(function(event) {
			    selected_date = $(this).text();
				$('#current_date').text(selected_date+' UTC');
				$("#tab2_content").accordion( "activate", 1 );

				selected_settings = 'chart-settings.php?date='+selected_date;
				
				event.preventDefault(); 
			});
		});			*/
	});
	
  $(function() {
    $( "#startdate" ).datepicker();
	$( "#enddate" ).datepicker();
  });
</script>

<div id="tab2_heading"><h4>Archived Data</h4></div>

<div id="tab2_content" style="width: 800px;">
	<h3><a href="#">Select Date</a></a></h3>
	<div class="bordered_lrb">
		<form>
		  <p>Start Date:</p> <input type="text" id="startdate">
		  <p>End Date:</p>   <input type="text" id="enddate"> 
		  <p><input type="button" id="processdates" value="Retrieve Data"></p>
		</form>
	</div>
	<h3><a href="#">Data For <span id="current_date"></span></a></h3>
            <div id="secondtab" class="bordered_lrb">
		<!-- amstock script-->
		<script type="text/javascript" src="amstock/swfobject.js"></script>
		<div id="flashcontent">
		   <strong>You need to upgrade your Flash Player</strong>
		</div>
		 
		<script type="text/javascript">
		  // <![CDATA[		
		  var so = new SWFObject("amstock.swf", "amstock", "750", "500", "8", "#FFFFFF");
		  so.addVariable("path", "");
		  so.addVariable("settings_file", escape("dummy_settings.xml"));
		  so.addVariable("preloader_color", "#006699");
		  so.addVariable("chart_id", "amstock");
		//so.addVariable("chart_settings", "");
		//so.addVariable("additional_chart_settings", "");
		  so.addVariable("loading_settings", "Loading Aurora Detector Network data...\nThis may take a few moments");
		//so.addVariable("error_loading_file", "ERROR LOADING FILE: ");
		  so.write("flashcontent");
		  // ]]>
		</script>
		<!-- end of amstock script -->
	</div>
</div>	