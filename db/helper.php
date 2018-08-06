<?php 
    
    
    
    class Helper{

        private const bLogScreen = true;
        private const bLogFile = true;
        private const logsDirectory = "logs";
        private const logFileName = "./" . self::logsDirectory . "/db-debug.txt";

        
        /*
            strip all white spaces
        */
        public static function removeWhiteSpace($s)
        {
            return preg_replace('/\s+/', '', $s);
        }


        /*
            screen log
        */
        public static function slog($s)
        {
            if (self::bLogScreen)
            {
                
                if (is_array($s))
                {
                    $s =json_encode($s);
                }
                echo $s ."<br/>";
            }
        }


        /*
            log file
        */
        public static function flog($s)
        {


            if (self::bLogFile)
            {


                if (is_array($s)){
                    $s =json_encode($s);
                }

                $s = time() ." : $s";
                self::writeToLog($s);
            }
        }



        /*
            write to log
        */
        private static function writeToLog($msg,  $bAppendContents = true)
        {
            $DirectoryAndLogFileExists = true;
            if (!file_exists(self::logFileName)) {
                $DirectoryAndLogFileExists = false;
                mkdir(self::logsDirectory, 0777, true);
            }
            if ($bAppendContents && $DirectoryAndLogFileExists){
                $content = file_get_contents(self::logFileName);
                $content .= "\n". $msg;
            }else {
                $content = "\n" . $msg;
            }
            // Write the contents back to the file
            file_put_contents(self::logFileName, $content);
        }


        /*
            check if string/array are empty
        */
        public static function checkIfEmpty($value, $s=""){
            try{
                $temp = Helper::removeWhiteSpace($value);
                if (empty($temp)){
                    Helper::flog($s);
                    die;
                }else if (is_array($temp)){
                    $temp = implode("",$temp);
                    $temp = Helper::removeWhiteSpace($temp);
                    if (empty($temp)){
                        Helper::flog($s);
                        die;
                    }
                }
            }catch(Exception $e) {
                Helper::flog($e->getMessage() . " " . $e->getLine());
            }
        }

    }

?>