<?php
	require_once('headers.php');
	require_once('functions.php');
	require_once('processor.php');
	$processes = new Processes();

	$data = json_decode(file_get_contents("php://input"));

    $email = $data->email;
	$first_name = $data->first_Name;
	$last_name = $data->last_Name;
	$gender = $data->gender;
	$country = $data->country;
	$password = $data->password;
	$phone = $data->phone;
	$role = $data->role;
	
// 	$email = $_POST['email'];
// 	$first_name = $_POST['first_Name'];
// 	$last_name = $_POST['last_Name'];
// 	$password = $_POST['password'];
// 	$phone = $_POST['phone'];
// 	$role = $_POST['role'];
	

	$email = mysql_prep($connect, trimData($email));
	$first_name = mysql_prep($connect, trimData($first_name));
	$last_name = mysql_prep($connect, trimData($last_name));
	$role = mysql_prep($connect, trimData($role));
	$password = mysql_prep($connect, trimData($password));
	$phone = mysql_prep($connect, trimData($phone));
	$gender = mysql_prep($connect, trimData($gender));
	$country = mysql_prep($connect, trimData($country));
	
	$otp = "";
    for ($i=0; $i <= 5; $i++) { 
        $otp .= $num[$i] = rand(0, 9);
    }

	if(email_validate($email) == true){
		echo json_encode(array('message' => 'enter a valid email address'));
	
	}else{ 

		$check = $processes->emailExists($email);
		if($check == 1){
			echo json_encode(array('message' => 'user with email address already exists'));
		}else{
		    
		    $check2 = $processes->phoneExists($phone);
		    if($check2 == 1){
    			echo json_encode(array('message' => 'user with phone number already exists'));
    		}else{
		    
    			$new_pass = password_encrypt($password);
    			$main_date = date("Y-m-d");
    			$user = $processes->registerUser($first_name, $last_name, $email, $new_pass, $phone, $role, $gender, $country, $otp, $main_date);
    			if($user == true) {
    				$sent = $processes->sendmail($email, $otp);
    				if($sent == true){
    				    
    					echo json_encode(array(
    					    'message' => 'account created',
    					    'user_email' => $email,
    					    'phone' => $phone,
    					    'otp' => $otp
    					    ));
    					    
    				}else{
    					echo json_encode(array('message' => 'error creating account'));
    				}
    			}else{
    				echo json_encode(array('message' => 'error registering details'));
    				
    			}
    		}
			
		}
	}	

	





?>