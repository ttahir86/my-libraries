<?php

    class Encryptor 
    {
        const ENCRYPTOR = "Encryptor";
        const KEY = "KEY";
        const IV = "iv";
        const TAG = "tag";
        const HMAC = 'hmac';
        const IVLEN = 'ivlen';

        // encryption types
        const AES_128_GCM = "aes-128-gcm";
        const AES_128_CBC = "aes-128-cbc";
        const SHA256 = 'sha256';


        private static $_cipher = self::AES_128_GCM;
        private static $_metaData = array();
        private static $_settings = array();
      

        /*
            @description:   defines which encryption type to use (gmc vs cbc).
            @params:        $cipher_scheme (defaults to AES_128_GCM)
            @sets:          $_cipher, $_settings
            @return:        none
            @calls:         _loadEncryptorSettings().
        */
        public static function using($cipher_scheme = self::AES_128_GCM)
        {
            if ( empty($cipher_scheme) )
            {
                $cipher_scheme = self::AES_128_GCM;
            };

            if( empty(self::$_settings) || strtoupper(self::$_cipher) !== strtoupper($cipher_scheme))
            {
       
                self::$_cipher = strtolower($cipher_scheme);
                self::_loadEncryptorSettings( strtoupper($cipher_scheme) );
            }else{

                self::_loadEncryptorSettings( strtoupper(self::AES_128_GCM) );
            }
        }

        /*
            @description:   encrypts a string to selected encryption type specification.
            @params:        $plaintext
            @sets:          $_metaData
            @return:        encrypted string 
            @calls:         none.
        */
        public static function encrypt($plaintext)
        {

            if(empty(self::$_settings))
            {
                self::_loadEncryptorSettings(strtoupper(self::AES_128_GCM));
            }
           
            if (in_array(self::$_cipher, openssl_get_cipher_methods()))
            {
                if(strtolower(self::$_cipher) === self::AES_128_GCM){
                    
                    $ivlen = openssl_cipher_iv_length(self::$_cipher);
                    $iv = openssl_random_pseudo_bytes($ivlen);
                    $encryptedText = openssl_encrypt($plaintext, self::$_cipher, self::$_settings[self::KEY], $options=0, $iv, $tag);
                    
                    self::$_metaData[$encryptedText] = array
                    (
                        self::IV    => $iv, 
                        self::TAG   => $tag
                    );
                    return $encryptedText;


                }else if (strtolower(self::$_cipher) === self::AES_128_CBC){
                    //$key previously generated safely, ie: openssl_random_pseudo_bytes

                    $ivlen = openssl_cipher_iv_length(self::$_cipher);
                    $iv = openssl_random_pseudo_bytes($ivlen);
                    $ciphertext_raw = openssl_encrypt($plaintext, self::$_cipher, self::$_settings[self::KEY], $options=OPENSSL_RAW_DATA, $iv);
                    $hmac = hash_hmac(self::SHA256, $ciphertext_raw,self::$_settings[self::KEY], $as_binary=true);
                    $encryptedText = base64_encode( $iv.$hmac.$ciphertext_raw );
                    self::$_metaData[$encryptedText] = array
                    (
                        self::IV    => $iv,
                        self::HMAC  => $hmac,
                        self::IVLEN => $ivlen
                    );
                    return $encryptedText;

                }
            }
        }

        /*
            @description:   decrypts a string to selected encryption type specification.
            @params:        $encryptedText
            @sets:          $_metaData
            @return:        Success: encrypted string, Failure: boolean false. 
            @calls:         none.
        */
        public static function decrypt($encryptedText)
        {

            
            if (!array_key_exists($encryptedText, self::$_metaData))
            {
                return false;
            }
            
            
            if (in_array(strtolower(self::$_cipher), openssl_get_cipher_methods()))
            {
                if(strtolower(self::$_cipher) === self::AES_128_GCM){
                    $original_plaintext = openssl_decrypt($encryptedText, self::$_cipher, self::$_settings[self::KEY], $options=0,  self::$_metaData[$encryptedText][self::IV],  self::$_metaData[$encryptedText][self::TAG]);
                    return $original_plaintext;

                }else if (strtolower(self::$_cipher) === self::AES_128_CBC){
                    $c = base64_decode($encryptedText);

                    $hmac = substr($c, self::$_metaData[$encryptedText][self::IVLEN], $sha2len=32);
                
                    $ciphertext_raw = substr($c, self::$_metaData[$encryptedText][self::IVLEN]+$sha2len);

                    $original_plaintext = openssl_decrypt($ciphertext_raw, self::AES_128_CBC, self::$_settings[self::KEY], $options=OPENSSL_RAW_DATA, self::$_metaData[$encryptedText][self::IV]);
                    
                    $calcmac = hash_hmac(self::SHA256, $ciphertext_raw, self::$_settings[self::KEY], $as_binary=true);
                    if (hash_equals($hmac, $calcmac))//PHP 5.6+ timing attack safe comparison
                    {
                        return $original_plaintext;
                    }else{
                        return false;
                    }
                }
            }

        }

        public static function GetMetaData()
        {
            return self::$_metaData;
        }

        /*
            @description:   loads settings for selected encryption type from encryption.settings.json config. Returns true upon success, false if exception thrown.
            @params:        $cipher_scheme
            @sets:          $_settings
            @return:        boolean 
            @calls:         none.
        */
        private static function _loadEncryptorSettings($cipher_scheme)
        {
            try{
                if ( empty($cipher_scheme) )
                {
                    $cipher_scheme = strtoupper(self::AES_128_GCM);
                }
                $str = file_get_contents(__DIR__ . '/' . ENCRYPTION_SETTINGS_FILENAME);
                $temp = json_decode($str, true);
                self::$_settings = $temp[self::ENCRYPTOR][$cipher_scheme];
                return true;
            }catch (Exception $ex){
                return false;
            }
        }

        

        
    }

?>