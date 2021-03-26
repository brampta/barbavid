<?php


class User{


    public function register($email,$name,$password,$password2){
        global $db, $message;
        $return_data=array('success'=>false,'errors'=>array());

        if(
            !$this->validate_value('email',$email)
            || !$this->validate_value('name',$name)
            || !$this->validate_value('password',$password)
        ){
            $return_data['errors'][] = 'validation_error';
        }else {
            if ($password != $password2) {
                $return_data['errors'][] = 'passwords_dont_match';
                $message->add_message('error', __('passwords don\'t match'));
            } else {
                //check that email is available
                $already_registered = $db->load_by('users', 'email', $email);

                if ($already_registered) {
                    //email already registered
                    $return_data['errors'][] = 'email_regsitered';
                    $message->add_message('error', __('email already registered'));
                } else {
                    //get free user hash
                    /*
                    $found_hash='';
                    $countturns=0; $maxturns=100;
                    while($found_hash=='' && $countturns<$maxturns){
                        $countturns++;
                        $arandomhash=base_convert(mt_rand(0x1D39D3E06400000, 0x41C21CB8E0FFFFFF), 10, 36);
                        $upload_info_for_hashgive = $db->load_by('users','hash',$arandomhash);
                        if(!$upload_info_for_hashgive){
                            $found_hash = $arandomhash;
                        }
                    }
                    */
                    $table_for_hash='users';
                    include(BP.'/include/procedure/create_unique_hash.php');

                    //create user
                    $user_data_array = array();
                    $user_data_array['hash'] = $found_hash;
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
                        $message->add_message('error', __('database error'));
                    } else {
                        /*
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
                        include(BP . '/include/function/mail.php');
                        send_mail($email, $name, $email_from, $email_fromname, $subject, $email_body, false);
                        */
                        $this->do_send_validation_email($id, $email, $name);

                        $return_data['success'] = true;
                        $message->add_message('success', __('successfully registered'));
                        $message->add_message('success', __('a confirmation email has been sent'));
                    }
                }
            }
        }

