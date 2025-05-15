<?php 
session_start();
include "header.php"; 
?>

<body class="overlay page__landing">
  <div class="wrapper">
    <?php include "navbar.php"; ?>
    <div class="content container">
      <section class="content-header">
        <div class="wave"></div>
        <div class="wave"></div>
        <div class="wave"></div>
      </section>
      <?php if (isset($_SESSION['registration_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <?php 
          echo $_SESSION['registration_success'];
          unset($_SESSION['registration_success']);
          ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>
      <div class="container mt-5">

        <!---time--->
        <div class="col-md-12">
          <section class="text-center">
            <div class="container" data-aos="zoom-out" data-aos-delay="100"><br><br>
              <h1 style="color:white;font-weight:bold;font-size:25pt;font-family: century gothic">
                <span style="color:white;font-size:30pt">MSWD</span>
                <span style="color:white;font-size:30pt">L</span>IVELIHOOD
                <span style="color:white;font-size:30pt">P</span>ROGRAM
              </h1>
              <?php
              if ($SYS_LOGO == "") {
              ?>
                <img class="brand-image rotateIn" src="dist/img/LOGO DESIGN.png" width="150">
              <?php } else { ?>
                <img class="brand-image rotateIn" width="150" src="data:image/jpg;charset=utf8;base64,<?= base64_encode($SYS_LOGO); ?>">
              <?php } ?>

              <h1 style="color:#fff;font-weight:bold;font-size:50pt;font-family: century gothic">
                MSWD PORTAL
              </h1>
              <p style="color:white">
                The MSWD Livelihood Monitoring System is a capability-building program for poor, <br>vulnerable and marginalized households and communities <br> to help improve their socio-economic conditions through accessing and <br> acquiring necessary assets to engage in and maintain thriving livelihoods.
              </p>

              <!-- Only REGISTER and LOGIN buttons -->
              <a class="btn btn-danger" href="register_form.php">REGISTER</a>
              <form action="login_selection.php" method="get" style="display: inline;">
                <button type="submit" class="btn btn-primary">LOGIN</button>
              </form>

            </div>
          </section>
          <br>
          <br>
          <br>
        </div>
        <!---end time--->
      </div>
    </div>
    <!-- /.content-wrapper -->
  </div><!-- End #main -->

  <!-- ======= Footer ======= -->
  <?php include "scripts.php"; ?>
</body>

</html>