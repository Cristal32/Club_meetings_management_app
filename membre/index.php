<?php 
session_start();

$current_page = basename($_SERVER['PHP_SELF'], ".php");

include('../includes/header.php'); 
include('../config/dbcon.php');
include('../functions/myfunctions.php'); 
//echo ($_SESSION['membre'] == true)? 'membre' : 'pas membre';
//echo ($_SESSION['respo'] == true)? 'respo' : 'pas respo';

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
//echo $_SERVER['PHP_SELF'];
?>

<!-- affichage des reunions approuvees du club du membre -->
<div class="py-5">
    <div class="container">
        <div class="col-md-12">
            <?php
                if(isset($_SESSION['message']))
                {
                    ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>Hey!</strong> <?= $_SESSION['message']; ?>.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php
                    unset($_SESSION['message']);
                }
            ?>
            <h1>Bienvenu, <?= $_SESSION['auth_user'] ?></h1>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>