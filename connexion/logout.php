<?php
session_start();

if(isset($_SESSION['auth']))
{
    unset($_SESSION['auth']);
    unset($_SESSION['admin']);
    unset($_SESSION['respo']);
    unset($_SESSION['idRespo']);
    unset($_SESSION['membre']);
    unset($_SESSION['auth_user']);
    $_SESSION['message'] = "Déconnecté avec succés";
}

header('Location: ../accueil/index.php?state=logout');

?>