<?php

include('../config/dbcon.php');
include('../functions/myfunctions.php');

if(isset($_POST['validate_btn']))
{
    $salle_status = 0;
    
    $idReun = $_POST['validate_btn'];
    //tout d'abord, check si la salle de la demande de reun est libre
    $salleReun_query = "SELECT * FROM reunion WHERE id_reun = '$idReun' ";
    $salleReun_query_run = mysqli_query($con,$salleReun_query);
    if(mysqli_num_rows($salleReun_query_run) > 0)
    {
        $salleReun_data = mysqli_fetch_array($salleReun_query_run);
        $salleId = $salleReun_data['id_salle'];
        $salle_query = "SELECT * FROM salle WHERE id_salle = '$salleId' ";
        $salle_query_run = mysqli_query($con,$salle_query);
        if(mysqli_num_rows($salle_query_run) > 0)
        {
            $salle_data = mysqli_fetch_array($salle_query_run);
            $salle_status = $salle_data['salle_isFree'];
        }
    }

    if($salle_status == 0) //si la salle est deja libre, on continue dans la validation de la reunion
    {
        //reun_isApproved = 1
        $validate_query = " UPDATE reunion SET reun_isApproved = '1' where id_reun = '$idReun' ";
        $validate_query_run = mysqli_query($con, $validate_query);
        if($validate_query)
        {
            //fetch la salle de la reunion
            $salle_query = "SELECT * FROM reunion where id_reun = '$idReun' ";
            $salle_query_run = mysqli_query($con,$salle_query);
            if(mysqli_num_rows($salle_query_run) > 0)
            {
                $salle_data = mysqli_fetch_array($salle_query_run);
                $idSalle = $salle_data['id_salle'];
                //salle associee: salle_isFree = 1
                $salle_occupied_query = "UPDATE salle SET salle_isFree = '1' WHERE id_salle = '$idSalle' ";
                $salle_occupied_query_run = mysqli_query($con,$salle_occupied_query);
                if($salle_occupied_query_run)
                {
                    redirect("demandes_reunions.php","reunion validee");
                }
            }
        }
        else
        {
            redirect("demandes_reunions.php","erreur imprevue lors de la validation de la reunion!");
        }
    }
    else //si la salle est deja occupee
    {
        redirect("demandes_reunions.php","salle occupee");
    }
}

if(isset($_POST['reject_btn']))
{
    $idReun = $_POST['reject_btn'];
    $validate_query = " UPDATE reunion SET reun_isApproved = '-1' where id_reun = '$idReun' ";
    $validate_query_run = mysqli_query($con, $validate_query);
    if($validate_query)
    {
        redirect("demandes_reunions.php","reunion rejetee");
    }
    else
    {
        redirect("demandes_reunions.php","erreur imprevue lors de le rejet de la reunion!");
    }
}

?>