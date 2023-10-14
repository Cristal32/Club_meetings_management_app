<?php 
session_start();
include('../includes/respoHeader.php');

//echo ($_SESSION['membre'] == true)? 'membre' : 'pas membre';
//echo ($_SESSION['respo'] == true)? 'respo' : 'pas respo';
?>

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
            <h1>Bienvenu, <?= $_SESSION['auth_user']; ?></h1>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>