<?php
include('../functions/myfunctions.php');

if(isset($_SESSION['auth']))
{
    if($_SESSION['admin'] == null)
    {
        redirect("../accueil/index.php","you are not an admin!");
    }
}
else
{
    redirect("../connexion/login.php","login to continue");
}

?>