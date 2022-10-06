<?php
	require_once('headers.php');
	require_once('functions.php');
	require_once('processor.php');
	$processes = new Processes();

	$data = json_decode(file_get_contents("php://input"));

	$email = $data->email;
	$password = $data->password;
    
    // $otp = "";

    // for($i=0; $i < 5; $i++) { 
    //      $otp .= $num[$i] = rand(0, 9);
    // }
    
	$email = mysql_prep($connect, trimData($email));
	$password = mysql_prep($connect, trimData($password));

	if(email_validate($email) == true){
		echo json_encode(array('message' => 'enter a valid email address'));
		
	}else{
		$new_pass = password_encrypt($password);
		$user = $processes->loginUsers($email, $new_pass);
		if($user != false) {
			echo json_encode($user);
		} else {
			echo json_encode(array('message' => 'user does not exist'));
			
		}
	}
		

?>