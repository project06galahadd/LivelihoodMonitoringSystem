<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #0d47a1 0%, #1a237e 100%); box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
  <div class="container-fluid">
    <!-- Brand/Logo -->
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <?php if ($SYS_LOGO == "") { ?>
        <img class="mr-2" src="dist/img/LOGO DESIGN.png" width="40" alt="MSWD Logo">
      <?php } else { ?>
        <img class="mr-2" width="40" src="data:image/jpg;charset=utf8;base64,<?= base64_encode($SYS_LOGO); ?>" alt="MSWD Logo">
      <?php } ?>
      <span class="text-white font-weight-bold" style="font-size: 1.2rem;">MSWD PORTAL</span>
    </a>

    <!-- Toggle Button -->
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Nav Items -->
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link d-flex align-items-center" href="brgy_requirements.php?requirements=requirements">
            <i class="fas fa-folder-open mr-2" style="font-size: 1.1rem;"></i>
            <span>Requirements</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link d-flex align-items-center" href="events_update.php?events=updates">
            <i class="fas fa-bell mr-2" style="font-size: 1.1rem;"></i>
            <span>Updates and Advisories</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link d-flex align-items-center" href="brgy_diretories.php?brgy_diretory=contact">
            <i class="fas fa-phone-alt mr-2" style="font-size: 1.1rem;"></i>
            <span>Brgy. Directory</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link d-flex align-items-center" href="register_form.php?register">
            <i class="fas fa-user-plus mr-2" style="font-size: 1.1rem;"></i>
            <span>REGISTER</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link d-flex align-items-center" href="login_selection.php">
            <i class="fas fa-sign-in-alt mr-2" style="font-size: 1.1rem;"></i>
            <span>LOGIN</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<style>
  .navbar {
    padding: 0.8rem 1rem;
  }

  .navbar-brand {
    padding: 0.5rem 0;
  }

  .nav-link {
    color: rgba(255, 255, 255, 0.9) !important;
    padding: 0.8rem 1rem !important;
    transition: all 0.3s ease;
    border-radius: 4px;
    margin: 0 0.2rem;
  }

  .nav-link:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #fff !important;
    transform: translateY(-1px);
  }

  .nav-link i {
    transition: transform 0.3s ease;
  }

  .nav-link:hover i {
    transform: scale(1.1);
  }

  .navbar-toggler {
    border: none;
    padding: 0.5rem;
  }

  .navbar-toggler:focus {
    outline: none;
  }

  @media (max-width: 991px) {
    .navbar-collapse {
      background: rgba(13, 71, 161, 0.95);
      padding: 1rem;
      border-radius: 8px;
      margin-top: 1rem;
    }

    .nav-link {
      padding: 0.8rem 1rem !important;
      margin: 0.2rem 0;
    }
  }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">