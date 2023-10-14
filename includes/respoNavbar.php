<nav class="navbar navbar-expand-lg"> <!-- navbar-light bg-light -->
  <div class="container">
  <img src="../imgs/logo_ensias.png" alt="ENSIAS logo" class="logo-img">
    <a class="navbar-brand custom-nav-item" href="../accueil/index.php" id="ensias">ENSIAS clubs</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link custom-nav-item <?= ($_SERVER['PHP_SELF'] == '/gestion_reunions_clubs/accueil/index.php') ? 'active' : ''; ?>" aria-current="page" href="../accueil/index.php?state=loggedin">Accueil</a>
        </li>
        <li class="nav-item">
          <a class="nav-link custom-nav-item <?= ($current_page == 'reunions_planifiees_club') ? 'active' : ''; ?>" href="../onglets/reunions_planifiees_club.php">Planning des réunions</a>
        </li>

        <?php
          if(isset($_SESSION['auth']))
          {
        ?>
            <li class="nav-item">
            <a class="nav-link custom-nav-item <?= ($current_page == 'club') ? 'active' : ''; ?>" href="../onglets/club.php">Clubs</a>
            </li>

            <li class="nav-item">
                <a class="nav-link custom-nav-item <?= ($current_page == 'reunion') ? 'active' : ''; ?>" href="../onglets/reunion.php">Vos demandes</a>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle custom-nav-item <?= ($_SERVER['PHP_SELF'] == '/gestion_reunions_clubs/membre/respo.php') ? 'active' : ''; ?>" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?= $_SESSION['auth_user']; ?>
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                  <?php
                      if(isset($_SESSION['admin']) && $_SESSION['admin'] == true) //si c'est un admin, dirige vers admin dashboard
                      {
                        ?>
                          <li><a class="dropdown-item" href="../admin/index.php">Accéder à votre compte</a></li>
                        <?php
                      }
                      else
                      {
                        if($_SESSION['respo'] == true) //si c'est un respo de club, dirige vers page de membre
                        {
                            ?>
                              <li><a class="dropdown-item" href="../membre/respo.php">Accéder à votre compte</a></li>
                            <?php
                        }
                        else
                        {
                          if($_SESSION['membre'] == true) //si membre
                          {
                            ?>
                              <li><a class="dropdown-item" href="../membre/index.php">Accéder à votre compte</a></li>
                            <?php
                          }
                        }
                      }
                    ?>
                  <li><a class="dropdown-item" href="../connexion/logout.php">Se déconnecter</a></li>
                </ul>
            </li>
        <?php
      }
      else
      {
        ?>
           <li class="nav-item">
            <a class="nav-link custom-nav-item" href="../connexion/login.php">Se connecter</a>
          </li>
        <?php
      }
      ?>
      </ul>
    </div>
  </div>
</nav>

<style>
  .logo-img{
    width:100px;
    height: auto;
  }
  .navbar{
    background-color: #CC0000;
    border-color: #AA0000;
  }
  .navbar .navbar-toggler {
    border-color: #AA0000;
  }
  .custom-nav-item{
    color: #fcbbbb !important;
    font-size: 20px !important;
    margin-right: 20px;
    font-weight: bold; 
  }
  .custom-nav-item.active{
    color: #FFF !important;
    font-size: 20px !important;
    margin-right: 20px;
  }
  #ensias{
    color: white !important;
  }
</style>