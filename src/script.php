<?php
	
	require_once(__DIR__.'/livestorm.php');

	//var_dump($_POST);
if (isset($_POST["form"]['ACCEPTATION']))
{
	if (!empty($_POST["form"]['PRENOM']) && !empty($_POST["form"]['NOM']) && !empty($_POST["form"]['FONCTION']) && !empty($_POST["form"]['EMAIL']) && !empty($_POST["form"]['ENTREPRISE']) && !empty($_POST["form"]['PAYS']) && !empty($_POST["form"]['EVENEMENT']))
	{
		$firstname = htmlspecialchars($_POST["form"]['PRENOM']);
		$lastname = htmlspecialchars($_POST["form"]['NOM']);
		$email = htmlspecialchars($_POST["form"]['EMAIL']);
		$ciefun = htmlspecialchars($_POST["form"]['FONCTION']);
		$company = htmlspecialchars($_POST["form"]['ENTREPRISE']);
		$country = htmlspecialchars($_POST["form"]['PAYS'][0]);

		if (is_array($_POST["form"]['EVENEMENT']))
		{
			$sessions = $_POST["form"]['EVENEMENT'];

			$i = 0;
			$cut_while = false;
			$attempt = 0;
			$nbsuccess = 0;
			$nbalready = 0;
			$nberrors = 0;

			$ret_req = array();

			while ($i < count($sessions) && !$cut_while)
			{
				$retour = registerToSession($sessions[$i], $email, $firstname, $lastname, $company, $ciefun, $country);
				/*echo '<br />';
				var_dump($retour);
				echo '<br />';*/

				array_push($ret_req, $retour);

				if ($retour["code"] == 0){
					$nbsuccess++;
					$i++;
				}
				else if ($retour["code"] == 3){
					$nbalready++;
					$i++;
				}
				else if ($retour["code"] == 429 && $attempt < 3)
				{
					sleep(1);
					$attempt++;	
				}
				else if ($retour["code"] == 429 && $attempt >= 3)
				{
					$nberrors++;
					$i++;
				}
			}

			$status = array("status" => true, "msg" => "", "original" => $ret_req);

			if ($nberrors != 0){
				$status['status'] = false;
				$status['msg'] .= "There is ".$nberrors." errors !";
			}

			if ($nbalready != 0){
				$status['msg'] .= "You were already registered for ".$nbalready." sessions !";				
			}

			if ($nbsuccess != 0){
				$status['msg'] .= "You are registered for ".$nbsuccess." sessions !";				
			}

			echo json_encode($status);
			//echo 'success: '.$nbsuccess. '<br /> already: '.$nbalready.'<br /> errors: '.$nberrors;
			// TODO: send a webmaster mail when error occurs
		}
		else{
			$error = array("code" => 1, "msg" => "You have not selected an event");
			echo json_encode($error);
		}	
	}
	else
	{
		$error = array("code" => 1, "msg" => "Some mandatory input fields are empty");
		echo json_encode($error);
	}
}
else
{
	$error = array("code" => 1, "msg" => "Please accept our terms of conditions");
	echo json_encode($error);
}

?>