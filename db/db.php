
<?php

    class DBConnect extends DBSettings{
        
        
        private $conn;

        # select
        private $TableName;
        private $SelectedColumns;
        private $WhereConditions;
        private $InConditions;

        # insert
        private $InsertTableName;
        private $InsertValues;

        private $SelectedQueryType = self::SELECT;
        


        /*
            Open and close connection to DB
        */
        public function openConnection(){
            $settings = self::getSettings();
            $host = $settings['host'];
            $dbname = $settings['database'];
            $user = $settings['username'];
            $pass = $settings['password'];
            $this->conn = null;
            try 
            {
                $this->conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch(PDOException $e)
            {
                Helper::flog("Error - Connection Failed: Line ". $e->getLine() . " in File ". __FILE__ . " ". $e->getMessage() );
                die;
            }
        }

        public function closeConnection(){
            $this->conn = null;
        }


        /*
            Main
        */
        public function query(){

            switch ($this->SelectedQueryType) {
                case self::SELECT:
                    $sql = $this->_getSelectSQL();
                    Helper::flog($sql);
                    $data = $this->_executeSelectQuery($sql);
                    Helper::flog($data);
                    return $data;
                case self::INSERT:
                    try{
                        $sql = $this->_getInsertSQL();
                        $this->_executeInsertQuery($sql);
                    }catch(Exception $e){
                        $line = $e->getLine();
                        Helper::flog("Error - Line  $line in File ". __FILE__ . " " .  $e->getMessage());
                    }
                    break;

            }
        }



        /*
            SELECT functions for mysql
        */
        public function select($selectedColumns = array()){
            $this->SelectedQueryType = self::SELECT;
            $this->SelectedColumns = $selectedColumns;
        }

        public function from($tableName){
            $this->TableName = $tableName;
        }

        public function where($whereConditions = array()){
            $this->WhereConditions = $whereConditions;
        }

        public function in($inConditions = array()){
            $this->InConditions = $inConditions;
        }

        private function _getSelectSQL(){
            # get table name
            $tableName = Helper::removeWhiteSpace($this->TableName);
            if(empty($tableName)){
                throw new InvalidArgumentException(SELF::DB_ERROR_TABLE_DOES_NOT_EXIST. " in ".__FUNCTION__ ."(): '$tableName'". var_dump($tableName));
                die;
            }

            # get columns to return
            $final_selected_columns = $this->_getSelectedColumns($this->SelectedColumns);

            # get where condition
            $final_where_conditions = $this->_getWhereConditions($this->WhereConditions);
            
            
            # get in condition
            $isWhereConditionEmpty = Helper::removeWhiteSpace($final_where_conditions);
            $final_in_conditions = $this->_getInConditions($this->InConditions, empty($isWhereConditionEmpty));

            return "SELECT $final_selected_columns FROM $tableName $final_where_conditions $final_in_conditions;";
     
        }

        private function _executeSelectQuery($sql){
            try 
            {
                $stmt = $this->conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
              
            }
            catch(PDOException $e)
            {
               Helper::flog("Error - Line ". $e->getLine() . " in File ". __FILE__ . " " . $e->getMessage());
               die;
            }
            return $stmt->fetchAll();
        }

        private function _getSelectedColumns($sc){
            if (empty($sc)){
                $sc = "*";
            }else if (!is_array($sc)) {
                throw new InvalidArgumentException(SELF::DB_ERROR_PARAM_MUST_BE_ARRAY. " in ".__FUNCTION__ ."(): '$sc'". var_dump($sc));
                die;
            }else{
                $sc = implode(", ", $sc);
            }
            $final_selected_columns = str_replace(",", "", $sc);

            if (empty($final_selected_columns) ){
                $final_selected_columns = "*";
            }else{
                $final_selected_columns = $sc;
            }

            return $final_selected_columns;

        }

        private function _getWhereConditions($wc){
            $final_where_conditions = "";
            if (empty($wc)){
                $final_where_conditions = " ";
            }else if (!is_array($wc)) {
                throw new InvalidArgumentException(SELF:: DB_ERROR_PARAM_MUST_BE_ARRAY. " in ".__FUNCTION__ ."(): '$wc'". var_dump($wc));
                die;
            }else{
                
                $bFirst = true;
                foreach($wc as $k=>$v){
                    if ($bFirst){
                        $bFirst = false;
                        $final_where_conditions = " WHERE $k='$v'";
                    }else{
                        $final_where_conditions .= " AND $k='$v'";
                    }
                    
                }
            }
            
            return $final_where_conditions;
        }

        private function _getInConditions($ic, $isWhereConditionEmpty){
            $final_in_conditions = "";
            if (empty($ic)){
                $final_in_conditions = " ";
            }else if (!is_array($ic)) {
                throw new InvalidArgumentException(SELF:: DB_ERROR_PARAM_MUST_BE_ARRAY. " in ".__FUNCTION__ ."(): '$ic'". var_dump($ic));
                die;
            }else{
                $bFirst = true;
                foreach($ic as $k=>$v){
                    if ((bool)$isWhereConditionEmpty === true){
                        $csvValue = implode(", ", $v);
                        $final_in_conditions = " WHERE $k IN ($csvValue)";
                        $isWhereConditionEmpty = true;
                    }else{
                        $csvValue = implode(", ", $v);
                        $final_in_conditions .= " AND  $k IN ($csvValue)";
                    } 
                }
            }

            return $final_in_conditions;
        }

    

        /* 
            INSERT functions for mysql
        */
        public function insert($tableName = "", $insertValues = array()){
            $this->SelectedQueryType = self::INSERT;

            $tn = Helper::removeWhiteSpace($tableName);

            Helper::checkIfEmpty($tableName, "Error: Table name is empty.");
            Helper::checkIfEmpty($insertValues, "Error: Insert Values are empty.");

            $this->InsertTableName = $tableName;
            $this->InsertValues = $insertValues;
        }

        private function _getInsertSQL(){
            $insertValues = $this->InsertTableName;
            $insertValues = $this->InsertValues;
            if(!is_array($insertValues)){
                Helper::flog(SELF::DB_ERROR_INSERT_VALUES_NOT_ARRAY. " in ".__FUNCTION__ ."(): '$insertValues'");
                throw new InvalidArgumentException(SELF::DB_ERROR_INSERT_VALUES_NOT_ARRAY. " in ".__FUNCTION__ ."(): '$temp'". var_dump($insertValues));
                die;
            }else if (is_array($insertValues)){
               
                $temp = implode("", $insertValues);
                $temp = Helper::removeWhiteSpace($temp);
                if (empty($temp)){
                    Helper::flog(SELF::DB_ERROR_INSERT_VALUES_NOT_ARRAY. " in ".__FUNCTION__ ."(): '$temp'");
                    throw new InvalidArgumentException(SELF::DB_ERROR_INSERT_VALUES_NOT_ARRAY. " in ".__FUNCTION__ ."(): '$temp'". var_dump($insertValues));
                    die;
                }
            }

            $columnNames = "";
            $bFirst = True;
            $columnValues = "";
            foreach($insertValues as $k => $v){
                if ($bFirst){
                    $bFirst = false;
                    $columnNames = $k;
                    $columnValues = "'$v'";
                }else{
                    $columnNames.= ",". $k;
                    $columnValues.= ", '$v'";
                }
            }

            $columnNames = "($columnNames)";
            $columnValues = "($columnValues)";
    
            $sql = "INSERT INTO $this->InsertTableName $columnNames VALUES $columnValues";
            Helper::flog($sql);
            return $sql;
        }

        private function _executeInsertQuery($sql){
            try 
            {
                $stmt = $this->conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
              
            }
            catch(PDOException $e)
            {
                Helper::flog("Error - Line ". $e->getLine() . " in File ". __FILE__ . " " . $e->getMessage());
                die;
            }
            return $stmt;
        }

        
        /*
           CREATE TABLE functions for mysql
        */
        public function createTable($sql, $tableName){
            if($this->_doesTableExist($tableName)){
                Helper::flog("The table '$tableName' already exists. Skipping proccess.");
                throw new Exception(SELF::DB_TABLE_ALREADY_EXISTS. " in ".__FUNCTION__ ."()");
            }else{
                Helper::flog("Creating table '$tableName'...\n$sql");
                return $this->_executeInsertQuery($sql);
            }
        }


        private function _doesTableExist($tableName){
            $sql = "SHOW TABLES LIKE '$tableName';";
            try 
            {
                $stmt = $this->conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
              
            }
            catch(PDOException $e)
            {
               Helper::flog("Error - Line ". $e->getLine() . " in File ". __FILE__ . " " . $e->getMessage());
               die;
            }
            $doesExist = count($stmt->fetchAll()) > 0 ? true : false;
            return $doesExist;

        }

     





       



        
        
    }




?>