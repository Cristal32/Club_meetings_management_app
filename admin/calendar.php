<?php 
$current_page = basename($_SERVER['PHP_SELF'], ".php");
include('includes/header.php');
include('../middleware/adminMiddleware.php');
include('../config/dbcon.php');
?>

<!-- liste des reunions planifiees -->
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Réunions planifiées de la semaine</h4>
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
                                <th>Etat</th>
                                <th> </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $dem_reun = getAll("reunion");

                                if(mysqli_num_rows($dem_reun) > 0)
                                {
                                    foreach($dem_reun as $item)
                                    {
                                        //reun_isApproved = 1 --> approuvee, = 2 --> approuvee et partagee dans la page d'accueil
                                        if($item['reun_isApproved'] == '1')
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
                                                    <td class="text-wrap col-2"> <?= $item['titre_reun']; ?> </td>
                                                    <td> <?= $nomClub ?> </td>
                                                    <td class="text-wrap col-2"> <?= $item['desc_reun']; ?> </td>
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
                                                            $etat = 'Terminée';
                                                            ?>
                                                                <td><?= $etat; ?></td>
                                                                <td>
                                                                    <form action="bilan_admin.php" method="POST">
                                                                        <input type="hidden" name="idReun" value="<?= $idReun ?>">
                                                                        <button type="submit" class="btn btn-secondary" name="bilan_admin_btn" value="<?= $idReun; ?>">Bilan</button>   
                                                                    </form>
                                                                </td>
                                                            <?php
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
                                                                $etat = 'Planifiée';
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
                    <form action="../functions/submitcode.php" method="POST">
                        <div class="d-flex justify-content-between mt-4">
                            <!--<button type="submit" class="btn btn-success" name="sendtoAccueil_btn">Partager dans la page d'accueil</button>-->
                            <button type="submit" class="btn btn-primary" name="archive_btn">Archiver</button>   
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- liste des salles -->
<div class="container mt-6">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Salles</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th class="text-center">Nom salle</th>
                                <th class="text-center">Numero salle</th>
                                <th class="text-center">Statut</th>
                                <th class="text-center">Réunion</th>
                                <th class="text-center">Date</th>
                                <th class="text-center">Heure de début</th>
                                <th class="text-center">Heure de fin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $salle = getAll('salle');
                                if(mysqli_num_rows($salle) > 0)
                                {
                                    foreach($salle as $item)
                                    {
                                        $idSalle = $item['id_salle'];
                                        ?>
                                            <tr>
                                                <td class="text-center"><?= $item['nom_salle']; ?></td>
                                                <td class="text-center"><?= $item['num_salle']; ?></td>
                                                <td class="<?= ($item['salle_isFree'] == '0')? 'text-success' : 'text-danger' ?> text-center">
                                                    <?= ($item['salle_isFree'] == '0')? 'Libre' : 'Reservee'; ?>
                                                </td>

                                                <?php
                                                    //fetch reunion de la salle occupee
                                                    if($item['salle_isFree'] == '1')
                                                    {
                                                        $reunTitre = null;
                                                        $reun_query = "SELECT * FROM reunion WHERE id_salle = '$idSalle' AND reun_isApproved = '1' OR reun_isApproved = '2' ";
                                                        $reun_query_run = mysqli_query($con,$reun_query);
                                                        if(mysqli_num_rows($reun_query_run) > 0)
                                                        {
                                                            $reun_data = mysqli_fetch_array($reun_query_run);
                                                            $reunTitre = $reun_data['titre_reun'];
                                                            $reunDate = $reun_data['date_reun'];
                                                            $reunHeureDeb = $reun_data['heure_deb_reun'];
                                                            $reunHeureFin = $reun_data['heure_fin_reun'];
                                                        }
                                                        ?>
                                                            <td class="text-wrap text-center"><?= $reunTitre ?></td>
                                                            <td class="text-center"><?= $reunDate ?></td>
                                                            <td class="text-center"><?= $reunHeureDeb ?></td>
                                                            <td class="text-center"><?= $reunHeureFin ?></td>
                                                            
                                                        <?php
                                                    }
                                                    else
                                                    {
                                                        ?>
                                                            <td> </td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                        <?php
                                                    }
                                                ?>
                                            </tr>
                                        <?php
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php');?>