<?php 
session_start();

$current_page = basename($_SERVER['PHP_SELF'], ".php");

include('../config/dbcon.php');
include('../functions/myfunctions.php'); 

$nomClub = null;
$domaineClub = null;
$dateClub = null;

//fetch id de l'eleve utilisateur -----------------------------------------------------------
$userId = null;
$username = $_SESSION['auth_user'];
$user_query = "SELECT * FROM utilisateur WHERE login = '$username' ";
$user_query_run = mysqli_query($con,$user_query);
if(mysqli_num_rows($user_query_run) > 0)
{
    $user_data = mysqli_fetch_array($user_query_run);
    $userId = $user_data['id_ifEleve'];
}

//recuperation du membre -----------------------------------------------------------
$membreId = null; $membre_club = null;
$membre_query = "SELECT * FROM membre_club WHERE cne = '$userId' ";
$membre_query_run = mysqli_query($con,$membre_query);
if(mysqli_num_rows($membre_query_run) > 0)
{
    $membre_data = mysqli_fetch_array($membre_query_run);
    $membreId = $membre_data['id_membre'];
    $membre_club = $membre_data['id_club'];
}

//check if responsable or not to redirect to the right header -----------------------------------------------------------
$isRespo = null;
$respo_query = "SELECT * from responsable_club WHERE id_membre = '$membreId' ";
$respo_query_run = mysqli_query($con,$respo_query);
if(mysqli_num_rows($respo_query_run) == 0) { include('../includes/header.php'); }
else { include('../includes/respoHeader.php'); }

//recuperation du club du membre ---------------------------------------------------------
$club_de_user_query = "SELECT * FROM club WHERE id_club = '$membre_club' ";
$club_de_user_query_run = mysqli_query($con,$club_de_user_query) ;
if(mysqli_num_rows($club_de_user_query_run) > 0) //if a club for this member exists
{
    $club_de_user_data = mysqli_fetch_array($club_de_user_query_run);
    $nomClub = $club_de_user_data['nom_club'];
    $domaineClub = $club_de_user_data['domaine'];
    $dateClub = $club_de_user_data['date_creation_club'];
}
// ----------------------------------------------------------------------------------------
?>
<div class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Club: <?= $nomClub ?></h1>
            </div>
        </div>
    </div>
</div>

<div class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Réunions planifiées pour la semaine</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Heure de début</th>
                                    <th>Heure de fin</th>
                                    <th>Salle</th>
                                    <th>Etat</th>
                                    <th>Bilan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $dem_reun = getAll("reunion");

                                    if(mysqli_num_rows($dem_reun) > 0)
                                    {
                                        foreach($dem_reun as $item)
                                        {
                                            //reun_isApproved = 1 
                                            if($item['reun_isApproved'] == '1' && $item['id_club'] == $membre_club)
                                            {
                                                //determination du nom et du numero de salle
                                                $nomSalle = null;
                                                $numSalle = null;

                                                $idSalle = $item['id_salle'];
                                                $salle_query = "SELECT * FROM salle WHERE id_salle = '$idSalle' ";
                                                $salle_query_run = mysqli_query($con, $salle_query);
                                                if(mysqli_num_rows($salle_query_run) > 0)
                                                {
                                                    $salleData = mysqli_fetch_array($salle_query_run);
                                                    $nomSalle = $salleData['nom_salle'];
                                                    $numSalle = $salleData['num_salle'];
                                                }

                                                //Determination du nom du club
                                                $nomClub = null;
                                                $idClub = $item['id_club'];
                                                $club_query = "SELECT * FROM club WHERE id_club = '$idClub' ";
                                                $club_query_run = mysqli_query($con, $club_query);
                                                if(mysqli_num_rows($club_query_run) > 0)
                                                {
                                                    $clubData = mysqli_fetch_array($club_query_run);
                                                    $nomClub = $clubData['nom_club'];
                                                }
                                                //id de la reunion demandee
                                                $idReun = $item['id_reun'];
                                                ?>
                                                    <!-- ligne -->
                                                    <tr>
                                                        <td class="text-wrap"> <?= $item['titre_reun']; ?> </td>
                                                        <td class="text-wrap"> <?= $item['desc_reun']; ?> </td>
                                                        <td> <?= $item['date_reun']; ?> </td>
                                                        <td> <?= $item['heure_deb_reun']; ?> </td>
                                                        <td> <?= $item['heure_fin_reun']; ?> </td>
                                                        <td> <?= $nomSalle ?> <?= $numSalle ?> </td>
                                                        <?php
                                                            //determiner si la reunion est en attente, en cours ou finie
                                                            $etat = ' ';
                                                            $date = $item['date_reun'];
                                                            $heure_deb = $item['heure_deb_reun'];
                                                            $heure_fin = $item['heure_fin_reun'];
                                                            $current_date = date('Y-m-d');
                                                            $current_time = date('H:i:s');
                                                            if($date < $current_date || ($date == $current_date && $heure_fin < $current_time))
                                                            {
                                                                $etat = 'Terminee';
                                                                ?>
                                                                    <td><?= $etat; ?></td>
                                                                    <?php
                                                                        //check si le bilan existe, then afficher le bouton bilan
                                                                        $bilan_query = "SELECT * FROM bilan WHERE id_reun = '$idReun' ";
                                                                        $bilan_query_run = mysqli_query($con,$bilan_query);
                                                                        if(mysqli_num_rows($bilan_query_run) > 0 ) //si le bilan existe
                                                                        {
                                                                            ?>
                                                                                <td>
                                                                                    <form action="bilan.php" method="POST">
                                                                                        <input type="hidden" name="idReun" value="<?= $idReun ?>">
                                                                                        <button type="submit" class="btn btn-secondary" name="bilan_btn" value="<?= $idReun; ?>">Bilan</button>   
                                                                                    </form>
                                                                                </td>
                                                                            <?php
                                                                        }
                                                                        else
                                                                        {
                                                                            ?>
                                                                                <td></td>
                                                                            <?php
                                                                        }
                                                            }
                                                            else
                                                            {
                                                                if($date == $current_date && $heure_deb < $current_time && $current_time < $heure_fin)
                                                                {
                                                                    $etat = 'En cours';
                                                                    ?>
                                                                        <td><?= $etat; ?></td>
                                                                        <td> </td>
                                                                    <?php
                                                                }
                                                                else
                                                                {
                                                                    $etat = 'Planifiee';
                                                                    ?>
                                                                        <td><?= $etat; ?></td>
                                                                        <td> </td>
                                                                    <?php
                                                                }
                                                            }
                                                        ?>
                                                    </tr>
                                                <?php
                                            }
                                        }
                                    }
                                    else
                                    {
                                        echo "No records found";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../includes/footer.php');?>