<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('./user-v2/user.bundle.php');




// $hash = Hasher::hp("talal");
// Log::pre($hash);
// Log::pre(Hasher::vp("talal", $hash));




$user = new User();

$data = array
(
    "username"=> "yo",
    "password"=> "ma",
    "email" => "sunny@test.com"
);
$user->SignUp($data);

Log::slog($user->GetErrorMessage());

if($user->GetErrorMessage() == 'username already exists.')
{
    Log::slog("Logging you in...");
    $isLoggedIn = $user->Login($data['username'], $data['password']);
    if($isLoggedIn)
    {
        Log::slog("username: " . $user->GetPropertyValue('name'));
        Log::slog("email: " . $user->GetPropertyValue('email'));
    }else{
        Log::slog($user->GetErrorMessage());
    
    }
}else if (!$user->GetErrorMessage())
{

}







#Log::pre($user);




// Log::slog($user->GetErrorMessage());


?>