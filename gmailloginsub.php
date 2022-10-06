<?php
	require_once('headers.php');
	require_once('functions.php');
	require_once('processor.php');
	$processes = new Processes();

	$data = json_decode(file_get_contents("php://input"));

	$email = $data->email;
    
    // $otp = "";

    // for($i=0; $i < 5; $i++) { 
    //      $otp .= $num[$i] = rand(0, 9);
    // }
    
	$email = mysql_prep($connect, trimData($email));
	$user = $processes->gmailloginUsers($email);
	if($user != false) {
		echo json_encode($user);
	} else {
		echo json_encode(array('message' => 'user does not exist'));
		
	}
		

?>