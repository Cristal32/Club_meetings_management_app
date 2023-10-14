<?php 
$current_page = basename($_SERVER['PHP_SELF'], ".php");
include('includes/header.php');
include('../middleware/adminMiddleware.php');
include('../config/dbcon.php');
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Réunions rejetées</h4>
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
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $dem_reun = getAll("reunion");

                                    if(mysqli_num_rows($dem_reun) > 0)
                                    {
                                        foreach($dem_reun as $item)
                                        {
                                            if($item['reun_isApproved']==-1)
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
                                                        <td> <?= $nomClub ?> </td>
                                                        <td class="text-wrap"> <?= $item['desc_reun']; ?> </td>
                                                        <td class="text-center align-middle"> <?= $item['date_reun']; ?> </td>
                                                        <td class="text-center align-middle"> <?= $item['heure_deb_reun']; ?> </td>
                                                        <td class="text-center align-middle"> <?= $item['heure_fin_reun']; ?> </td>
                                                        <td class="text-center align-middle"> <?= $nomSalle ?> <?= $numSalle ?> </td>
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
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php');?>