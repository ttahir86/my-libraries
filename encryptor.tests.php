<?php
require_once('mylibs.bundle.php');


const TITLE = "<h1>Encryptor Library Tests:</h1><br/>";
# run these tests;
const TEST_DEFAULT_ENCRYPTION = true;
const TEST_AES_128_GCM = true;
const TEST_AES_128_CBC = true;
const TEST_GET_META_DATA = true;

# output upon pass/fail;
const PASSED = "<span style='color:green'>PASSED</span>";
const FAILED = "<span style='color:red'>FAILED</span>";

# keeps count of tests run
$testsRan = 0;

echo TITLE;
if (TEST_DEFAULT_ENCRYPTION)
{
    _testEncrypt("Test Default Encryption (GCM)","testing");
    $testsRan++;
}

if (TEST_AES_128_GCM)
{

    _testEncrypt("Test GCM Encryption Type","gcm", "aes-128-gcm");
    $testsRan++;
}

if (TEST_AES_128_CBC)
{
    _testEncrypt("Test CBC Encryption Type","cbc", "aes-128-cbc");
    $testsRan++;
}


if (TEST_GET_META_DATA)
{

    TestGetMetaData($testsRan);
}



function TestGetMetaData($count)
{
    $c = count(Encryptor::GetMetaData());
    if($c === $count)
    {
        echo "<h3>" . __FUNCTION__ .":</h3>  " . PASSED;
        Log::pre(Encryptor::GetMetaData());
    }else {
        echo "<h3>" . __FUNCTION__ .":</h3> " . FAILED;
    }
}




// test default encryption works.
function _testEncrypt($testName, $str, $type = "")
{
    if (!empty($type))
    {
        Encryptor::using($type);
    }
    
    $enc = Encryptor::encrypt($str);
    $dec = Encryptor::decrypt($enc);
    
    if ($str  === $dec)
    {
        echo "<h3>$testName:</h3>" . PASSED;
        echo": $str, $dec<hr>";
        return true;
    }
    echo $testName . ":</h3>" . FAILED ; 
    echo": $str, $dec";


    Log::pre($str);
    Log::pre($dec);
    return false;
}

