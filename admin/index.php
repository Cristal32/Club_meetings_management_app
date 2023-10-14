<?php 
$current_page = basename($_SERVER['PHP_SELF'], ".php");
include('includes/header.php');
include('../config/dbcon.php');
include('../middleware/adminMiddleware.php');
?>

<style>
 .btn-link{
    background: none;
    border: none;
    padding: 0
 }
 .text-purple{
    color: purple;
    text-decoration: none
 }
 .text-purple:hover{
    color: purple;
    text-decoration: underline
 }
 .delete-btn{
    color: red;
    border-color: red
 }
</style>

<!-- Modal - fenetre qui demande si vous etes sur de vouloir supprimer le club -->
<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Supprimer club</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Etes-vous surs de vouloir supprimer ce club?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
        <form action="../functions/submitcode.php" method="POST">
            <input type="hidden" name="clubId" id="clubIdInput">
            <button type="submit" name="deleteClub_btn" class="btn btn-primary" id="confirmDelete_btn">Supprimer</button>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- Liste des clubs -->
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Liste des clubs</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped text-center align-middle">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Domaine</th>
                                <th>Date de création du club</th>
                                <th>Responsable</th>
                                <th>Supprimer club</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $club = getAll("club");

                                if(mysqli_num_rows($club) > 0)
                                {
                                    foreach($club as $item)
                                    {
                                        $idClub = $item['id_club'];
                                        //determiner le nom et les contacts du responsable de club
                                        //tout d'abord determiner le id du respo
                                        $respoId = null; $respoMembre = null;
                                        $respo_query = "SELECT * FROM responsable_club WHERE id_club = '$idClub' ";
                                        $respo_query_run = mysqli_query($con,$respo_query);
                                        if(mysqli_num_rows($respo_query_run) > 0)
                                        {
                                            $respo_data = mysqli_fetch_array($respo_query_run);
                                            $respoId = $respo_data['id_respo'];
                                            $respoMembre = $respo_data['id_membre'];
                                        }
                                        //ensuite, determiner les infos de l'e'eve correspondant a ce respo
                                        //respo --> membre 
                                        $membreCNE = null;
                                        $membre_query = "SELECT * FROM membre_club WHERE id_membre = '$respoMembre' ";
                                        $membre_query_run = mysqli_query($con,$membre_query);
                                        if(mysqli_num_rows($membre_query_run) > 0)
                                        {
                                            $membre_data = mysqli_fetch_array($membre_query_run);
                                            $membreCNE = $membre_data['cne'];
                                        }
                                        //membre --> eleve
                                        $eleveNom = null; $elevePrenom = null; $eleveEmail = null; $eleveTel = null;
                                        $eleve_query = "SELECT * FROM eleve WHERE cne = '$membreCNE' ";
                                        $eleve_query_run = mysqli_query($con,$eleve_query);
                                        if(mysqli_num_rows($eleve_query_run) > 0)
                                        {
                                            $eleve_data = mysqli_fetch_array($eleve_query_run);
                                            $eleveNom = $eleve_data['nom'];
                                            $elevePrenom = $eleve_data['prenom'];
                                            $eleveEmail = $eleve_data['email'];
                                            $eleveTel = $eleve_data['tel'];
                                        }

                                        ?>
                                            <tr>
                                                <!-- cliquer sur le nom d'un club nous envoie vers une page contenant la liste des membres de ce club -->
                                                <form action="membresClub.php" method="POST">
                                                    <input type="hidden" name="idClub" value="<?= $idClub ?>">
                                                    <td>
                                                        <button type="submit" name="adminClub_btn" class="btn btn-lg text-purple">
                                                            <?= $item['nom_club']; ?> 
                                                        </button>
                                                    </td>
                                                </form>
                                                <td> <?= $item['domaine']; ?> </td>
                                                <td> <?= $item['date_creation_club']; ?> </td>
                                                <!-- bouton du responsable du club, affiche ses contacts lorsqu'on le clique -->
                                                <td>
                                                    <button type="button" class="btn btn-lg text-purple respo-popover" data-bs-toggle="popover" title="Contact Info" data-email="<?= $eleveEmail; ?>" data-tel="<?= $eleveTel; ?>">
                                                        <?= $eleveNom; ?> <?= $elevePrenom; ?>
                                                    </button>
                                                    <!-- icone pour modifier le responsable de club 
                                                    <a href="#" class="editRespo" data-bs-toggle="modal" data-bs-target="#editRespoModal" data-id="</?= $idClub; ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </a> -->
                                                    <a href="#" class="editRespo" id="editRespoLink">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <!-- hidden div that allows you to select a new respo -->
                                                    <div id="select-club" class="d-none align-middle">
                                                        <form action="../functions/submitcode.php" method="POST">
                                                            <div class="col-md-6">
                                                                <label for="select-membre">Choisir un nouveau responsable:</label>
                                                                <select name="select_membre" id="select-membre" class="form-select">
                                                                    <?php
                                                                        $membre = "SELECT * FROM membre_club WHERE id_club = '$idClub' ";
                                                                        $membre_run = mysqli_query($con,$membre);
                                                                        if(mysqli_num_rows($membre_run) > 0)
                                                                        {
                                                                            foreach($membre_run as $item)
                                                                            {
                                                                                $nom = cneData($item['cne'])['nom'];
                                                                                $prenom = cneData($item['cne'])['prenom'];
                                                                                ?>
                                                                                    <option><?= $nom; ?> <?= $prenom; ?> <?= $item['cne']; ?></option>
                                                                                <?php
                                                                            }
                                                                        }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                                <button  type="submit" name="editRespo_btn" class="btn btn-sm custom-btn" id="confirmEditSalle_btn" value="<?= $idClub ?>">Modifier responsable</button>
                                                        </form>
                                                    </div>
                                                </td>
                                                <!-- bouton de suppression des clubs, fait appel a une fenetre modale d'abord -->
                                                <td>
                                                 <button type="button" class="delete-btn" data-bs-toggle="modal" data-bs-target="#myModal" data-clubId="<?= $idClub; ?>">
                                                    <i class="fas fa-times"></i>
                                                 </button>
                                                </td>
                                            </tr>
                                        <?php
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
    .custom-btn{
        border: 1px solid #a1b5d6 !important;
        top: 10px;
    }
</style>

<!-- creation de club -->
<div class="container mt-6">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Créer un nouveau club</h4>
                </div>
                <div class="card-body">
                    <form action="../functions/submitcode.php" method="POST">
                        <div class="mb-3">
                            <label for="inputNomClub" class="form-label">Nom du club</label>
                            <input id="inputNomClub" type="text" name="nom-club" placeholder="Entrer le nom du club" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="inputDomaineClub" class="form-label">Domaine</label>
                            <input id="inputDomaineClub" type="text" name="domaine-club" placeholder="Entrer le domaine du club" class="form-control">
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="submit" name="createClub_btn" class="btn btn-primary">Créer club</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- -------------------------------------------------footer et scripts--------------------------------------------------------------->
<?php include('includes/footer.php');?>

<script>
     //poper
     document.addEventListener("DOMContentLoaded", function() {
        var respoPopovers = document.querySelectorAll('.respo-popover');
        respoPopovers.forEach(function(popover) {
            var email = popover.getAttribute('data-email');
            var tel = popover.getAttribute('data-tel');
            var content = '<strong>Email:</strong> ' + email + '<br><strong>Tel:</strong> ' + tel;
            
            new bootstrap.Popover(popover, {
                content: content,
                html: true
            });
        });
    });
    //when you press delete club in modal
    document.addEventListener("DOMContentLoaded", function() {
        var modal = document.getElementById('myModal');
        modal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var clubId = button.getAttribute('data-clubId');
            var clubIdInput = modal.querySelector('#clubIdInput');
            clubIdInput.value = clubId;
        });
    });

    const editRespoLink = document.getElementById('editRespoLink');
    const SelectClub = document.getElementById('select-club');
    editRespoLink.addEventListener('click', function() {
        if (SelectClub.classList.contains('d-none')) {
            // Si oui, la supprimer pour afficher le formulaire
            SelectClub.classList.remove('d-none');
        } else {
            // Sinon, ajouter la classe "d-none" pour masquer le formulaire
            SelectClub.classList.add('d-none');
        }
    });
</script>


</script>
