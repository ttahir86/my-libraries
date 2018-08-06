<?php

require_once('mylibs.bundle.php');


# ===================================================           Encryptor             ===================================================
Log::sLog("<h2> Start Encryptor()</h2>", false);
encrypt("testing 0");
$encryptedValueGcm = encrypt("testing 1", "aes-128-gcm");
$encryptedValueCbc = encrypt("testing 2", "aes-128-cbc");
$metaData = Encryptor::GetMetaData();
Log::pre($metaData);

function encrypt($str, $encType = "")
{
   
    Encryptor::using($encType);
    $enc = Encryptor::encrypt($str);
    $dec = Encryptor::decrypt($enc);
    Log::slog("$enc<br/>$dec<br/>");
    return $enc;
}

#===================================================             DBConnect         ===================================================
Log::sLog("<h2> Start DBConnect()</h2>", false);
$db = new DBConnect();
$db->openConnection();
$db->select();
$db->from('userTable');
$temp = $db->query();
$db->closeConnection();

Log::slog("all users:");
Log::pre($temp);
Log::slog("<br/>");

#===================================================             Log               ===================================================
Log::sLog("<h2> Start Log()</h2>", false);

Log::flog($metaData[$encryptedValueGcm]['iv'], "gcm-log.txt");
Log::flog($metaData[$encryptedValueGcm]['tag'], "gcm-log.txt");
Log::sLog("<span>Wrote to log file gcm-log.txt</span>");

Log::flog($metaData[$encryptedValueCbc]['iv']);
Log::flog($metaData[$encryptedValueCbc]['hmac']);
Log::sLog("<span>Wrote to default log file log.txt</span>");



#===================================================             User               ===================================================
Log::sLog("<h2> Start User()</h2>", false);
doUserStuff("test","test");
doUserStuff("test","talal");
doUserStuff("sdfadsf","asdfdsf");


function doUserStuff($username, $password)
{
    $userLog = "user-log.txt";
    $user = new User();
    $isLoggedIn = $user->login($username, $password);
    if(!$isLoggedIn)
    {
        Log::slog($user->GetErrorMessage());
        Log::flog($user->GetErrorMessage() .": $username, $password" , $userLog);
    }else{
        Log::slog('Logged in successfully as user: '. $user->GetPropertyValue('name'));
        Log::pre($user);
        Log::flog($user, $userLog);
    
    }
}

