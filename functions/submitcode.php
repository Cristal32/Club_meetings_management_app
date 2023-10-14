<?php
session_start();
include('../config/dbcon.php');
include('myfunctions.php');


//si l'admin veut editer la salle d'un eleve
if(isset($_POST['editSalle_btn']))
{
    //fetch le nom de salle et numero de salle selectionnes
    $nomSalle = $_POST['nom-salle'];
    $numSalle = $_POST['num-salle'];

    //fetch anciens nom et numero de salle puis id de cette ancienne salle
    $idReun = $_POST['editSalle_btn'];

    //check if cette salle existe
    $salle_query = "SELECT * FROM salle WHERE nom_salle = '$nomSalle' AND num_salle = '$numSalle' ";
    $salle_query_run = mysqli_query($con,$salle_query);
    if(mysqli_num_rows($salle_query_run) > 0) //si cette salle existe
    {
        $salle_data = mysqli_fetch_array($salle_query_run);
        $salleId = $salle_data['id_salle']; //fetch son id

        $update_salle_reun = "UPDATE reunion SET id_salle = '$salleId' WHERE id_reun = '$idReun' "; //update la reunion avec la nouvelle valeur de id salle
        $update_salle_reun_run = mysqli_query($con,$update_salle_reun);
        if($update_salle_reun_run)
        {
            redirect("../admin/demandes_reunions.php","Salle modifiée");
        }
    }
    else //si cette salle n'existe pas
    {
        redirect("../admin/demandes_reunions.php","Cette salle n'existe pas!");
    }
}

//si l'admin veut creer un club
if(isset($_POST['createClub_btn']))
{
    $clubNom = mysqli_real_escape_string($con, $_POST['nom-club']);
    $clubDomaine = mysqli_real_escape_string($con, $_POST['domaine-club']);
    $insertClub = "INSERT INTO club(nom_club,domaine) VALUES ('$clubNom','$clubDomaine')";
    $insertClub_run = mysqli_query($con,$insertClub);
    if($insertClub_run)
    {
        redirect("../admin/index.php",'Club créé avec succés');
    }
    else
    {
        redirect("../admin/index.php",'Erreur inattendue');
    }
}

//bouton de suppression des clubs par l'admin 
if(isset($_POST['deleteClub_btn']))
{
    $idClub = $_POST['clubId'];
    $deleteClub_query = "DELETE FROM club WHERE id_club = '$idClub' ";
    $deleteClub_query_run = mysqli_query($con,$deleteClub_query);
    if($deleteClub_query_run)
    {
        redirect("../admin/index.php",'Club supprimé avec succés');
    }
    else
    {
        redirect("../admin/index.php",'Erreur inattendue');
    }
}

//bouton d'ajout d'un membre de club
if(isset($_POST['addEleve_btn']))
{
    $eleve = $_POST['nom-eleve']; //recupere le resultat de la selection: 'nom prenom cne' dans membresClub.php section ajout d'un membre
    $eleve_array = explode(' ',$eleve); //divise ce resultat en elements d'un tableau avec comme delimiteur l'espace ' '
    $cne = end($eleve_array); //cne est le dernier element

    $idClub = $_POST['addEleve_btn'];
    //maintenant qu'on a selectionne cet eleve, il faut l'ajouter a la table des membres 
    $insert_membre_query = "INSERT INTO membre_club (cne,id_club) VALUES ('$cne','$idClub')";
    $insert_membre_query_run = mysqli_query($con,$insert_membre_query);
    if($insert_membre_query_run)
    {
        //maintenant que c'est un membre de club, il doit avoir un compte utilisateur
        //si il en a deja un, cool
        $checkUser_query = "SELECT * FROM utilisateur WHERE id_ifEleve = '$cne' ";
        $checkUser_query_run = mysqli_query($con,$checkUser_query);
        if(mysqli_num_rows($checkUser_query_run) > 0) //si il a deja un compte user
        {
            redirect("../admin/membresClub.php?idClub=" . $idClub,'Membre de club ajouté avec succés');
        }
        else //si il n'a pas de compte user, il faut lui en creer un
        {
            $mdp = $_POST['password'];
            $insert_user_query = "INSERT INTO utilisateur (login,mdp,id_ifAdmin,id_ifEleve) VALUES ('$cne','$mdp',null,'$cne')";
            $insert_user_query_run = mysqli_query($con,$insert_user_query);
            if($insert_user_query_run)
            {
                redirect("../admin/membresClub.php?idClub=" . $idClub,'Membre de club ajouté avec succés');
            }
            else
            {
                redirect("../admin/membresClub.php?idClub=" . $idClub,'Erreur imprévue');
            }
        }
    }
}