        return $return_data;
    }

    public function validate_email($code){
        global $db, $message;
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
            $message->add_message('error',__('invalid code'));
        }else{

            //update user
            $user_data_array=array();
            $user_data_array['validated']=1;
            $update_results = $db->update('users',$validation_code_data_array['user_id'],$user_data_array);

            //get user
            $user_data_array = $db->load('users',$validation_code_data_array['user_id']);

            if($update_results!=1){
                $return_data['errors'][]='update_error';
                $message->add_message('error',__('error updating the database'));
            }else{
                $return_data['success']=true;
                $message->add_message('success',__('successfully validated email'));
                //$_SESSION['user_id']=$validation_code_data_array['user_id'];
                //$_SESSION['name']=$user_data_array['name'];
                $this->set_user_session($user_data_array);
            }
        }

        return $return_data;
    }

    public function login($email,$password,$remember_me){
        global $db, $main_domain, $message;
        $return_data=array('success'=>false,'errors'=>array());

        //get user
        $user_data_array = $db->load_by('users','email',$email);

        //verify user and password
        if(!$user_data_array || md5($user_data_array['salt'].$password)!=$user_data_array['password_hash']){
            $return_data['errors'][]='invalid_login';
            $message->add_message('error',__('invalid login information'));
        }else{
            //verify if email is validated
            if($user_data_array['validated']==0){
                $return_data['errors'][]='email_not_validated';
                $message->add_message('error','<a href="/user/resend_validaton">'.__('click here to resend the validation email').'</a>');
            }else{
                //well thats it user is valid
                $return_data['success']=true;
                $message->add_message('success',__('successfully logged in'));
                //$_SESSION['user_id']=$user_data_array['id'];
                //$_SESSION['name']=$user_data_array['name'];
                $this->set_user_session($user_data_array);

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
        global $main_domain, $message;
        $return_data=array('success'=>false,'errors'=>array());

        //unset($_SESSION['user_id']);
        session_unset();

        setcookie("remember_me_token", '', time()-3600, '/', '.'.$main_domain);
        $return_data['success']=true;
        $message->add_message('success',__('successfully logged out'));

        return $return_data;
    }

    public function update($user_id,$name){
        global $db, $message;
        $return_data=array('success'=>false,'errors'=>array(),'user_id'=>null);

        if(!$this->validate_value('name',$name)){
            $return_data['errors'][] = 'validation_error';
        }else {
            $user_data_array = array();
            $user_data_array['name'] = $name;
            $update_results = $db->update('users', $user_id, $user_data_array);

            if ($update_results != 1) {
                $return_data['errors'][] = 'update_error';
                $message->add_message('error', __('error updating the database'));
            } else {
                $return_data['success'] = true;
                $message->add_message('success', __('successfully udpated user'));
                //$_SESSION['name'] = $user_data_array['name'];
                $this->set_user_session($user_data_array);
            }
        }

        return $return_data;
    }

    public function send_password_reset_email($email){
        global $db, $email_from, $email_fromname, $main_domain, $message;
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
            include(BP . '/include/function/mail.php');
            send_mail($email, $user_data_array['name'], $email_from, $email_fromname, $subject, $email_body, false);

        }

        //always pretend if might have worked..
        $return_data['success'] = true;
        $message->add_message('success',__('if this email exists in the database, you will receive an email with a link to reset your password.'));

        return $return_data;
    }

    public function send_validation_email($email){
        global $db, $email_from, $email_fromname, $main_domain, $message;
        $return_data=array('success'=>false,'errors'=>array());

        //get user
        $user_data_array = $db->load_by('users','email',$email);
        if($user_data_array){
            $this->do_send_validation_email($user_data_array['id'],$email,$user_data_array['name']);
        }

        //always pretend if might have worked..
        $return_data['success'] = true;
        $message->add_message('success',__('if this email exists in the database, you will receive an email with a link to validate this email adress.'));

        return $return_data;
    }

    public function reset_password($code,$password,$password2){
        global $db, $message;
        $return_data=array('success'=>false,'errors'=>array());

        //first drop all validation codes older than 24h...
        $query='DELETE FROM password_reset_codes WHERE created < (NOW() - INTERVAL 24 HOUR)';
        $params=array();
        $db->query($query,$params);

        if(!$this->validate_value('password',$password)){
            $return_data['errors'][] = 'validation_error';
        }else{
            if ($password != $password2) {
                $return_data['errors'][] = 'passwords_dont_match';
                $message->add_message('error', __('passwords don\'t match'));
            } else {
                $password_reset_code_data_array = $db->load_by('password_reset_codes', 'code', $code);
                if (!$password_reset_code_data_array) {
                    $return_data['errors'][] = 'invalid_code';
                    $message->add_message('error', __('invalid code'));
                } else {
                    $user_data_array = array();
                    $salt = base_convert(mt_rand(0x1D39D3E06400000, 0x41C21CB8E0FFFFFF), 10, 36);;
                    $user_data_array['salt'] = $salt;
                    $user_data_array['password_hash'] = md5($salt . $password);
                    $update_results = $db->update(
                        'users',
                        $password_reset_code_data_array['user_id'],
                        $user_data_array
                    );

                    if ($update_results != 1) {
                        $return_data['errors'][] = 'update_error';
                        $message->add_message('error', __('error updating the database'));
                    } else {
                        $return_data['success'] = true;
                        $message->add_message('success', __('password successfully reset'));
                    }
                }
            }
        }

        return $return_data;
    }

    public function change_password($user_id,$old_password,$password,$password2){
        global $db, $message;
        $return_data=array('success'=>false,'errors'=>array());

        if(!$this->validate_value('password',$password)){
            $return_data['errors'][] = 'validation_error';
        }else{
            if($password!=$password2){
                $return_data['errors'][] = 'passwords_dont_match';
                $message->add_message('error',__('passwords don\'t match'));
            }else {
                //get user
                $user_data_array = $db->load('users', $user_id);

                if(!$user_data_array || md5($user_data_array['salt'].$old_password)!=$user_data_array['password_hash']){
                    $return_data['errors'][]='invalid_login';
                    $message->add_message('error',__('old password is incorrect'));
                }else{

                    $user_data_array=array();
                    $salt = base_convert(mt_rand(0x1D39D3E06400000, 0x41C21CB8E0FFFFFF), 10, 36);;
                    $user_data_array['salt'] = $salt;
                    $user_data_array['password_hash'] = md5($salt . $password);
                    $update_results = $db->update('users',$user_id,$user_data_array);

                    if($update_results!=1){
                        $return_data['errors'][]='update_error';
                        $message->add_message('error',__('error updating the database'));
                    }else {
                        $return_data['success'] = true;
                        $message->add_message('success',__('successfully changed password'));
                    }
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
                //$_SESSION['user_id']=$remember_me_code_data_array['user_id'];
                //$_SESSION['name']=$user_data_array['name'];
                $this->set_user_session($user_data_array);

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

    private function do_send_validation_email($user_id,$email,$name){
        global $db, $email_from, $email_fromname, $main_domain;

        //create validation code, and send validation email
        $validation_code = bin2hex(random_bytes(32));
        $validation_code_data_array = array();
        $validation_code_data_array['user_id'] = $user_id;
        $validation_code_data_array['code'] = $validation_code;
        $id = $db->insert('validation_codes', $validation_code_data_array);
        ob_start();
        include(BP . '/include/email/user/email_validation.php');
        $email_body = ob_get_contents();
        ob_end_clean();
        $subject = __('email validation');
        include(BP . '/include/function/mail.php');
        send_mail($email, $name, $email_from, $email_fromname, $subject, $email_body, false);
    }

    private function validate_value($type,$value){
        global $message;
        var_dump($type,$value);

        if($type=='name'){
            $minlen=2;
            if(mb_strlen($value)<$minlen){
                $message->add_message('error',__('username needs to be %1 characters or more.',$minlen));
                return false;
            }
            return true;
        }else if($type=='password'){
            $minlen=8;
            if(mb_strlen($value)<$minlen){
                $message->add_message('error',__('password needs to be %1 characters or more.',$minlen));
                return false;
            }
            return true;
        }else if($type=='email'){
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)){
                $message->add_message('error',__('email address is invalid.'));
                return false;
            }
            return true;
        }else{
            $message->add_message('error',__('unknown validation type "%1".',$type));
            return false;
        }
    }

    private function set_user_session($user_data_array){
        global $admin_ids;

        $_SESSION['user_id']=$user_data_array['id'];
        $_SESSION['name']=$user_data_array['name'];
        if(isset($admin_ids[$user_data_array['id']])){
            $_SESSION['mod_level']=$admin_ids[$user_data_array['id']];
        }
    }

}