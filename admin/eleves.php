<?php 
$current_page = basename($_SERVER['PHP_SELF'], ".php");
include('includes/header.php');
include('../config/dbcon.php');
include('../middleware/adminMiddleware.php');
?>

<style>
    .custom-width{
        width: 20%;
        position: relative;
        display: flex;
        min-width: 100px;
    }
</style>

<!-- Liste des eleves -->
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Liste des élèves</h4>
                </div>
                <div class="card-body">
                    <!------------- search bar---------------- -->
                    <div class="input-group mb-3 input-group-outline custom-width">
                        <input type="text" id="searchInput" class="form-control" placeholder="Rechercher..." style="margin-left: 10px;">
                        <div class="input-group-prepend d-flex align-items-center">
                            <span class="input-group-text">
                                <button class="btn btn-outline-primary" type="button" style="position: relative; top: 8px;">
                                    <i class="bi bi-search text-primary"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                    <!-- -------------------------------------- -->
                    <table class="table table-bordered table-striped text-center align-middle" id="table-eleve">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>CNE</th>
                                <th>Email</th>
                                <th>Tel</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $eleve = getAll("eleve");

                                if(mysqli_num_rows($eleve) > 0)
                                {
                                    foreach($eleve as $item)
                                    {
                                        ?>
                                            <tr>
                                                <td><?= $item['nom']; ?></td>
                                                <td><?= $item['prenom']; ?></td>
                                                <td><?= $item['cne']; ?></td>
                                                <td><?= $item['email']; ?></td>
                                                <td><?= $item['tel']; ?></td>
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