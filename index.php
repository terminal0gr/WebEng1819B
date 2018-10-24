<html>
<head>
	<link href="styles.css" rel="stylesheet" type="text/css" />
	<title>PHP Bing</title>
</head>

<body>
	<?php ini_set("allow_url_fopen", 1); ?>
	<form method="post" action=""> 
		Από:<input type="text" id="origin" name="origin" value="<?php 
		if (isset($_POST['origin'])){
			echo($_POST['origin']); }
		else { 
			echo('');
		}
		?>"/>
        Σε:<input type="text" id="destination" name="destination" value="<?php 
		if (isset($_POST['destination'])){
			echo($_POST['destination']); }
		else { 
			echo('');
		}
		?>"/>
		Ημ/νία αναχώρησης:<input type="text" id="departure_date" name="departure_date" value="<?php 
		if (isset($_POST['departure_date'])){
			echo($_POST['departure_date']); }
		else { 
			echo('');
		}
		?>"/>
		<input type="submit" value="Search!" name="submit" id="searchButton" />
		
	</form>
	
	<?php
		//echo $_POST['submit'];
		if (isset($_POST['submit'])) {
			$request = 'https://api.sandbox.amadeus.com/v1.2/flights/low-fare-search?apikey=3jl39kYzxVtbQA9UWNg9BTV5Su3vLGnu&origin='.$_POST["origin"].'&destination='.$_POST["destination"].'&departure_date='.$_POST["departure_date"].'&currency=EUR&number_of_results=250';
			echo "Request url: <br><b>".$request."</b><br>";
			$response  = file_get_contents($request);
			$jsonobj  = json_decode($response);
			echo "<br><u>ΑΠΟΤΕΛΕΣΜΑΤΑ ΑΝΑΖΗΤΗΣΗΣ:</u><br>".$response;
			echo "<br><u>Νόμισμα:</u>".$jsonobj->currency;
			
			echo('<ul>');
			foreach($jsonobj->results as $results)
			{
				foreach($results->itineraries as $itineraries) {
					echo("<br>Διάρκεια: ".$itineraries->outbound->duration);
					foreach($itineraries->outbound->flights as $flights) {
						echo("   Αναχώρηση: ".$flights->departs_at);
						echo("   Άφιξη: ".$flights->arrives_at);
					}
				}
				

			}
			echo('</ul>');
			
			
			/*
			echo('<ul ID="resultList">');                    
                
			foreach($jsonobj->SearchResponse->Image->Results as $value)
			{
				echo('<li class="resultlistitem"><a href="' . $value->Url . '">');
				echo('<img src="' . $value->Thumbnail->Url. '"></li>');

			}
			echo("</ul>");
			*/
		} ?>
	
	
	
	
</body>
</html>