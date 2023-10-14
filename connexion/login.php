<?php 
session_start();

if(isset($_SESSION['auth']))
{
    $_SESSION['message'] = "You are already logged in!";
    header('Location: ../membre/index.php');
}

include('../includes/header.php'); ?>

<div class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
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
                        <h4>Connexion</h4>
                    </div>
                    <div class="card-body">
                        <form action="../functions/authcode.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Nom d'utilisateur</label>
                                <input type="text" name="username" class="form-control" placeholder="Enter your name">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mot de passe</label>
                                <input type="password" name="password" class="form-control" placeholder="Enter your password" id="exempleInputPassword1">
                            </div>
                            <button type="submit" name="login_btn" class="btn btn-primary">Se connecter</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>