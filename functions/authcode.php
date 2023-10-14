<?php

session_start();
include('../config/dbcon.php');
include('myfunctions.php');

//bouton d'authentification login
if(isset($_POST['login_btn']))
{
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    $login_query = "SELECT * FROM utilisateur WHERE login='$username' AND mdp='$password' ";
    $login_query_run = mysqli_query($con, $login_query);

    if(mysqli_num_rows($login_query_run) > 0)
    {
        $_SESSION['auth'] = true;

        $userdata = mysqli_fetch_array($login_query_run);
        $name = $userdata['login'];
        $idEleve = $userdata['id_ifEleve'];
        $idAdmin = $userdata['id_ifAdmin'];

        $_SESSION['auth_user'] = $name;
        $_SESSION['adminId'] = $userdata['id_ifAdmin'];

        //------------------------------------- redirection vers la page appropriee -------------------------------------------------

        if($idAdmin != NULL ) //si c'est un admin
        {
            //sessions
            $_SESSION['admin'] = true;
            $_SESSION['membre'] = false;
            $_SESSION['respo'] = false;
            redirect("../admin/calendar.php","Bienvenu au tableau de bord");
        }
        else 
        {   
            if($idEleve != null) //si c'est un eleve
            {
                $_SESSION['eleve'] = $idEleve;

                //selectionne le id_membre de l'eleve en question
                $membre_query = "SELECT * FROM membre_club WHERE cne='$idEleve' ";
                $membre_query_run = mysqli_query($con, $membre_query);

                if(mysqli_num_rows($membre_query_run) > 0)
                { 
                    
                    $membredata = mysqli_fetch_array($membre_query_run);
                    $idmembre = $membredata['id_membre'];
                    $id_Club = $membredata['id_club'];

                    //tirer le nom du club
                    $Club_query = "SELECT * FROM club WHERE id_club='$id_Club' ";
                    $Club_query_run = mysqli_query($con, $Club_query);

                    if(mysqli_num_rows($Club_query_run) > 0)
                    {
                        $clubdata = mysqli_fetch_array($Club_query_run);
                        $nomClub = $clubdata['nom_club'];

                        $_SESSION['membreInfo'] = [
                            'id_club' => $id_Club,
                            'nom_club' => $nomClub
                        ];
                        
                    }

                    //id du respo si le membre en est un
                    $respo_query = "SELECT * FROM responsable_club WHERE id_membre='$idmembre' ";
                    $respo_query_run = mysqli_query($con, $respo_query);

                    if(mysqli_num_rows($respo_query_run) > 0) //si le membre est un respo
                    {
                        $respodata = mysqli_fetch_array($respo_query_run);
                        $idRespo = $respodata['id_respo'];

                        if($idRespo != null) //si c'est un responsable de club
                        {
                            $_SESSION['idRespo'] = $idRespo;
                            $_SESSION['respo'] = true;
                            $_SESSION['membre'] = false;
                            redirect("../membre/respo.php","Connexion réussie en tant que responsable de club");
                        }
                    }
                    else //si c'est un membre de club normal
                    {
                        $_SESSION['respo'] = false;
                        $_SESSION['membre'] = true;
                        redirect("../membre/index.php","Connexion réussie en tant que membre de club");
                    }
                }
                else //c'est un utilisateur mais pas un membre de club
                {
                    redirect("../membre/index.php","Vous n'êtes membre d'aucun club!");
                }
            }
            
        }

    }
    else
    {
        $_SESSION['message'] = "Nom d'utilisateur ou mot de passe invalide!";
        header('Location: ../connexion/login.php');
    }
}

?>