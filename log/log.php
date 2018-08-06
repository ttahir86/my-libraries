<?php


    class Log
    {
        private const WRITE_TO_LOG = true;
        private const WRITE_TO_SCREEN = true;
        private const LOGS_DIRECTORY = "logs";
        private static $FileName = "log.txt";
        private static $FileNameAndPath = "./" . self::LOGS_DIRECTORY . "/log.txt";

        /*
            screen log
        */
        public static function pre($s, $lineBreak = true)
        {
            if (self::WRITE_TO_SCREEN)
            {
                
       
                    echo"<pre>";
                        var_dump($s);
                    echo"</pre>";
                    return;
           
            }
        }


        /*
            screen log
        */
        public static function slog($s, $lineBreak = true)
        {
            if (self::WRITE_TO_SCREEN)
            {
                
                if (is_array($s))
                {
                    $s = json_encode($s);
                }

                if ($lineBreak){
                    echo $s ."<br/>";
                }else{
                    echo $s ;
                }
            }
        }

        /*
            Screen var_dump();
        */
        public static function svd($s, $linebreak = true)
        {
            if (self::WRITE_TO_SCREEN)
            {
                var_dump($s);
                if($linebreak)
                {
                    echo "<br/>";
                }
                
            }
        }

        

        /*
            @description    
                Checks to see how the log should be written and calls a function to do the actually writing of the log.
            @param  string/array     $s                     - Message that is written to log;
            @param  string           $fileName              - FileName of log, by default we use "log.txt"           
            @param  boolean          $bAppendContents       - Determines whether message should be appended to end of file contents. 
            @param  boolean          $bAddDateTime          - Determines whether datetime should be appended before every message. 
            @call   function         _writeToLog()          - Function that does all the writing to the file.
            @call   function        _setFileNameAndPath()   - Function that sets the $FileNameAndPath field. 
            @return                 none                
            
        */
        public static function flog($s, $fileName = "log.txt", $bAppendContents = true, $bAddDateTime = True)
        {
            if (self::WRITE_TO_LOG)
            {

                self::$FileNameAndPath;

                if ($fileName != self::$FileName)
                {
                    self::_setFileNameAndPath($fileName);
                }

                if (is_array($s))
                {
                    $s = json_encode($s);
                }

                if(is_object($s))
                {
                    $s = json_encode((array)$s);
                }

                if ($bAddDateTime)
                {
                    $s = time() ." : $s";
                }
                
                self::_writeToLog($s, $bAppendContents);
            }
        }



        /*
            @description
                writes to log in specified directory with specified filename. If directory or file does not exist, the function creates them.
            @param string           $s                  - Message that is written to log;    
            @param boolean          $bAppendContents    - Determines whether message should be appended to end of file contents.
            @return                 none 
        */
        private static function _writeToLog($s,  $bAppendContents = true)
        {
            $doesDirectoryExist = self::_doesFileExist();
            if ($bAppendContents && $doesDirectoryExist){
                $content = file_get_contents(self::$FileNameAndPath);
                $content .= "\n". $s;
            }else {
                $content = "\n" . $s;
            }
            // Write the contents back to the file
            file_put_contents(self::$FileNameAndPath, $content);
        }

        private static function _doesFileExist()
        {
            # check if directory exists, if not, make it.
            $doesDirectoryExist = true;
            if (!file_exists(self::LOGS_DIRECTORY))
            {
                $doesDirectoryExist = false;
                mkdir(self::LOGS_DIRECTORY, 0777, true);
            }

            if (!file_exists(self::$FileNameAndPath))
            {
                $doesDirectoryExist = false;
            }
            return $doesDirectoryExist;
        }

        
        /*
            @description
                sets the fields FileName and FileNameAndPath
            @param string     $fileName     - The new file name to be assgined;    
            @return           none 
        */
        private static function _setFileNameAndPath($fileName)
        {
            self::$FileName = $fileName;
            self::$FileNameAndPath = "./" . self::LOGS_DIRECTORY . "/". self::$FileName;
        }






    }

?>