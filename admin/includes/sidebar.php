<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3   bg-gradient-dark" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href=" https://demos.creative-tim.com/material-dashboard/pages/dashboard " target="_blank">
        <span class="ms-1 font-weight-bold text-white">Tableau de bord</span>
      </a>
    </div>
    <hr class="horizontal light mt-0 mb-2">
    <div class="collapse navbar-collapse  w-auto  max-height-vh-100" id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item ">
            <a class="nav-link text-white <?= ($current_page === 'calendar') ? 'active' : ''; ?>" href="calendar.php">
              <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="bi bi-calendar"></i>
              </div>
              <span class="nav-link-text ms-1">Calendrier des réunions</span>
            </a>
          </li>
        <li class="nav-item ">
          <a class="nav-link text-white <?= ($current_page === 'eleves') ? 'active' : ''; ?>" href="eleves.php">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">dashboard</i>
            </div>
            <span class="nav-link-text ms-1">Elèves</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white <?= ($current_page === 'index') ? 'active' : ''; ?>" href="index.php">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">dashboard</i>
            </div>
            <span class="nav-link-text ms-1">Clubs</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white <?= ($current_page === 'demandes_reunions') ? 'active' : ''; ?>" href="demandes_reunions.php">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">table_view</i>
            </div>
            <span class="nav-link-text ms-1">Demandes de réunions</span>
          </a>
        </li>
        <!--
        <li class="nav-item">
          <a class="nav-link text-white <?= ($current_page === 'reunions_approuvees') ? 'active' : ''; ?>" href="reunions_approuvees.php">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">table_view</i>
            </div>
            <span class="nav-link-text ms-1">Reunions approuvees</span>
          </a>
        </li>-->
        <li class="nav-item">
          <a class="nav-link text-white <?= ($current_page === 'reunions_rejetees') ? 'active' : ''; ?>" href="reunions_rejetees.php">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">table_view</i>
            </div>
            <span class="nav-link-text ms-1">Réunions rejetées</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white <?= ($current_page === 'archives_reuns') ? 'active' : ''; ?>" href="archives_reuns.php">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">table_view</i>
            </div>
            <span class="nav-link-text ms-1">Archives des réunions</span>
          </a>
        </li>
      </ul>
    </div>
    <div class="sidenav-footer position-absolute w-100 bottom-0 ">
    <div class="mx-3">
        <a class="btn bg-gradient-info mt-4 w-100" 
        href="../accueil/index.php?state=loggedin" type="button">
        Retour a l'accueil</a>
      </div>
      <div class="mx-3">
        <a class="btn bg-gradient-primary mt-4 w-100" 
        href="../connexion/logout.php" type="button">
        Se déconnecter</a>
      </div>
    </div>
  </aside> 