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

//selectionner le responsable de ce club ----------------------------------------------------------------------------
$respoId = null;
$respo_query = "SELECT * from responsable_club WHERE id_club = '$membre_club' ";
$respo_query_run = mysqli_query($con,$respo_query);
if(mysqli_num_rows($respo_query_run) > 0) //respo existe
{
    $respo_data = mysqli_fetch_array($respo_query_run);
    $respoId = $respo_data['id_respo'];

    //recuperer le respo en tant qu'eleve
    $respoCNE = respoCNE($respoId);

    //selectionner les attributs de cet eleve
    $respoNom = cneData($respoCNE)['nom'];
    $respoPrenom = cneData($respoCNE)['prenom'];
    $respoEmail = cneData($respoCNE)['email'];
    $respoTel = cneData($respoCNE)['tel'];
}

// ----------------------------------------------------------------------------------------
?>

<!-- affichage des infos du club du membre -->
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Club: <?= $nomClub ?></h4>
                </div>
                <div class="card-body">
                    <form action="validateReject.php" method="POST">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center align-middle">Titre du club</th>
                                    <th class="text-center align-middle">Domaine</th>
                                    <th class="text-center align-middle">Responsable du club</th>
                                    <th class="text-center align-middle">Date de création du club</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center align-middle"> <?= $nomClub ?> </td>
                                    <td class="text-center align-middle"> <?= $domaineClub ?> </td>
                                    <td class="text-center align-middle">
                                        <button type="button" class="btn btn-lg text-purple respo-popover" data-bs-toggle="popover" title="Contact Info" data-email="<?= $respoEmail; ?>" data-tel="<?= $respoTel; ?>">
                                            <?= $respoNom; ?> <?= $respoPrenom; ?>
                                        </button>
                                    </td>
                                    <td class="text-center align-middle"> <?= $dateClub ?> </td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
     .text-purple{
        color: purple !important;
        text-decoration: none;
        font-size: 18px !important;
        
    }
    .text-purple:hover{
        color: purple;
        text-decoration: underline
    }
</style>

<!-- affichage des membres du club precis du membre en question -->
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Membres</h4>
                </div>
                <div class="card-body">
                    <form action="validateReject.php" method="POST">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>cne</th>
                                    <th>nom</th>
                                    <th>prenom</th>
                                    <th>date d'entree au club</th>
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
                                            if($item['id_club'] == $membre_club)
                                            {
                                                $cne = $item['cne'];
                                                //recuperation de l'eleve correspondant a chaque item de membre_club
                                                $eleves_query = "SELECT * FROM eleve WHERE cne = '$cne' ";
                                                $eleves_query_run = mysqli_query($con,$eleves_query);
                                                if(mysqli_num_rows($eleves_query_run) > 0)
                                                {
                                                    $eleves_data = mysqli_fetch_array($eleves_query_run);
                                                    $eleve_nom = $eleves_data['nom'];
                                                    $eleve_prenom = $eleves_data['prenom'];
                                                }
                                                ?>
                                                    <tr>
                                                        <td> <?= $item['cne'] ?> </td>
                                                        <td> <?= $eleve_nom ?> </td>
                                                        <td> <?= $eleve_prenom ?> </td>
                                                        <td> <?= $item['date_entree'] ?> </td>
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


<?php include('../includes/footer.php'); ?>

<script src="../admin/assets/js/bootstrap.bundle.min.js"></script>

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
</script>