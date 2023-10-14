<?php
include('../config/dbcon.php');


function getAll($table)
{
    global $con;
    $query = "SELECT * FROM $table";
    return $query_run = mysqli_query($con, $query);
}

function redirect($url,$msg)
{
    $_SESSION['message'] = $msg;
    header('Location: '.$url);
    exit();
}

function respoCNE($respoId) //returns le cne de l'eleve correspondant au responsable de club
{
    global $con;
    $respo_query = "SELECT * from responsable_club WHERE id_respo = '$respoId' ";
    $respo_query_run = mysqli_query($con,$respo_query);
    if(mysqli_num_rows($respo_query_run) > 0)
    {
        $respo_data = mysqli_fetch_array($respo_query_run);
        $respoMembreId = $respo_data['id_membre'];

        //recuperer le respo en tant que membre
        $respoMembre_query = "SELECT * from membre_club WHERE id_membre = '$respoMembreId' ";
        $respoMembre_query_run = mysqli_query($con,$respoMembre_query);
        if(mysqli_num_rows($respoMembre_query_run) > 0)
        {
            $respoMembre_data = mysqli_fetch_array($respoMembre_query_run);
            $respoCNE = $respoMembre_data['cne'];
        }
    }
    return $respoCNE;
}


function cneData($cne) //returns a list of eleve attributes
{
    global $con;
    $eleve_query = "SELECT * FROM eleve WHERE cne = '$cne' ";
    $eleve_query_run = mysqli_query($con,$eleve_query);
    if(mysqli_num_rows($eleve_query_run) > 0)
    {
        $eleve_data = mysqli_fetch_array($eleve_query_run);
    }
    return [
        'nom' => $eleve_data['nom'],
        'prenom' => $eleve_data['prenom'],
        'email' => $eleve_data['email'],
        'tel' => $eleve_data['tel']
    ];
}

?>