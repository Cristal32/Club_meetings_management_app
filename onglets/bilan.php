<?php 
session_start(); 

if($_SESSION['respo'] == true)
{
    include('../includes/respoHeader.php'); 
}
else
{
    include('../includes/header.php'); 
}
include('../functions/myfunctions.php');
include('../config/dbcon.php');

$idReun = null;

if(isset($_POST['bilan_btn']))
{
    $idReun = $_POST['idReun'];

    //recuperation du titre de la reunion dont on visualise le bilan
    $reun_titre = null;
    $reun_query = "SELECT * FROM reunion WHERE id_reun = '$idReun' ";
    $reun_query_run = mysqli_query($con,$reun_query);
    if(mysqli_num_rows($reun_query_run) > 0)
    {
        $reun_data = mysqli_fetch_array($reun_query_run);
        $reun_titre = $reun_data['titre_reun'];
    }

     //recuperation du bilan dont le id_reun est le meme que celui partage par reunion.php
     $bilan_query = "SELECT * FROM bilan WHERE id_reun = '$idReun' ";
     $bilan_query_run = mysqli_query($con,$bilan_query);
     if(mysqli_num_rows($bilan_query_run) > 0 ) //si le bilan existe
     {
         $bilan_data = mysqli_fetch_array($bilan_query_run);
         $bilan_msg = $bilan_data['msg_bilan'];
         ?>
             <div class="mt-4">
                 <div class="card">
                     <div class="card-header">
                         <h4>Bilan de la réunion: <?= $reun_titre ?></h4>
                     </div>
                     <div class="card-body">
                         <div>
                             <?php echo $bilan_msg ?>
                         </div>
                     </div>
                 </div>
             </div>
         <?php
    }
    else
    {
        ?>
            <div class="py-5" id="formulaire">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Bilan de la réunion : </h4>
                                </div>
                                <div class="card-body">
                                    <form action="../functions/submitcode.php" method="POST">
                                        <div class="mb-3">
                                            <label class="form-label">Entrez le bilan de la réunion</label>
                                            <textarea rows="15" name="bilan_txt" class="form-control"></textarea>
                                        </div>
                                        <!-- hidden input containing the value of id reunion -->
                                        <input type="hidden" name="idReun" value="<?= $idReun ?>" >
                                        <div class="d-flex justify-content-between">
                                            <button type="submit" name="bilan_btn" class="btn btn-primary">Soumettre bilan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
    }
}



include('../includes/footer.php'); ?>