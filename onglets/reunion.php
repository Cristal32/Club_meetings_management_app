<?php 
session_start();

$current_page = basename($_SERVER['PHP_SELF'], ".php");
include('../config/dbcon.php');
include('../functions/myfunctions.php'); 
include('../includes/respoHeader.php');

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
$membre_query = "SELECT * FROM membre_club WHERE cne = '$userId' ";
$membre_query_run = mysqli_query($con,$membre_query);
if(mysqli_num_rows($membre_query_run) > 0)
{
    $membre_data = mysqli_fetch_array($membre_query_run);
    $membreId = $membre_data['id_membre'];
    $membre_club = $membre_data['id_club'];
}

//recuperation du id respo -----------------------------------------------------------
$idRespo = null;
$respo_query = "SELECT * from responsable_club WHERE id_membre = '$membreId' ";
$respo_query_run = mysqli_query($con,$respo_query);
if(mysqli_num_rows($respo_query_run) > 0)
{
    $respo_data = mysqli_fetch_array($respo_query_run);
    $idRespo = $respo_data['id_respo'];
}
?>

<style>
    #bilan-fait{
        background-color: green;
    }
    #bilan-non-fait{
        background: none;
        color: blue;
        border: none;
    }
</style>

<!-- bouton emmennant vers la page du formulaire -->
<div class="py-5">
    <div class='container'>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <?php
                        if(isset($_SESSION['message']))
                        {
                            ?>
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <?= $_SESSION['message']; ?>.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php
                            unset($_SESSION['message']);
                        }
                    ?>
                    <div class="card-header">
                        <h3>Formulaire de demande de réunion: </h3>
                        <a href='formulaireReun.php' class='btn btn-primary' role='button'>Demande de réunion</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- liste des demandes de reunions faites par le responsable en question avec leur statut si elles son approuvees, non approuvees ou en attente -->
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Vos demandes de réunions</h4>
                </div>
                <div class="card-body">
                    
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Club</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Heure de début</th>
                                    <th>Heure de fin</th>
                                    <th>Salle</th>
                                    <th>Statut</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $dem_reun = getAll("reunion");

                                    if(mysqli_num_rows($dem_reun) > 0)
                                    {
                                        foreach($dem_reun as $item)
                                        {
                                            if($item['id_respo'] == $idRespo)
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
                                                ?>
                                                    <!-- valeurs des colonnes -->
                                                    <tr>
                                                        <td> <?= $item['titre_reun']; ?> </td>
                                                        <td> <?= $nomClub ?> </td>
                                                        <td> <?= $item['desc_reun']; ?> </td>
                                                        <td> <?= $item['date_reun']; ?> </td>
                                                        <td> <?= $item['heure_deb_reun']; ?> </td>
                                                        <td> <?= $item['heure_fin_reun']; ?> </td>
                                                        <td> <?= $nomSalle ?> <?= $numSalle ?> </td>
                                                        <?php
                                                        //check l'etat de la reunion
                                                        $etat = '';
                                                            if($item['reun_isApproved'] == 1 || $item['reun_isApproved'] == 3){$etat = 'Approuvée';}
                                                            else{
                                                                if($item['reun_isApproved'] == -1 || $item['reun_isApproved'] == -3){$etat = 'Non approuvée';}
                                                                else {$etat = "En attente";}
                                                            }
                                                        ?>
                                                        <td class="<?= ($etat == 'Approuvée')? "success" : (($etat == 'Non approuvée')?  "fail" : " ") ?>"> <?= $etat?> </td>
                                                        <td>
                                                            <!-- le boutton du bilan n'apparaitra qu'une fois la reunion est approuvee -->
                                                            <?php
                                                                if($etat == 'Approuvée')
                                                                {
                                                                    //id de la reunion demandee
                                                                    $bilan = false;
                                                                    $idReun = $item['id_reun'];
                                                                    $bilan_query = "SELECT * FROM bilan WHERE id_reun = '$idReun' ";
                                                                    $bilan_query_run = mysqli_query($con,$bilan_query);
                                                                    if(mysqli_num_rows($bilan_query_run) > 0) //si le bilan est deja ecrit
                                                                    {
                                                                        $bilan = true;
                                                                    }
                                                                    ?>
                                                                        <form action="bilan.php" method="POST">
                                                                            <input type="hidden" name="idReun" value="<?= $idReun ?>">
                                                                            <?php
                                                                                $date = $item['date_reun'];
                                                                                $heure_deb = $item['heure_deb_reun'];
                                                                                $heure_fin = $item['heure_fin_reun'];
                                                                                $current_date = date('Y-m-d');
                                                                                $current_time = date('H:i:s');
                                                                                if($date < $current_date || ($date == $current_date && $heure_fin < $current_time)) // reunion terminee
                                                                                {
                                                                                    ?>
                                                                                        <button type="submit" class="btn btn-secondary" id="<?= ($bilan == false)? 'bilan-non-fait' : 'bilan-fait' ?>" name="bilan_btn"><?= ($bilan == false)? 'Ecrire bilan' : 'Bilan' ?></button>
                                                                                    <?php
                                                                                }
                                                                                else //si non terminee
                                                                                {
                                                                                    ?>
                                                                                        <button type="submit" class="btn btn-secondary" id="bilan-non-fait" name="bilan_btn" disabled>'Ecrire bilan'</button>
                                                                                    <?php
                                                                                }
                                                                            ?>
                                                                        </form>
                                                                    <?php
                                                                }
                                                            ?>                                            
                                                        </td>
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

<style>
    .success{
        color: #37ab2c !important;
    }
    .fail{
        color: red !important;
    }
</style>

<?php include('../includes/footer.php'); ?>