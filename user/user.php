<?php

    class User extends UserModel
    {

        public function __construct()
        {
            $this->_setModel();
        }

        public function Login($username, $password)
        {

            $where = array
            (
                "username" =>   $username,
                "password" =>   $password
            );


            $data =  $this->_userRepository->GetUserData($where);
        
            
            if(empty($data)) {
                Helper::flog("No Record found matching that username password combination.");
                $this->_setErrorMessage("No Record found matching that username password combination.");
                return false;
            } else if (count($data) === 1) {
                Helper::flog("Login Success.\n". json_encode($data));
                $this->_setUserData($data[0]);
                return true;
            } else {
                $this->_setErrorMessage("Multiple ids found matching that username password combination.");
                Helper::flog("Multiple ids found matching that username password combination: " . json_encode($data));
                return false;
            }
            

        }
        

        public function GetPropertyValue($propname)
        {
            return $this->_userData[$propname];   
        }


        public function isAuthenticated()
        {
            
        }

        public function GetErrorMessage()
        {
            return $this->_userData['error'];
        }


        private function _setUserData($data)
        {
            $this->_userData['id']      = $data['id'];
            $this->_userData['name']    = $data['username'];
            $this->_userData['email']   = $data['email'];
            
        }
 }