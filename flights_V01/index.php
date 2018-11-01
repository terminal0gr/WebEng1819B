<?php
	include("./config.php");
	include("./functions.php");
?>


<!DOCTYPE html>
<head>
	<meta charset="utf-8">
	<title>Flight Scanner</title>
	<link rel="icon" type="image/png" href="./images/plane_02.png"/>
	
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
	<script src="http://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

	<script src="./scripts.js"></script>
	<link rel="stylesheet" href="./styles.css" type="text/css" />
	
	<script>
		$(function() {
			$( "#dialog" ).dialog({ autoOpen: false });
			$( "#opener" ).click(function() {
			  $( "#dialog" ).dialog( "open" );
			});
			
			function log( message ) {
				$( "<div>" ).text( message ).prependTo( "#log" );
				$( "#log" ).scrollTop( 0 );
			}
			$( "#origin" ).autocomplete({
				source: function( request, response ) {
					$.ajax({
						url: "https://api.sandbox.amadeus.com/v1.2/airports/autocomplete",
						dataType: "json",
						data: {
							apikey: "3jl39kYzxVtbQA9UWNg9BTV5Su3vLGnu",
							term: request.term
						},
						success: function( data ) {
							response( data );
						}
					});
				},
				minLength: 3,
				select: function( event, ui ) {
					log( ui.item ?
							"Selected: " + ui.item.label :
							"Nothing selected, input was " + this.value);
				},
				open: function() {
					$( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
				},
				close: function() {
					$( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
				}
			});
			
			$( "#destination" ).autocomplete({
				source: function( request, response ) {
					$.ajax({
						url: "https://api.sandbox.amadeus.com/v1.2/airports/autocomplete",
						dataType: "json",
						data: {
							apikey: "3jl39kYzxVtbQA9UWNg9BTV5Su3vLGnu",
							term: request.term
						},
						success: function( data ) {
							response( data );
						}
					});
				},
				minLength: 3,
				select: function( event, ui ) {
					log( ui.item ?
							"Selected: " + ui.item.label :
							"Nothing selected, input was " + this.value);
				},
				open: function() {
					$( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
				},
				close: function() {
					$( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
				}
			});			
		});
	</script>	
	
</head>

<body>
	<?php 
	  ini_set("allow_url_fopen", 1); 
	  session_start() 
	?>

	<form method="post" action=""> 
		<label class="control-label">Από: </label>
		<input type="text" id="origin" name="origin" class="form-textbox" value="<?php 
			if (isset($_POST['origin'])){
				echo($_POST['origin']); }
			else { 
				echo('');
			}
			?>"/>
		<label class="control-label">Σε: </label>
		<input type="text" id="destination" name="destination" class="form-textbox" value="<?php 
			if (isset($_POST['destination'])){
				echo($_POST['destination']); }
			else { 
				echo('');
			}
			?>"/>
		<label class="control-label">Ημ/νία αναχώρησης: </label>
		<input type="text" id="departure_date" required name="departure_date" size=6 placeholder="dd/mm/yyyy" class="form-date form-textbox" value="<?php 
			if (isset($_POST['departure_date'])){
				echo($_POST['departure_date']); }
			else { 
				echo('');
			}
			?>"/>
		<label class="control-label">Ημ/νία επιστροφής: </label>
		<input type="text" id="return_date" name="return_date" size=6 placeholder="dd/mm/yyyy" class="form-textbox form-date" value="<?php 
			if (isset($_POST['return_date'])){
				echo($_POST['return_date']); }
			else { 
				echo('');
			}
			?>"/>

	<br/>
    
	<input type="submit" value="Αναζήτηση" name="submit" class="btn" id="searchButton" />
		
	</form>
	
	<?php
		//echo $_POST['submit'];
		if (isset($_POST['submit'])) {
			
			$tdeparture_date = ((strpos($_POST["departure_date"], "-") === true ) ? explode("-", $_POST["departure_date"]) : explode("/", $_POST["departure_date"]) );
			//$fdeparture_date = ((strpos($_POST["departure_date"], "-") === true ) ? "-" : "/" );
			
			$fdeparture_date = $tdeparture_date[2]."-".$tdeparture_date[1]."-".$tdeparture_date[0];
			//echo("<br>3: ".$tdeparture_date[0]);
			//echo("<br>4: ".$fdeparture_date);
			
			$request = 'https://api.sandbox.amadeus.com/v1.2/flights/low-fare-search?apikey=3jl39kYzxVtbQA9UWNg9BTV5Su3vLGnu&currency=EUR&origin='.$_POST["origin"].'&number_of_results=250'.'&destination='.$_POST["destination"].'&departure_date='.$fdeparture_date;
			//$request = 'https://api.sandbox.amadeus.com/v1.2/flights/low-fare-search?apikey=3jl39kYzxVtbQA9UWNg9BTV5Su3vLGnu&currency=EUR&origin='.$_POST["origin"].'&number_of_results=250'.'&destination='.$_POST["destination"].'&departure_date='.$_POST["departure_date"];

			echo "<div id=request>Request url: <br><b>".$request."</b></div><br>";
			$response  = @file_get_contents($request);
			$code=getHttpCode($http_response_header);
			$jsonobj  = json_decode($response);
      
			echo "<br><div id='flightsnum'><u>ΑΠΟΤΕΛΕΣΜΑΤΑ ΑΝΑΖΗΤΗΣΗΣ:</u></div><br>";

			if($code == 400) {
				echo "Δεν βρέθηκαν πτήσεις με τα κριτήρια που δώσατε!";
			} elseif ($code == 200) {
				$flights_sum = 0;
				echo "<br><u>Νόμισμα:</u>".$jsonobj->currency;
				
				echo('<table id="results" border=1>');
				?>
				<tr><td rowspan=2 align=center>Διάρκεια</td>
					<td colspan=2 align=center>Αναχώρηση</td>
					<td colspan=2 align=center>Άφιξη</td>
					<td colspan=3 align=center>Πτήση</td>
					<td colspan=2 rowspan=2 align=center>Θέση</td>
					<td rowspan=2 align=center>Διαθέσιμες<br>θέσεις</td>
					<td colspan=3 align=center>Συν. Κόστος</td>
				</tr>
				<tr><td align=center>Αεροδρόμιο</td>
					<td align=center>Ώρα</td>
					<td align=center>Αεροδρόμιο</td>
					<td align=center>Ώρα</td>
					<td align=center>Airline</td>
					<td align=center>Αριθμός</td>
					<td align=center>Αεροσκάφος</td>
					<td align=center>Total Price</td>
					<td align=center>Total Fare</td>
					<td align=center>Tax</td>
					
				</tr>
				
				<?php
				foreach($jsonobj->results as $results)
				{
					
					echo('<tr>');
					
					
					foreach($results->itineraries as $itineraries) {
						$dromologia=count($itineraries->outbound->flights);
						$duration = explode(":", $itineraries->outbound->duration);
						
						echo('<tr><td colspan=14 style="background: lightblue; line-height:0.01">&nbsp;</td></tr><tr>');
						$flights_sum++;
						echo("<td align=center rowspan=".$dromologia.">".$duration[0]."ώ ".$duration[1]."λ<br>");
						if($dromologia == 1) 
							echo("<b>Απευθείας πτήση</b></td>");
						else
							echo("<b>".($dromologia - 1).(($dromologia - 1)>1?" στάσεις":" στάση")."</b></td>");
						
						$index=0;
						
						foreach($itineraries->outbound->flights as $flights) {
							if($index > 0) echo("<tr>");
							
							
							echo("<td align=center>".$flights->origin->airport."<br>".find_airport($flights->origin->airport)."</td>");
							
							$departure=explode("T", $flights->departs_at);
							$tmp_departure_date = explode("-", $departure[0]);
							$departure_date = $tmp_departure_date[2]."/".$tmp_departure_date[1]."/".$tmp_departure_date[0];
							$departure_time = $departure[1];
							echo("<td align=center>".$departure_date."<br>".$departure_time."</td>");
							
							echo("<td align=center>".$flights->destination->airport."<br>".find_airport($flights->destination->airport)."</td>");
							
							$arrival=explode("T", $flights->arrives_at);
							$tmp_arrival_date = explode("-", $arrival[0]);
							$arrival_date = $tmp_arrival_date[2]."/".$tmp_arrival_date[1]."/".$tmp_arrival_date[0];
							$arrival_time = $arrival[1];
							echo("<td align=center>".$arrival_date."<br>".$arrival_time."</td>");
							
							echo("<td align=center>".$flights->operating_airline."<br>".find_airline($flights->operating_airline)."</td>");
							echo("<td align=center>".$flights->flight_number."</td>");
							echo("<td align=center>".$flights->aircraft."</td>");
							echo("<td align=center>".$flights->booking_info->travel_class."</td>");
							echo("<td align=center>".$flights->booking_info->booking_code."</td>");

              				$seats=$flights->booking_info->seats_remaining;
              				if($flights->booking_info->seats_remaining==9) $seats=">=9";
							echo("<td align=center>".$seats."</td>");
							
							if($index == 0) echo("<td align=center rowspan=".$dromologia.">".$results->fare->total_price."</td>");
							if($index == 0) echo("<td align=center rowspan=".$dromologia.">".$results->fare->price_per_adult->total_fare."</td>");
							if($index == 0) echo("<td align=center rowspan=".$dromologia.">".$results->fare->price_per_adult->tax."</td>");
							if($index > 0) echo("</tr>");
							$index++;
						}

						If(isset($itineraries->inbound)) {
							foreach($itineraries->inbound->flights as $flights) {
								if($index > 0) echo("<tr>");
								
								echo("<td align=center>".$flights->origin->airport."<br>".find_airport($flights->origin->airport)."</td>");
								
								$departure=explode("T", $flights->departs_at);
								$tmp_departure_date = explode("-", $departure[0]);
								$departure_date = $tmp_departure_date[2]."/".$tmp_departure_date[1]."/".$tmp_departure_date[0];
								$departure_time = $departure[1];
								echo("<td align=center>".$departure_date."<br>".$departure_time."</td>");
								
								echo("<td align=center>".$flights->destination->airport."<br>".find_airport($flights->destination->airport)."</td>");
								
								$arrival=explode("T", $flights->arrives_at);
								$tmp_arrival_date = explode("-", $arrival[0]);
								$arrival_date = $tmp_arrival_date[2]."/".$tmp_arrival_date[1]."/".$tmp_arrival_date[0];
								$arrival_time = $arrival[1];
								echo("<td align=center>".$arrival_date."<br>".$arrival_time."</td>");
								
								echo("<td align=center>".$flights->operating_airline."<br>".find_airline($flights->operating_airline)."</td>");
								echo("<td align=center>".$flights->flight_number."</td>");
								echo("<td align=center>".$flights->aircraft."</td>");
								echo("<td align=center>".$flights->booking_info->travel_class."</td>");
								echo("<td align=center>".$flights->booking_info->booking_code."</td>");

								$seats=$flights->booking_info->seats_remaining;
								if($flights->booking_info->seats_remaining==9) $seats=">=9";
								echo("<td align=center>".$seats."</td>");
								
								if($index == 0) echo("<td align=center rowspan=".$dromologia.">".$results->fare->total_price."</td>");
								if($index == 0) echo("<td align=center rowspan=".$dromologia.">".$results->fare->price_per_adult->total_fare."</td>");
								if($index == 0) echo("<td align=center rowspan=".$dromologia.">".$results->fare->price_per_adult->tax."</td>");
								if($index > 0) echo("</tr>");
								$index++;
							}
						}
					}
					
					echo('</tr>');
					
				}
				echo('</table>');
				
				 echo "<script> 
							alert('Βρέθηκαν ".$flights_sum." πτήσεις με τα κριτήρια που δώσατε');
							var numobj = document.getElementById('flightsnum');
							numobj.innerHTML = '<u>ΑΠΟΤΕΛΕΣΜΑΤΑ ΑΝΑΖΗΤΗΣΗΣ:</u> <b>(Βρέθηκαν ' + ".$flights_sum." + ' πτήσεις)</b>';
					   </script>";
				
				
			}	
				
			
		} ?>
	
	
	
	
</body>
</html>