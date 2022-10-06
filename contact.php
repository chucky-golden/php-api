<?php
	session_start();
	require_once('functions.php');
	require_once('processor.php');
	$processes = new Processes();

	if (isset($_POST['submit'])) {
		$check = checkempty($_POST, 'submit');

		if($check == ''){

			$sanitizer = sanitizer($_POST);

			$email = mysql_prep($connect, trimData($sanitizer['email']));
			$fullname = mysql_prep($connect, trimData($sanitizer['fullname']));
			$message = mysql_prep($connect, trimData($sanitizer['message']));

			if(email_validate($email) == true){
				$error = "please enter a correct email format";
				header("Location: ../register?error=".$error);
				return false;
			}else{
				
				$main_date = date("Y-m-d");
				$sent = $processes->sendcontact($email, $fullname, $message, $main_date);
				
				if($sent == true){
				    $error = "we will get back to you shortly";
					header("Location: ../contact?error=".$error);
					return false;
				}else{
					$error = "error sending message";
					header("Location: ../contact?error=".$error);
					return false;
				}
				
				
			}

		}else{
			$error = urlencode($check);
			header('Location: ../index.php?error='.$error);
			return false;
		}

	}else{
		$error = urlencode("you must log in first");
		header('Location: ../index.php?error='.$error);
		return false;
	}





?>