//bouton pour retirer un membre
if(isset($_POST['retirerEleve_btn']))
{
    //recuperer les infos de l'eleve a retirer et du club
    $idClubEleve = $_POST['retirerEleve_btn'];
    $cneEleve_aRetirer = $_POST['cne_input'];

    $retirerMembre_query = "DELETE FROM membre_club where cne = '$cneEleve_aRetirer' ";
    $retirerMembre_query_run = mysqli_query($con,$retirerMembre_query);
    if($retirerMembre_query_run)
    {
        redirect("../admin/membresClub.php?idClub=" . $idClubEleve,'élève retiré de '.$idClub.' avec succés');
    }
    
}

//partage des reunions approuvees dans la page d'accueil
//cliquer sur le bouton rend reun_isApproved = 2 --> va etre affichee dans la page d'accueil
/*if(isset($_POST['sendtoAccueil_btn']))
{
    $updateReunsApprouvees_query = "UPDATE reunion SET reun_isApproved = '2' WHERE reun_isApproved = '1' " ;
    $updateReunsApprouvees_query_run = mysqli_query($con,$updateReunsApprouvees_query);
    if($updateReunsApprouvees_query_run)
    {
        redirect("../admin/reunions_approuvees.php","reunions approuvees partagees dans la page d'accueil");

    }
}*/

//une fois qu'il veut disposer de toutes les reunions en fin de semaine, clear et archiver
//les reunions acceptess auront reun_isApproved = 3, et les reunions rejetees auront reun_isApproved = -3
//donc 3 et -3 symbolisent les reunions archivees qu'elles soient validees ou rejetees, bien sur les reunions encore en attente ne subissent aucun changement
if(isset($_POST['archive_btn']))
{
    //update les reunions approuvees (reun_isApproved = 1) et affichees (reun_isApproved = 2)
    $archiveReunsAppr_query = "UPDATE reunion SET reun_isApproved = '3' WHERE reun_isApproved = '1' " ;
    $archiveReunsAppr_query_run = mysqli_query($con,$archiveReunsAppr_query);

    //update les reunions rejetees (reun_isApproved = -1) 
    $archiveReunsRej_query = "UPDATE reunion SET reun_isApproved = '-3' WHERE reun_isApproved = '-1' ";
    $archiveReunsRej_query_run = mysqli_query($con,$archiveReunsRej_query);

    if($archiveReunsAppr_query_run && $archiveReunsRej_query_run)
    {
        //puisque toutes les reunions ont finis et sont maintenant archivees, il faut liberer les salles
        $free_salles = "UPDATE salle SET salle_isFree = '0' ";
        $free_salles_run = mysqli_query($con,$free_salles);
        if($free_salles_run)
        {
            redirect("../admin/calendar.php","Réunions archivées avec succés");
        }
    }

}

