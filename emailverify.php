<?php
	require_once('headers.php');
	require_once('functions.php');
	require_once('processor.php');
	$processes = new Processes();

	$data = json_decode(file_get_contents("php://input"));

	$otp = $data->otp;
	$email = $data->user_email;

	$email = mysql_prep($connect, trimData($email));
	$otp = mysql_prep($connect, trimData($otp));

		
	$user = $processes->verifyemail($email, $otp);
	if($user == true) {		
		    
		echo json_encode(array(
		    'message' => 'account verified',
		    'user_email' => $email
		    ));
			    
		
	}else if($user == 'errors') {
		echo json_encode(array('message' => 'error verifying account'));
		
	}else if($user == false){
		echo json_encode(array('message' => 'otp does not match'));
	}


?>