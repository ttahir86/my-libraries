<?php


    abstract class UserModel
    {
        // JSON config file constants. Matches keys in json object.
        const   USER_MODEL_CONFIG       = "./user/user.config.json";
        const   PROPERTIES              = "Properties";
        const   USER_MODEL              = 'UserModel';
        const   TABLE_MODELS            = "TableModels";
        const   USER_TABLE_MODEL        = 'UserTableModel';
        const   USER_TOKEN_TABLE_MODEL  = 'UserTokenTableModel';


        const   TYPE_ARRAY              = "array";
        const   TYPE_STRING             = "string";
        const   PRIMARY_KEY_LABEL       = "primarykey";
        const   TABLENAME_LABEL         = "tablename";


        // class properties that store user-related data.
        protected $_userData; 
        protected $_userTable;
        protected $_userTokenTable;
        protected $_userRepository; 

   
        protected function _setModel()
        {
            $this->_userRepository = new UserRepository();
            $this->_buildUserModel();
        }
        
        private function _buildUserModel()
        {
            $userDefinition         = $this->_loadUserModelDefinition();

            if(array_key_exists(self::PROPERTIES, $userDefinition)){
                $this->_userData        = $this->_createAndSaveProperties($userDefinition[self::PROPERTIES]);
            }
            
            if(array_key_exists(self::TABLE_MODELS, $userDefinition)){

                if (array_key_exists(self::USER_TABLE_MODEL, $userDefinition[self::TABLE_MODELS])){
                    $this->_userTable = $this->_getTableModel($userDefinition[self::TABLE_MODELS][self::USER_TABLE_MODEL]);
                }

                if (array_key_exists(self::USER_TOKEN_TABLE_MODEL, $userDefinition[self::TABLE_MODELS])){
                    $this->_userTokenTable  = $this->_getTableModel($userDefinition[self::TABLE_MODELS][self::USER_TOKEN_TABLE_MODEL]);
                }

                $this->_userRepository->CreateTable($userDefinition[self::TABLE_MODELS]);
            }
            
            
        }
        
        private function _loadUserModelDefinition()
        {
            $str = file_get_contents(self::USER_MODEL_CONFIG);
            return json_decode($str, true);
        }

        private function _createAndSaveProperties($userModelDefnition)
        {
            $data = array();
            foreach($userModelDefnition[self::USER_MODEL] as $propName => $propType)
            {
                switch ($propType) 
                {
                    case self::TYPE_ARRAY:
                        $data[$propName] = array();
                        break;
                    case self::TYPE_STRING:
                        $data[$propName] = "";
                        break;
                    default:
                        $data[$propName] = $propType;
                }
            }

            return $data;
        }

        private function _getTableModel($tableModel)
        {
            $data       = array();
            $columns    = array();
            foreach($tableModel as $columnName => $columnDefinition)
            {
                if ($columnName === self::PRIMARY_KEY_LABEL || $columnName === self::TABLENAME_LABEL){
                    $data[$columnName] = $columnDefinition;
                }else{
                    $columns[] = $columnName;
                }
            }
            $data["columns"] = $columns;

            return $data;

        }

       
        protected function _setErrorMessage($msg)
        {
            $this->_userData['error'] = $msg;
        }

        

    }
?>