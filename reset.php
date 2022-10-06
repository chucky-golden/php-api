<?php
	require_once('headers.php');
	require_once('functions.php');
	require_once('processor.php');
	$processes = new Processes();

	$data = json_decode(file_get_contents("php://input"));

	$email = $data->user_email;
	$password = $data->password;

	$password = mysql_prep($connect, trimData($password));
	$new_pass = password_encrypt($password);
	
	$user = $processes->reset($email, $new_pass);
	if($user == true) {		
		    
		echo json_encode(array(
		    'message' => 'password changed',
		    'user_email' => $email
		    ));
			    
		
	}else{
		echo json_encode(array('message' => 'check network connection'));
	}
	

?>