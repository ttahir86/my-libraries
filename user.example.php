<?php

    require_once("mylibs.bundle.php");

    $user = new User();
    

    $isSuccess = $user->Login("test", "talal");
    if($isSuccess)
    {
        echo $user->GetPropertyValue('name');
        echo "<br>". $user->GetPropertyValue('id'). "<br/><br/>";
        var_dump($user);

    }else{
        echo $user->GetErrorMessage();

    }


?>