<?php


class User{


    public function register($email,$name,$password,$password2){
        global $db, $email_from, $email_fromname, $main_domain;
        $return_data=array('success'=>false,'errors'=>array());

        if($password!=$password2){
            $return_data['errors'][] = 'passwords_dont_match';
        }else {
            //check that email is available
            $already_registered = $db->load_by('users', 'email', $email);

            if ($already_registered) {
                //email already registered
                $return_data['errors'][] = 'email_regsitered';
            } else {
                //create user
                $user_data_array = array();
                $user_data_array['email'] = $email;
                $user_data_array['name'] = $name;
                $salt = base_convert(mt_rand(0x1D39D3E06400000, 0x41C21CB8E0FFFFFF), 10, 36);;
                $user_data_array['salt'] = $salt;
                $user_data_array['password_hash'] = md5($salt . $password);
                $user_data_array['validated'] = 0;
                $id = $db->insert('users', $user_data_array);
                if (!$id) {
                    //error creating user
                    $return_data['errors'][] = 'insert_error';
                } else {
                    //create validation code, and send validation email
                    $validation_code = bin2hex(random_bytes(32));
                    $validation_code_data_array = array();
                    $validation_code_data_array['user_id'] = $id;
                    $validation_code_data_array['code'] = $validation_code;
                    $id = $db->insert('validation_codes', $validation_code_data_array);
                    ob_start();
                    include(BP . '/include/email/email_validation.php');
                    $email_body = ob_get_contents();
                    ob_end_clean();
                    $subject = 'email validation';
                    include(BP . '/include/class/mail.php');
                    send_mail($email, $name, $email_from, $email_fromname, $subject, $email_body, false);

                    $return_data['success'] = true;
                }
            }
        }

        return $return_data;
    }

    public function validate($code){
        global $db;
        $return_data=array('success'=>false,'errors'=>array());

        //first drop all validation codes older than 24h...
        $query='DELETE FROM validation_codes WHERE created < (NOW() - INTERVAL 24 HOUR)';
        $params=array();
        $db->query($query,$params);

        //then check if this validation code exists
        //if yes, validate and login
        //if not, error
        $validation_code_data_array = $db->load_by('validation_codes','code',$code);
        if(!$validation_code_data_array){
            $return_data['errors'][]='invalid_code';
        }else{

            //update user
            $user_data_array=array();
            $user_data_array['validated']=1;
            $update_results = $db->update('users',$validation_code_data_array['user_id'],$user_data_array);

            //get user
            $user_data_array = $db->load('users',$validation_code_data_array['user_id']);

            if($update_results!=1){
                $return_data['errors'][]='update_error';
            }else{
                $return_data['success']=true;
                $_SESSION['user_id']=$validation_code_data_array['user_id'];
                $_SESSION['name']=$user_data_array['name'];
            }
        }

        return $return_data;
    }

    public function login($email,$password,$remember_me){
        global $db, $main_domain;
        $return_data=array('success'=>false,'errors'=>array());

        //get user
        $user_data_array = $db->load_by('users','email',$email);

        //verify user and password
        if(!$user_data_array || md5($user_data_array['salt'].$password)!=$user_data_array['password_hash']){
            $return_data['errors'][]='invalid_login';
        }else{
            //verify if email is validated
            if($user_data_array['validated']==0){
                $return_data['errors'][]='email_not_validated';
            }else{
                //well thats it user is valid
                $return_data['success']=true;
                $_SESSION['user_id']=$user_data_array['id'];
                $_SESSION['name']=$user_data_array['name'];

                if($remember_me){
                    $token = bin2hex(random_bytes(32));
                    setcookie("remember_me_token", $token, time()+31536000, '/', '.'.$main_domain);

                    $db->delete_by('remember_me_codes','user_id',$user_data_array['id']);
                    $remember_me_code_data_array=array();
                    $remember_me_code_data_array['user_id']=$user_data_array['id'];
                    $remember_me_code_data_array['code']=$token;
                    $db->insert('remember_me_codes',$remember_me_code_data_array);
                }
            }
        }

        return $return_data;
    }

    public function logout(){
        global $main_domain;
        $return_data=array('success'=>false,'errors'=>array());

        unset($_SESSION['user_id']);
        setcookie("remember_me_token", '', time()-3600, '/', '.'.$main_domain);
        $return_data['success']=true;

        return $return_data;
    }

