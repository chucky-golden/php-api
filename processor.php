<?php
    // import library for reading .env files
    require_once('vendor/autoload.php');
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();


    class Processes{

        private $host  = 'localhost';
        private $user  = 'root';
        private $password   = "";
        private $database  = "database";     
        public $dbConnect = false;
        private $users = 'users';
        private $contact = 'contact';

        public function __construct(){
            if(!$this->dbConnect){ 
                $conn = new mysqli($this->host, $this->user, $this->password, $this->database);
                if($conn->connect_error){
                    die("Error failed to connect to MySQL: " . $conn->connect_error);
                }else{
                    $this->dbConnect = $conn;
                }
            }
        }


        private function getData($sqlQuery) {
            $result = mysqli_query($this->dbConnect, $sqlQuery);
            if(!$result){
                die('Error in query: '. mysqli_error());
            }
            $data= array();
            while ($row = mysqli_fetch_assoc($result)) {
                $data[]=$row;            
            }
            return $data;
        }


        private function getNumRows($sqlQuery) {
            $result = mysqli_query($this->dbConnect, $sqlQuery);
            if(!$result){
                die('Error in query: '. mysqli_error());
            }
            $numRows = mysqli_num_rows($result);
            return $numRows;
        }


        public function loginUsers($email, $password){
            $sqlQuery = "
                SELECT id, email 
                FROM ".$this->users." 
                WHERE email='".$email."' AND password='".$password."'";
            $num = $this->getNumRows($sqlQuery);

            if($num > 0){
                $check = $this->getData($sqlQuery);
                $verified = $check[0]['verified'];
                $userid = $check[0]['id'];
                    
                if($verified == 0){    
                    return array(
                        'message' => 'operation successful',
                        'email' => $email,
                        'user_id' => $userid
                    );

                }else{
                    return array(
                        'message' => 'user has not verified email',
                        'email' => $email,
                        'user_id' => $userid
                    );    
                }
            }else{
                return false;
            }
        }

        public function gmailloginUsers($email){
            $sqlQuery = "
                SELECT id, email 
                FROM ".$this->users." 
                WHERE email='".$email."'";
            $num = $this->getNumRows($sqlQuery);

            if($num > 0){
                $check = $this->getData($sqlQuery);
                $verified = $check[0]['verified'];
                $userid = $check[0]['id'];
                    
                if($verified == 0){    
                    return array(
                        'message' => 'operation successful',
                        'email' => $email,
                        'user_id' => $userid
                    );

                }else{
                    return array(
                        'message' => 'user has not verified email',
                        'email' => $email,
                        'user_id' => $userid
                    );    
                }
            }else{
                return false;
            }
        }


        public function emailExists($email) {
            $sqlQuery = "
                SELECT * FROM ".$this->users." 
                WHERE email='".$email."' "; 
            $numRows = $this->getNumRows($sqlQuery);
            return $numRows;
        }
        
        
        public function phoneExists($phone) {
            $sqlQuery = "
                SELECT * FROM ".$this->users." 
                WHERE phone='".$phone."' "; 
            $numRows = $this->getNumRows($sqlQuery);
            return $numRows;
        }
        


        public function registerUser($first_name, $last_name, $email, $new_pass, $phone, $role, $gender, $country, $otp, $main_date) {
            $sqlInsert = "
                INSERT INTO ".$this->users." 
                (firstname, lastname, email, password, phone, gender, country, role, otp, createddate) 
                VALUES ('".$first_name."', '".$last_name."', '".$email."', '".$new_pass."', '".$phone."', '".$gender."', '".$country."', '".$role."', '".$otp."', '".$main_date."')";
            $result = mysqli_query($this->dbConnect, $sqlInsert);
            if(!$result){
                return ('Error in query: '. mysqli_error());
            } else { 
                return  true;  
            }
        }
        
        
        public function verifyemail($email, $otp) {
            $sqlQuery = "
                SELECT otp 
                FROM ".$this->users." 
                WHERE email='".$email."'";
            
            $check = $this->getData($sqlQuery);
            $otpdatabase = $check[0]['otp'];

            if($otpdatabase == $otp){
                $sqlUserUpdate = "
                    UPDATE ".$this->users." 
                    SET verified = 0 
                    WHERE email = '".$email."'";          
                $result = mysqli_query($this->dbConnect, $sqlUserUpdate);
                if(!$result){
                    return 'errors';
                }else{ 
                    return true;  
                }
            }else{
                return false;
            }            
            
        }


        public function reset($email, $new_pass) {            
            $sqlUserUpdate = "
                UPDATE ".$this->users." 
                SET password = '".$new_pass."' 
                WHERE email = '".$email."'";          
            $result = mysqli_query($this->dbConnect, $sqlUserUpdate);
            if(!$result){
                return 'errors';
            }else{ 
                return true;  
            }           
            
        }


        public function forgot($email){
            $to = $email;
            $subject = "Password Recovery";
            
            $mailcontent = '
            <div class="container">
                <div class="row">
                    <div>
                        <h3 class="text-center">BedRock Trades</h3>
                        <p>we got a request to reset your 
                        password, if this was you, click the link below <br><b><i><a href="https://www.bedrock.trade/passwordreset?email='.$email.'">Password reset</a></i></b> to reset password or ignore and nothing will happen to your account.</p>

                    </div>
                </div>
            </div>';
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            
            $headers .= 'From: <support@bedrock.trade>' . "\r\n";
            
            $sent = mail($to,$subject,$mailcontent,$headers);
            if($sent){
                return true;
            } else {
                return false;    
            }            
        }
        

        public function sendmail($email, $otp) {
            $sqlUserUpdate = "
                UPDATE ".$this->users." 
                SET otp = '".$otp."'
                WHERE email = '".$email."'";          
            $res = mysqli_query($this->dbConnect, $sqlUserUpdate);
            if($res){ 
                $to = $email;
                $subject = "Email Verification";
                
                $mailcontent = '
                <div class="container">
                    <div class="row">
                        <div>
                            <h3 class="text-center">My Mail</h3>
                            <p>This email was sent to you due to a registration request made on our website. Enter the OTP below to have access to your account <br><br>'.$otp.'</p>
                        </div>
                    </div>
                </div>';
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                
                $headers .= 'From: <support@mysite.com>' . "\r\n";
                
                $sent = mail($to,$subject,$mailcontent,$headers);
                if($sent){
                    return true;
                } else {
                    return 'Check network connection';    
                }
            }

        }


        public function sendcontact($email, $fullname, $message, $main_date) {
            $sqlInsert = "
                INSERT INTO ".$this->contact." 
                (email, fullname, message, createddate) 
                VALUES ('".$email."', '".$fullname."', '".$message."', '".$main_date."')";
            $result = mysqli_query($this->dbConnect, $sqlInsert);
            if(!$result){
                return false;
            } else { 
                return  true;  
            }
        }
        
        
        public function phonesms($phone, $otp) {
            require('twilio-php-main/src/Twilio/autoload.php');
            
            
            $account_sid = $_ENV['ACCOUNT_SID'];
            $auth_token = $_ENV['AUTH_TOKEN'];
            
            $twilio_number = $_ENV['TPHONE'];
            $client = new Twilio\Rest\Client($account_sid, $auth_token);
            
            $client->messages->create(
            // Where to send a text message (your cell phone?)
                $phone,
                array(
                    'from' => $twilio_number,
                    'body' => $otp
                )
            );
            
            return true;

        }


    }

?>