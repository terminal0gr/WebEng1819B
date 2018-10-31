<?php
$A=['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
$B=['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
$C=['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];

$counter=0;

for($i=0;$i<1;$i++){
	for($j=0;$j<26;$j++){
		for($k=0;$k<26;$k++){
			$counter++;
			$airport="";
			$code = $A[$i].$B[$j].$C[$k];
			echo($counter." - ".$code);
			
			
			$airport_request = 'https://api.sandbox.amadeus.com/v1.2/airports/autocomplete?apikey=3jl39kYzxVtbQA9UWNg9BTV5Su3vLGnu&term='.$code;
			$airport_response  = file_get_contents($airport_request);
			$airport_jsonobj  = json_decode($airport_response);
			
			foreach($airport_jsonobj as $airport_result)
			{
				if($airport_result->value == $code) $airport = $airport_result->label;
			}
			echo ">>>". $airport."<br>";
			
			
		}
	}
}

?>