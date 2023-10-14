<?php 
session_start();
$logout = ''; 
if(isset($_GET['state']))
{
    $logout = $_GET['state'];
}
if($logout == 'logout')
{
    unset($_SESSION['admin']);
    unset($_SESSION['membre']);
    unset($_SESSION['respo']);
}
if(isset($_SESSION['respo']) && $_SESSION['respo'] == true)
{
    include('../includes/respoHeader.php'); 
    //echo 'respoHeader';
}
else
{
    include('../includes/header.php'); 
    //echo 'header';
}
include('../functions/myfunctions.php');
include('../config/dbcon.php');

//echo ($_SESSION['membre'] == true)? 'membre' : 'pas membre';
//echo ($_SESSION['respo'] == true)? 'respo' : 'pas respo';
//echo (isset($_SESSION['admin']))?'admin':'not admin';

?>


<!-- liste des reunions planifiees -->
<div class="py-5">
    <div class="container">
        <div class="row">
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
                <h1 style="color: #CC0000;">Bienvenu à la plateforme ENSIAS Clubs </h1>
                <br><br><br>
                <div class="card">
                    <div class="card-header">
                        <h3><a href="#" id="reuns-link" style="color: #a83a32;">Réunions planifiées pour cette semaine:</a></h3>
                    </div>
                    <div class="card-body">
                        <div id="reuns-container" class="collapse">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Titre</th>
                                        <th>Club</th>
                                        <th>Description</th>
                                        <th>Date</th>
                                        <th>Heure de debut</th>
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
                                                if($item['reun_isApproved']=='1')
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
                                                                    $etat = 'Terminee';
                                                                    ?>
                                                                        <td><?= $etat; ?></td>
                                                                        <td>
                                                                            <form action="../onglets/bilan.php?" method="POST">
                                                                                <input type="hidden" name="idReun" value="<?= $idReun ?>">
                                                                                <button type="submit" class="btn btn-secondary" name="bilan_btn" value="<?= $idReun; ?>">Bilan</button>   
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
</div>

<!--
<div id="myCarousel" class="carousel slide" data-ride="carousel">
  <!- Wrapper for slides ->
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="../imgs/logo_ensias.png" class="logo-img" alt="Slide 1">
      <div class="carousel-caption">
        <h5 class="car-item">Slide 1 Heading</h5>
        <p class='car-item'>Slide 1 description</p>
      </div>
    </div>
    <div class="carousel-item">
      <img src="../imgs/logo_ensias.png" class="logo-img" alt="Slide 2">
      <div class="carousel-caption">
        <h5 class='car-item'>Slide 2 Heading</h5>
        <p class='car-item'>Slide 2 description</p>
      </div>
    </div>
  </div>
</div> -->

<div id="myCarousel" class="carousel carousel-dark slide c-item">
    <div class="carousel-indicators" style="bottom:-50px;">
        <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="eitc"></button>
        <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="1" aria-label="cindh"></button>
        <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="2" aria-label="club_quran 3"></button>
  </div>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <div class="img-container">
        <img src="../imgs/eitc.png" class="slide-img" alt="eitc">
        <img src="../imgs/insec.PNG" class="slide-img" alt="insec">
        <img src="../imgs/ieee.png" class="slide-img" alt="ieee">
      </div>
    </div>
    <div class="carousel-item">
        <div class="img-container">
            <img src="../imgs/cindh.PNG" class="slide-img" alt="cindh">
            <img src="../imgs/bridge.png" class="slide-img" alt="bridge">
            <img src="../imgs/enactus.png" class="slide-img" alt="enactus">
        </div>
    </div>
    <div class="carousel-item">
        <div class="img-container">
            <img src="../imgs/club_quran.png" class="slide-img" alt="club_quran">
            <img src="../imgs/olympiades.png" class="slide-img" alt="olympiades">
            <img src="../imgs/photography.png" class="slide-img" alt="photography">
        </div>
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#myCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#myCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>

<style>
    .c-item{
        border: 1px solid #dce7fa; /*background color of the caroussel */
    }
    .slide-img{ /* adjusting the size and position of the carousel images */
        width: 180px ;
        height: 200px;
        margin: 20px auto 20px auto;
    }
    .img-container { /*making the images be in the aligned center */
        display: flex;
        justify-content: center;
    }
</style>


<?php include('../includes/footer.php'); ?>
    
<!-- affiche les reunions planifiees lorsqu'on clique sur le lien -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  var link = document.getElementById('reuns-link');
  var container = document.getElementById('reuns-container');

  link.addEventListener('click', function(e) {
    e.preventDefault();
    $(container).collapse('toggle');
    //container.style.display = (container.style.display === 'none') ? 'block' : 'none';
  });
});

//carousel des clubs

  $(document).ready(function() {
    $('#myCarousel').carousel();
  });
</script>
