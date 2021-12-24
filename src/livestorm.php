<?php

function registerToSession($idsession, $email, $firstname, $lastname, $company, $ciefun, $country)
{
	$cr = curl_init();

	$token = "SECRET_KEY";
	$url = "https://api.livestorm.co/v1/sessions";

	$data = '{
		"data": {
			"type": "people",
			"attributes": {
				"fields":[
					{"id":"email","value": "'.$email.'"},
					{"id":"first_name","value":"'.$firstname.'"},
					{"id":"last_name","value":"'.$lastname.'"}, 
					{"id": "company", "value":"'.$company.' ('.$ciefun.')"},
					{"id": "job", "value":"'.$ciefun.'"},
					{"id": "country", "value":"'.$country.'"}
				]
			}
		}
	}';

	$timestamp = time(); 
	$signature = hash_hmac('sha1', $timestamp, $token);

	$headers = [
		"HTTP/2",
		"authorization: ".$token,
		"Accept: application/vnd.api+json",
		"Content-Type: application/json",
		"sig: ".$signature,
	];

	curl_setopt($cr, CURLOPT_URL, $url."/".$idsession."/people");
	curl_setopt($cr, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($cr, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($cr, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($cr, CURLINFO_HEADER_OUT, true);
	//Only register participant
	curl_setopt($cr, CURLOPT_POST, true);
	curl_setopt($cr, CURLOPT_POSTFIELDS, $data);

	$response = curl_exec($cr);
	$error = curl_error($cr);

	if($error) {
		//var_dump($error);
	    $retour = array("code" => 2, "msg" => "Une erreur est survenue pendant la communication");
	}
	else{
		$response = json_decode($response, true);
		
		if (!empty($response["errors"])) 
		{
			if ($response["errors"][0]["code"] == "422"){
				$retour = array("code" => 3, "msg" => "You are already register for this session");
			}
			else if ($response["errors"][0]["code"] == "400"){
				$retour = array("code" => 1, "msg" => "Error in the form (Please contact webmaster)");
			}
			else if ($response["errors"][0]["code"] == "404"){
				$retour = array("code" => 1, "msg" => "Error in the form (Please contact webmaster)");
			}
			else if ($response["errors"][0]["code"] == "429"){
				$retour = array("code" => 429, "msg" => "Too much request ! Please try later or contact webmaster");
			}
		}
		else
		{
			$retour = array("code" => 0, "msg" => "Votre inscription est enregistrée, veuillez consulter votre messagerie");
		}
	}

	curl_close($cr);

	return $retour;
}