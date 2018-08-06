<?php


    abstract class UserModel
    {
        // JSON config file constants. Matches keys in json object.
        const   USER_SETTINGS           = "user.settings.json";
        const   DEPENDENCIES            = "Dependencies";
        const   LOG                     = "Log";
        const   DBCONNECT               = "DBConnect";
        const   ENCRYPTOR               = "Encryptor";
        // const   PROPERTIES              = "Properties";s
        // const   USER_MODEL              = 'UserModel';
        // const   TABLE_MODELS            = "TableModels";
        // const   USER_TABLE_MODEL        = 'UserTableModel';
        // const   USER_TOKEN_TABLE_MODEL  = 'UserTokenTableModel';


        // const   TYPE_ARRAY              = "array";
        // const   TYPE_STRING             = "string";
        // const   PRIMARY_KEY_LABEL       = "primarykey";
        // const   TABLENAME_LABEL         = "tablename";


        // class properties that store user-related data.
        private     $_userData; 
        private     $_userRepository; 
        private     $_userSettings;

        // dependecy classes
        private     $_d; 

   


        protected function _setModel()
        {
            $this->_userSettings = $this->_loadUserSettings();
            

            $this->_loadClasses($this->_userSettings[self::DEPENDENCIES]);
            $this->_userRepository = new UserRepository($this->_d[self::DBCONNECT]);

            #$this->_test();
        }

        protected function _registerUser($data)
        {
            $this->_userRepository->RegisterUser($data);
        }
        

        protected function _getClass($className)
        {
            return $this->_d[$className];
        }

        protected function _setErrorMessage($s)
        {
            $this->_userData['error'] = $s;
        }

        protected function _getErrorMessage()
        {
            return $this->_userData['error'] ;
        }

        protected function _setUserData($data)
        {
            $this->_userData['id']      = $data['id'];
            $this->_userData['name']    = $data['username'];
            $this->_userData['email']   = $data['email'];
            
        }

        protected function _getUserData($where)
        {
            return $this->_userRepository->GetUserData($where);
        }

        protected function _getPropertyValue($propname)
        {
            return $this->_userData[$propname];
        }


        protected function _getHashedPassword($data)
        {
            
            if(!array_key_exists('password', $data))
            {
                $this->_setErrorMessage('missing password');
                return false;
            }else{
                return Hasher::hp($data['password']);
            }
        }

        protected function _validateSignupParams($data)
        {
            $mandatory_fields = array ("username", "password", "email");
            
            foreach($mandatory_fields as $field)
            {
                
                if(!array_key_exists($field, $data))
                {
                    Log::slog("no $field provided.");
                    Log::flog("no $field provided." . json_encode($data));
                    $this->_setErrorMessage("no $field provided.");
                    return false;
                }
            }

            $where = array
            (
                "username" =>   $data['username'],
            );

            Log::slog($data['username']);

            Log::pre(count($this->_getUserData($where)));

            if($this->_getUserData($where))
            {
                #Log::slog("username already exists");
                Log::flog("username already exists." . json_encode($data));
                $this->_setErrorMessage("username already exists.");
                return false;
            }


            return true;
            
        }

        private function _loadClasses($dependencies)
        {
            foreach ($dependencies as $data)
            {
                foreach($data as $name => $path)
                {
                    $path =__DIR__ . "$path";
                    require_once($path);
                    $this->_d[$name] = new $name();
                }
                
            }
        }


        private function _loadUserSettings()
        {
            try{
                $str = file_get_contents(__DIR__ . '\\' . self::USER_SETTINGS);
                return json_decode($str, true);
            }catch (Exception $ex){
                return false;
            }
        }

        private function _test()
        {
            if(array_key_exists(self::LOG, $this->_d))
            {
                #$this->_d['Log']::pre($this->_d); 
                Log::pre($this->_d);               
            }

        }

    }
?>