<?php

    class User extends UserModel
    {

        public function __construct()
        {
           $this->_setModel();
        }

        public function Login($username, $password)
        {
            $password = Hasher::hp($password);
            $where = array
            (
                "username" =>   $username,
                "password" =>   $password
            );


            $data =  $this->_getUserData($where);
        
            
            if(empty($data)) {
                Log::flog("No Record found matching that username password combination.");
                $this->_setErrorMessage("No Record found matching that username password combination.");
                return false;
            } else if (count($data) === 1) {
                Log::flog("Login Success.\n". json_encode($data));
                $this->_setUserData($data[0]);
                return true;
            } else {
                $this->_setErrorMessage("Multiple ids found matching that username password combination.");
                Log::flog("Multiple ids found matching that username password combination: " . json_encode($data));
                return false;
            }
            

        }

        
        public function SignUp($data = array())
        {
            $hashed_password = $this->_getHashedPassword($data);
            if(!$hashed_password)
            {
                Log::flog("could not hash password string." . json_encode($data));
                return false;
            }
            $data['password'] = $hashed_password;

           if($this->_validateSignupParams($data))
           {
               $userData = $this->_registerUser($data);
               
               $this->Login($data['username'], $data['password']);
               Log::pre($this->_getUserData());
           }

            $userData = $this->_getUserData(array("username" =>$data['username'], "password"=>$data['password']))[0];
            unset($userData['password']);
            Log::pre($userData);


                        
        }
        

        public function GetPropertyValue($propname)
        {
            return $this->_getPropertyValue($propname);   
        }

        public function GetErrorMessage()
        {
            return $this->_getErrorMessage();
        }


        public function isAuthenticated()
        {
            
        }
        
 }