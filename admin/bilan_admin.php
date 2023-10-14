<?php 
$current_page = "reunions_approuvees";
include('includes/header.php');
include('../middleware/adminMiddleware.php');
include('../config/dbcon.php');

$idReun = null;
if(isset($_POST['bilan_admin_btn']))
{
    $idReun = $_POST['idReun'];
}

//recuperation du titre de la reunion dont on visualise le bilan
$reun_titre = null;
$reun_query = "SELECT * FROM reunion WHERE id_reun = '$idReun' ";
$reun_query_run = mysqli_query($con,$reun_query);
if(mysqli_num_rows($reun_query_run) > 0)
{
    $reun_data = mysqli_fetch_array($reun_query_run);
    $reun_titre = $reun_data['titre_reun'];
}

//recuperation du bilan dont le id_reun est le meme que celui partage par reunions_acceptees
$bilan_isSubmitted = 0;
$bilan_query = "SELECT * FROM bilan WHERE id_reun = '$idReun' ";
$bilan_query_run = mysqli_query($con,$bilan_query);
if(mysqli_num_rows($bilan_query_run) > 0)
{
    $bilan_data = mysqli_fetch_array($bilan_query_run);
    $bilan_isSubmitted = 1;
    $bilan_msg = $bilan_data['msg_bilan'];
}
?>

<!-- affichage du bilan -->
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Bilan de: <?= $reun_titre ?></h4>
                </div>
                <div class="card-body">
                    <div>
                        <?php
                            if($bilan_isSubmitted == 1)
                            {
                                echo $bilan_msg;
                            }
                            else
                            {
                                echo "bilan non encore soumis";
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
