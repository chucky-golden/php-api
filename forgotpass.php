<?php
	require_once('headers.php');
	require_once('functions.php');
	require_once('processor.php');
	$processes = new Processes();

	$data = json_decode(file_get_contents("php://input"));

	$email = $data->user_email;

	$email = mysql_prep($connect, trimData($email));

	if(email_validate($email) == true){
		echo json_encode(array('message' => 'enter a valid email address'));
	
	}else{ 

		$check = $processes->emailExists($email);
		if($check == 1){
		
			$user = $processes->forgot($email);
			if($user == true) {		
				    
				echo json_encode(array(
				    'message' => 'check your mail',
				    'user_email' => $email
				    ));
					    
				
			}else{
				echo json_encode(array('message' => 'check network connection'));
			}
		}else{
			echo json_encode(array('message' => 'email not found'));
			
		}
	}

?>