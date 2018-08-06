<?php

    
    class UserRepository
    {
        private $db;

        const USERS_TABLE_NAME = "UserTable";
     
        const TABLENAME = "tablename";
        const PRIMARYKEY = "primarykey";
        
        public function __construct()
        {
            $this->db = new DBConnect();
        }
        
        public function GetUserData ($where)
        {
            try
            {
                $this->db->openConnection();
                $this->db->select();
                $this->db->from(self::USERS_TABLE_NAME);
                $this->db->where($where);
                $data = $this->db->query();
                $this->db->closeConnection();
                return $data;
            }catch (Exception $ex){
                $this->db->closeConnection();
            }
        }
        

        public function CreateTable($tableModels)
        {
            foreach($tableModels as $model)
            {       
                $sql = $this->_getCreateTableSql($model);
                $this->db->openConnection();
                try{
                    $this->db->createTable($sql, $model[self::TABLENAME]);
                }catch(Exception $ex){
                    Helper::flog("failed creating table.\n$sql");
                
                }
                $this->db->closeConnection();
            }
            
        }


        private function _getCreateTableSql($tablemodel)
        {
            $newTableName = '';
            $sql_columns = '';
            foreach($tablemodel as $k => $v)
            {
 
                if ($k === self::TABLENAME){
                    $newTableName = $v;
                }else if ($k === self::PRIMARYKEY){
                    $primarykey = $v;
                }else{
                        $sql_columns .= " $k $v,";
                }
                
            }

            $sql_primary_key = "PRIMARY KEY (`$primarykey`)";
            $dbname = "test";

            $sql = "CREATE TABLE `$dbname`.`$newTableName`(	
                        $sql_columns
                        $sql_primary_key
                    );";
            
         

            return $sql;
        }


        
    /*
        private function _insertDummyRecord()
        {
            
            $insertValues = array
            (
                "username" => "test",
                "email" => "test@test.com",
                "password" => "test",
            );
    
    
    
            try{
                $this->db->openConnection();
                $this->db->insert(self::USERS_TABLE_NAME, $insertValues);
                $this->db->query();
                $this->db->closeConnection();
            }catch(Exception $e){
                echo $e->getMessage();
            }
        }
    */
    
    }

?>