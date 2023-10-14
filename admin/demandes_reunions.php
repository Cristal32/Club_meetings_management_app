<?php 
$current_page = basename($_SERVER['PHP_SELF'], ".php");
include('includes/header.php');
include('../middleware/adminMiddleware.php');
include('../config/dbcon.php');
?>

<!-- modal pour changer la salle -->
<!-- -->
<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modifier salle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Etes-vous surs de vouloir modifier la salle?</p>
        <!-- dropdowns de la salle -->
        <form action="../functions/submitcode.php" method="POST">
            <div class="col-md-6">
                <label for="select-nom-salle">Nom de la salle</label>
                <select name="nom-salle" id="select-nom-salle" class="form-control">
                    <?php
                        $salleNom = 'SELECT DISTINCT nom_salle FROM salle';
                        $salleNom_run = mysqli_query($con,$salleNom);
                        if(mysqli_num_rows($salleNom_run) > 0)
                        {
                            foreach($salleNom_run as $item)
                            {
                                ?>
                                    <option value="<?= $item['nom_salle'] ?>"><?= $item['nom_salle']; ?></option>
                                <?php
                            }
                        }
                    ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="select-num-salle">Numéro de la salle</label>
                    <select name="num-salle" id="select-num-salle" class="form-control">
                        <?php
                            $salleNum = 'SELECT DISTINCT num_salle FROM salle';
                            $salleNum_run = mysqli_query($con,$salleNum);
                            if(mysqli_num_rows($salleNum_run) > 0)
                            {
                                foreach($salleNum_run as $item)
                                {
                                    ?>
                                        <option value="<?= $item['num_salle'] ?>"><?= $item['num_salle']; ?></option>
                                    <?php
                                }
                            }
                        ?>
                    </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="submit" name="editSalle_btn" class="btn btn-primary" id="confirmEditSalle_btn">Modifier salle</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- affichage des demandes de reunion -->
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Demandes de réunions</h4>
                </div>
                <div class="card-body">
                    <form action="validateReject.php" method="POST">
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
                                    <th></th>
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
                                            if($item['reun_isApproved']==0)
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
                                                    <!-- valeurs des colonnes -->
                                                    <tr>
                                                        <td class="text-wrap"> <?= $item['titre_reun']; ?> </td>
                                                        <td class="text-wrap"> <?= $nomClub ?> </td>
                                                        <td class="text-wrap"> <?= $item['desc_reun']; ?> </td>
                                                        <td class="text-wrap col-1"> <?= $item['date_reun']; ?> </td>
                                                        <td class="text-wrap"> <?= $item['heure_deb_reun']; ?> </td>
                                                        <td class="text-wrap"> <?= $item['heure_fin_reun']; ?> </td>
                                                        <td class="text-wrap"> 
                                                            <div class="editable-cell"><?= $nomSalle ?> <?= $numSalle ?></div>
                                                            <!-- bouton de edit salle transfere idReun vers le bouton de la fenetre modale -->
                                                            <a href="#" class="editSalle" data-bs-toggle="modal" data-bs-target="#myModal" data-id="<?= $idReun; ?>">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        </td> 
                                                        <td> 
                                                            <button type="submit" class="btn btn-success" name="validate_btn" value="<?= $idReun; ?>">Approuver</button>                                                 
                                                        </td>
                                                        <td> 
                                                            <button type="submit" class="btn btn-primary" name="reject_btn" value="<?= $idReun; ?>">Rejeter</button>                                                 
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
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php');?>

<!-- si on clique sur editer salle -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var modal = document.getElementById('myModal');
        modal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var idReun = button.getAttribute('data-id')
            var editSalleBtn = modal.querySelector('#confirmEditSalle_btn');
            editSalleBtn.value = idReun;
        });
    });
</script>

