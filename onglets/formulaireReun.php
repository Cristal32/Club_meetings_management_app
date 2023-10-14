<?php 
session_start();

$current_page = 'reunion';
include('../includes/respoHeader.php'); 
include('../config/dbcon.php');
include('../functions/myfunctions.php');

$auth_user = $_SESSION['auth_user'];

$user_query = "SELECT * FROM utilisateur WHERE login='$auth_user' ";
$user_query_run = mysqli_query($con, $user_query);
if(mysqli_num_rows($user_query_run) > 0)
{
    //fetch username
    $userdata = mysqli_fetch_array($user_query_run);
    $name = $userdata['login'];
    $idEleve = $userdata['id_ifEleve'];
    $_SESSION['auth_user'] = $name;

    if($idEleve != null)
    {
        $_SESSION['eleve'] = $idEleve;

        //fetch membres
        $membre_query = "SELECT * FROM membre_club WHERE cne='$idEleve' ";
        $membre_query_run = mysqli_query($con, $membre_query);

        if(mysqli_num_rows($membre_query_run) > 0)
        {
            //fetch membre
            $membredata = mysqli_fetch_array($membre_query_run);
            $idmembre = $membredata['id_membre'];
            $id_Club = $membredata['id_club'];

            //tirer le nom du club
            $Club_query = "SELECT * FROM club WHERE id_club='$id_Club' ";
            $Club_query_run = mysqli_query($con, $Club_query);

            if(mysqli_num_rows($Club_query_run) > 0)
            {
                $clubdata = mysqli_fetch_array($Club_query_run);
                $nomClub = $clubdata['nom_club'];

                $_SESSION['membreInfo'] = [
                    'id_club' => $id_Club,
                    'nom_club' => $nomClub
                ];
            }
        }

    }
}
?>

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
                <div class="card">
                    <div class="card-header">
                        <h4>Formulaire de demande de réunion</h4>
                    </div>
                    <div class="card-body">
                        <form action="../functions/submitcode.php" method="POST" onsubmit="return validateForm()">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="titre-reun">Titre de la réunion</label>
                                    <input type="text" name="titre-reunion" placeholder="Entrer le titre de la reunion" class="form-control" id="titre-reun">
                                </div>
                                <div class="col-md-6">
                                    <div>Nom du club</div>
                                    <div>
                                        <?= $_SESSION['membreInfo']['nom_club']; ?>
                                    </div>  
                                </div>
                                <div><br></div>
                                <div class="col-md-12">
                                    <label for="desc-reun">Description de la réunion</label>
                                    <textarea row="3" name="desc-reunion" placeholder="Entrer la description de la reunion" class="form-control" id="desc-reun"></textarea>
                                </div>
                                <div><br></div>
                                <div class="col-md-6">
                                    <label for="date-reun">Date de la réunion</label>
                                    <input type="date" name="date-reunion" placeholder="Entrer la date de reunion" class="form-control" id="date-reun">
                                </div>
                                 <!-- selection de la salle -->
                                 <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="salle_selection">Salle</label>
                                        <div class="input-group">
                                            <select class="form-control" id="salle_selection" name="salle">
                                            <?php
                                                $salle_query = 'SELECT * FROM salle ORDER BY nom_salle,num_salle';
                                                $salle_query_run = mysqli_query($con,$salle_query);
                                                if(mysqli_num_rows($salle_query_run) > 0)
                                                {
                                                    foreach($salle_query_run as $item)
                                                    {
                                                        ?>
                                                            <option><?= $item['nom_salle']; ?> <?= $item['num_salle']; ?></option>
                                                        <?php
                                                    }
                                                }
                                            ?>
                                            </select>
                                            <span class="input-group-text"><i class="bi bi-caret-down-square-fill"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="heure-deb">Heure de début de la réunion</label>
                                    <input type="time" name="heure-debut-reunion" placeholder="Entrer l'heure de debut de la reunion" class="form-control" id="heure-deb">
                                </div>
                                <div class="col-md-6">
                                    <label for="heure-fin">Heure de fin de la réunion</label>
                                    <input type="time" name="heure-fin-reunion" placeholder="Entrer l'heure de fin de la reunion" class="form-control" id="heure-fin">
                                </div>
                                <div><br></div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-danger" name="reunion_btn" id="submit-btn">Soumettre</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<?php include('../includes/footer.php'); ?>

<script>
    function validateForm() {
        var startTime = document.getElementById("heure-deb").value;
        var endTime = document.getElementById("heure-fin").value;

        if (startTime >= endTime) {
            var errorMessage = document.querySelector(".error-message");
            if (!errorMessage) {
                errorMessage = document.createElement("p");
                errorMessage.className = "error-message";
                errorMessage.style.color = "red";
                var formBody = document.querySelector(".row");
                formBody.appendChild(errorMessage);
            }

            errorMessage.innerHTML = "L'heure de début doit être inférieure à l'heure de fin!";

            var submitButton = document.getElementById("submit-btn");
            submitButton.disabled = true;

            var formBody = document.querySelector(".row");
            formBody.appendChild(errorMessage);
            submitButton.disabled = false;

            return false; // Prevent form submission
        }

        return true; // Allow form submission
    }
</script>