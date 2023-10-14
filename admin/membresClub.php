<?php 
$current_page = 'index';
include('includes/header.php');
include('../config/dbcon.php');
include('../middleware/adminMiddleware.php');

$idClub = null; $clubNom = null;
if(isset($_POST['adminClub_btn']))
{
    $idClub = $_POST['idClub'];
    $clubNom = null;
    $club_query = "SELECT * FROM club WHERE id_club = '$idClub' ";
    $club_query_run = mysqli_query($con,$club_query);
    if(mysqli_num_rows($club_query_run) > 0)
    {
        $club_data = mysqli_fetch_array($club_query_run);
        $clubNom = $club_data['nom_club'];
    }
}
//si on rafraichit la page, $idClub n'est plus definie car elle m'est definie que lorsqu'on clique sur le bouton qui transmet cette infornation, 
//donc insetad ce qu'on fait c'est que dans submitcode.php lorsque ca ajoute l'eleve et que ca redirige l'admin vers cette page, il transfere le id du club a travers l'URL
if($idClub == null)
{
    $idClub = $_GET['idClub'];
    $club_query = "SELECT * FROM club WHERE id_club = '$idClub' ";
    $club_query_run = mysqli_query($con,$club_query);
    if(mysqli_num_rows($club_query_run) > 0)
    {
        $club_data = mysqli_fetch_array($club_query_run);
        $clubNom = $club_data['nom_club'];
    }
}
?>

<style>
    .clickable-select{
        cursor: pointer;
        padding: 20px;
    }
    .clickable-select:hover {
    background-color: #f7f7f7;
    }

    .select-wrapper {
        position: relative;
        display: inline-block;
        width: 100%;
    }

    .select-wrapper::after {
        content: '\25BE';
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        pointer-events: none;
    }
    .retirerEleve-btn{
    color: red;
    border-color: red
    }
</style>