    public function update($user_id,$name){
        global $db;
        $return_data=array('success'=>false,'errors'=>array(),'user_id'=>null);

        $user_data_array=array();
        $user_data_array['name']=$name;
        $update_results = $db->update('users',$user_id,$user_data_array);

        if($update_results!=1){
            $return_data['errors'][]='update_error';
        }else{
            $return_data['success']=true;
            $_SESSION['name']=$user_data_array['name'];
        }

        return $return_data;
    }

    public function send_password_reset_email($email){
        global $db, $email_from, $email_fromname, $main_domain;
        $return_data=array('success'=>false,'errors'=>array());

        //get user
        $user_data_array = $db->load_by('users','email',$email);

        if($user_data_array){

            //create validation code, and send validation email
            $password_reset_code = bin2hex(random_bytes(32));
            $password_reset_code_data_array = array();
            $password_reset_code_data_array['user_id'] = $user_data_array['id'];
            $password_reset_code_data_array['code'] = $password_reset_code;
            $id = $db->insert('password_reset_codes', $password_reset_code_data_array);
            ob_start();
            include(BP . '/include/email/reset_password.php');
            $email_body = ob_get_contents();
            ob_end_clean();
            $subject = 'email validation';
            include(BP . '/include/class/mail.php');
            send_mail($email, $user_data_array['name'], $email_from, $email_fromname, $subject, $email_body, false);

        }

        //always pretend if might have worked..
        $return_data['success'] = true;

        return $return_data;
    }

    public function reset_password($code,$password,$password2){
        global $db;
        $return_data=array('success'=>false,'errors'=>array());

        //first drop all validation codes older than 24h...
        $query='DELETE FROM password_reset_codes WHERE created < (NOW() - INTERVAL 24 HOUR)';
        $params=array();
        $db->query($query,$params);

        if($password!=$password2){
            $return_data['errors'][] = 'passwords_dont_match';
        }else {
            $password_reset_code_data_array = $db->load_by('password_reset_codes','code',$code);
            if(!$password_reset_code_data_array){
                $return_data['errors'][]='invalid_code';
            }else{
                $user_data_array = array();
                $salt = base_convert(mt_rand(0x1D39D3E06400000, 0x41C21CB8E0FFFFFF), 10, 36);;
                $user_data_array['salt'] = $salt;
                $user_data_array['password_hash'] = md5($salt . $password);
                $update_results = $db->update('users',$password_reset_code_data_array['user_id'],$user_data_array);

                if($update_results!=1){
                    $return_data['errors'][]='update_error';
                }else{
                    $return_data['success']=true;
                }
            }
        }


        return $return_data;
    }

    public function change_password($user_id,$old_password,$password,$password2){
        global $db;
        $return_data=array('success'=>false,'errors'=>array());

        if($password!=$password2){
            $return_data['errors'][] = 'passwords_dont_match';
        }else {
            //get user
            $user_data_array = $db->load('users', $user_id);

            if(!$user_data_array || md5($user_data_array['salt'].$old_password)!=$user_data_array['password_hash']){
                $return_data['errors'][]='invalid_login';
            }else{

                $user_data_array=array();
                $salt = base_convert(mt_rand(0x1D39D3E06400000, 0x41C21CB8E0FFFFFF), 10, 36);;
                $user_data_array['salt'] = $salt;
                $user_data_array['password_hash'] = md5($salt . $password);
                $update_results = $db->update('users',$user_id,$user_data_array);

                if($update_results!=1){
                    $return_data['errors'][]='update_error';
                }else {
                    $return_data['success'] = true;
                }
            }
        }

        return $return_data;
    }

    public function autologin(){
        global $db, $main_domain;
        $return_data=array('success'=>false,'errors'=>array());

        if(isset($_COOKIE['remember_me_token'])){
            $remember_me_code_data_array = $db->load_by('remember_me_codes','code',$_COOKIE['remember_me_token']);
            if(!$remember_me_code_data_array){
                $return_data['errors'][]='invalid_code';
            }else{
                //login
                //get user
                $user_data_array = $db->load('users',$remember_me_code_data_array['user_id']);

                $return_data['success']=true;
                $_SESSION['user_id']=$remember_me_code_data_array['user_id'];
                $_SESSION['name']=$user_data_array['name'];

                //rotate token
                $token = bin2hex(random_bytes(32));
                setcookie("remember_me_token", $token, time()+31536000, '/', '.'.$main_domain);
                $db->delete('remember_me_codes',$remember_me_code_data_array['id']);
                $remember_me_code_data_array=array();
                $remember_me_code_data_array['user_id']=$_SESSION['user_id'];
                $remember_me_code_data_array['code']=$token;
                $db->insert('remember_me_codes',$remember_me_code_data_array);
            }
        }
        return $return_data;
    }

}