//modifier respo
if(isset($_POST['editRespo_btn']))
{
    //recuperer le club 
    $RespoClubId = $_POST['editRespo_btn'];
    //recuperer quel eleve qui va devenir respo
    $respoAEditer = $_POST['select_membre'];
    $respoAEditerArray = explode(' ',$respoAEditer);
    $respoCNE = end($respoAEditerArray); //recupere son cne

    $membreRespo_query = "SELECT * FROM membre_club WHERE cne = '$respoCNE' ";
    $membreRespo_query_run = mysqli_query($con,$membreRespo_query);
    if(mysqli_num_rows($membreRespo_query_run) > 0)
    {
        $membreRespo_data = mysqli_fetch_array($membreRespo_query_run);
        $idMembreRespo = $membreRespo_data['id_membre']; //on a recupere le id du membre

        $respo_query = "SELECT * FROM responsable_club WHERE id_club = '$RespoClubId' "; //selectionner le responsable du club pour le changer
        $respo_query_run = mysqli_query($con,$respo_query);
        if(mysqli_num_rows($respo_query_run) > 0)
        {
            $respo_data = mysqli_fetch_array($respo_query_run);
            $respoId = $respo_data['id_respo']; //on a recupere le id du respo

            //maintenant on change le id du membre dans la table respo 
            $editRespo_query = "UPDATE responsable_club SET id_membre = '$idMembreRespo' WHERE id_respo='$respoId' ";
            $editRespo_query_run = mysqli_query($con,$editRespo_query);
            if($editRespo_query_run)
            {
                redirect("../admin/index.php","Responsable de club remplacé avec succés");
            }
            else{redirect("../admin/index.php","Erreur inattendue");}

        }
        else{redirect("../admin/index.php","Erreur inattendue");}
    }
    else
    {
        redirect("../admin/index.php","Erreur inattendue");
    }
}

//=============================================== respo de club ================================================================

//bouton d'ajout d'une demande de reunion par le respo de reunion
if(isset($_POST['reunion_btn']))
{
    $id_club = $_SESSION['membreInfo']['id_club'];

    $idRespo =  $_SESSION['idRespo'];

    $titre = mysqli_real_escape_string($con,$_POST['titre-reunion']);
    $date = mysqli_real_escape_string($con,$_POST['date-reunion']);
    $heureDebut = mysqli_real_escape_string($con,$_POST['heure-debut-reunion']);
    $heureFin = mysqli_real_escape_string($con,$_POST['heure-fin-reunion']);

    //$nomSalle = mysqli_real_escape_string($con,$_POST['nom-salle']);
    //$numSalle = mysqli_real_escape_string($con,$_POST['num-salle']);
    $salle = mysqli_real_escape_string($con,$_POST['salle']);
    $nomSalle = explode(' ',$salle)[0];
    $numSalle = explode(' ',$salle)[1];
    $idSalle_query = "SELECT id_salle FROM salle WHERE nom_salle='$nomSalle' AND num_salle='$numSalle' ";
    $idSalleResult = mysqli_query($con, $idSalle_query);
    $idSalle = mysqli_fetch_assoc($idSalleResult)['id_salle'];

    $desc = mysqli_real_escape_string($con,$_POST['desc-reunion']);

    // Insert reunion data
    $insert_query = "INSERT INTO reunion (id_club,id_respo,titre_reun,date_reun,heure_deb_reun,heure_fin_reun,id_salle,desc_reun,reun_isApproved) VALUES ('$id_club','$idRespo','$titre','$date','$heureDebut','$heureFin','$idSalle','$desc',0)";
    $insert_query_run = mysqli_query($con, $insert_query);

    if($insert_query_run)
    {
        redirect("../onglets/reunion.php","Demande de réunion ajoutée avec succés");
    }
    else
    {
        redirect("../onglets/reunion.php","Erreur imprévue");
    }
}

//bouton d'ajout d'un bilan par le responsable de club
if(isset($_POST['bilan_btn']))
{
    $idReun = $_POST['idReun'];
    $bilan = mysqli_real_escape_string($con, $_POST['bilan_txt']);
    $insert_bilan_query = "INSERT INTO bilan(id_reun,msg_bilan) VALUES ('$idReun','$bilan')";
    $insert_bilan_query_run = mysqli_query($con, $insert_bilan_query);

    if($insert_bilan_query_run)
    {
        redirect("../onglets/reunion.php","Bilan de réunion soumis avec succés");
    }
    else
    {
        redirect("../onglets/reunion.php","Erreur imprévue");
    }
}
?>