<!-- Modal - fenetre qui demande si vous etes sur de vouloir retirer l'eleve du club -->
<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="h5-nomEleve"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p id="membreP"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
        <form action="../functions/submitcode.php" method="POST">
            <input type="hidden" id="confirmDeleteEleve_input" name="cne_input">
            <button type="submit" name="retirerEleve_btn" class="btn btn-primary" value="<?= $idClub; ?>">Retirer du club</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- afficher les membres de ce club -->
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Membres de <?= $clubNom; ?></h4>
                </div>
                <div class="card-body">
                    <form action="validateReject.php" method="POST">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>cne</th>
                                    <th>nom</th>
                                    <th>prenom</th>
                                    <th>Email</th>
                                    <th>Tel</th>
                                    <th>statut</th>
                                    <th>date d'entree au club</th>
                                    <th> </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    //recuperation de tous les membres du club precis
                                    $membres = getAll('membre_club');
                                    if(mysqli_num_rows($membres) > 0)
                                    {
                                        foreach($membres as $item)
                                        {
                                            if($item['id_club'] == $idClub)
                                            {
                                                $cne = $item['cne'];
                                                //recuperation de l'eleve correspondant a chaque item de membre_club
                                                $eleve_nom = null; $eleve_prenom = null; $eleve_email = null; $eleve_tel = null;
                                                $eleves_query = "SELECT * FROM eleve WHERE cne = '$cne' ";
                                                $eleves_query_run = mysqli_query($con,$eleves_query);
                                                if(mysqli_num_rows($eleves_query_run) > 0)
                                                {
                                                    $eleves_data = mysqli_fetch_array($eleves_query_run);
                                                    $eleve_nom = $eleves_data['nom'];
                                                    $eleve_prenom = $eleves_data['prenom'];
                                                    $eleve_email = $eleves_data['email'];
                                                    $eleve_tel = $eleves_data['tel'];
                                                }
                                                $statut = 0; //par defaut 0: membre
                                                //check si c'est un responsable
                                                $idMembre = $item['id_membre'];
                                                $respo_query = "SELECT * FROM responsable_club WHERE id_membre = '$idMembre' ";
                                                $respo_query_run = mysqli_query($con,$respo_query);
                                                if(mysqli_num_rows($respo_query_run) > 0) //si c'est un respo
                                                {
                                                    $statut = 1; //1: respo
                                                }
                                                ?>
                                                    <tr>
                                                        <td> <?= $item['cne'] ?> </td>
                                                        <td> <?= $eleve_nom ?> </td>
                                                        <td> <?= $eleve_prenom ?> </td>
                                                        <td> <?= $eleve_email ?> </td>
                                                        <td> <?= $eleve_tel ?> </td>
                                                        <td> <?= ($statut == 0)? 'membre' : 'respo' ?> </td>
                                                        <td> <?= $item['date_entree'] ?> </td>
                                                        <!-- bouton pour retirer un membre, fait appel a une fenetre modale d'abord -->
                                                        <td class="text-center align-middle">
                                                            <button type="button" class="retirerEleve-btn" data-bs-toggle="modal" data-bs-target="#myModal" data-membre="<?= $eleve_nom.' '.$eleve_prenom; ?>" data-cne="<?= $item['cne']; ?>">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php
                                            }
                                        }
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

<!-- ajout de membre -->
<div class="container mt-6">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Ajouter un membre</h4>
                     <!-- <div id="test"></div> -->
                </div>
                <div class="card-body">
                    <button id="ajout-eleve-btn">Ajouter un élève</button>
                    <form id="form-eleve" class="d-none" action="../functions/submitcode.php" method="POST">
                        <div class="mb-3 clickable-select">
                            <div class="col-md-6">
                                <label for="select-eleve">Sélectioner un élève (ne peut pas être déjà un membre de club)</label>
                                <select name="nom-eleve" id="select-eleve" class="form-control">
                                    <option disabled selected> Choisir un élève </option>
                                    <?php
                                        $eleve = 'SELECT * FROM eleve WHERE cne NOT IN (SELECT cne FROM membre_club)';
                                        $eleve_run = mysqli_query($con,$eleve);
                                        if(mysqli_num_rows($eleve_run) > 0)
                                        {
                                            foreach($eleve_run as $item)
                                            {
                                                //check si l'eleve est un utilisateur
                                                $isUser = 'Non_utilisateur';
                                                $CNE = $item['cne'];
                                                $recherche_user_query = "SELECT * FROM utilisateur WHERE id_ifEleve = '$CNE' ";
                                                $recherche_user_query_run = mysqli_query($con,$recherche_user_query);
                                                if(mysqli_num_rows($recherche_user_query_run) > 0) //s'il est un utilisateur
                                                {
                                                    $isUser = 'Utilisateur';
                                                }
                                                ?>
                                                    <option value="<?= $CNE; ?>"><?= $item['nom']; ?> <?= $item['prenom']; ?> <?= $item['cne']; ?> <?= $isUser; ?></option>
                                                <?php
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="d-none" id="if-no-account">
                                <div class="mb-3">
                                    <label class="form-label">Nom d'utilisateur</label>
                                    <div id="cne-username"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="admin_input_password" class="form-label">Mot de passe</label>
                                    <input type="text" name="password" class="form-control" placeholder="Entrer le nouveau mot de passe" id="input_password">
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="submit" name="addEleve_btn" id="addEleve_btn" class="btn btn-primary" value=<?= $idClub; ?>>Ajouter élève à <?= $clubNom; ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    //add eleve toggle
    document.getElementById('select-eleve').addEventListener('change',function(){
        var selectedOption = this.options[this.selectedIndex];
        var selectedText = selectedOption.text;
        var selectedValue = selectedOption.value; // value d'une option est le cne de l'eleve

        //check si c'est un utilisateur
        var selectedTextWords = selectedText.split(' ');
        var isUser = selectedTextWords[selectedTextWords.length - 1]; //last word is the $isUser variable
        
        //document.getElementById('test').textContent = selectedValue;

        if(isUser == 'Non_utilisateur')
        {
            document.getElementById('cne-username').textContent = selectedValue;
            document.getElementById('if-no-account').classList.remove('d-none');
        }
        else {document.getElementById('if-no-account').classList.add('d-none');}
    });

    //affiche le form des qu'on clique sur le bouton ajouter eleve
    const btnAjoutEleve = document.getElementById('ajout-eleve-btn');
    const formEleve = document.getElementById('form-eleve');
    // Ajouter un gestionnaire d'événements pour le clic sur le bouton
    btnAjoutEleve.addEventListener('click', function() {
        // Vérifier si la classe "d-none" est présente sur le formulaire
        if (formEleve.classList.contains('d-none')) {
            // Si oui, la supprimer pour afficher le formulaire
            formEleve.classList.remove('d-none');
        } else {
            // Sinon, ajouter la classe "d-none" pour masquer le formulaire
            formEleve.classList.add('d-none');
        }
    });

    //retirer un eleve du club dans modal
    document.addEventListener("DOMContentLoaded", function() {
        var modal = document.getElementById('myModal');
        modal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var membre = button.getAttribute('data-membre');
            var cne = button.getAttribute('data-cne');
            var membreP = modal.querySelector('#membreP');
            var cneInput = modal.querySelector('#confirmDeleteEleve_input');
            var h5 = modal.querySelector('#h5-nomEleve');
            membreP.textContent = 'Etes-vous surs de vouloir retirer '+ membre +' de ce club?';
            cneInput.value = cne;
            var clubNom = '<?= $clubNom; ?>';
            h5.textContent = 'Retirer eleve de ' + clubNom;
        });
    });
</script>

<?php include('includes/footer.php');?>