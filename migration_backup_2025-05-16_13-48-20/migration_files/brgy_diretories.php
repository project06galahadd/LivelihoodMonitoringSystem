<?php include "header.php"; ?>

<body>
  <div class="wrapper">
    <?php include "navbar.php"; ?>

    <div class="content container mt-5">
      <section class="content-header">
      </section>
      <div class="container">
        <?php //include "sidebar.php"
        ?>
        <div class="col-md-12">
          <h4 class="text-white">BARANGAY DIRECTORIES</h4>
          <table class="table bg-white text-white table-bordered table-hover">
            <thead>
              <tr>
                <th>BARANGAY</th>
                <th>BARANGAY CAPTAIN</th>
                <th>CONTACT NUMBER</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $brgy = "SELECT * FROM tbl_barangay ORDER BY BRGY_NAME ASC";
              $brgy_query = $conn->query($brgy);
              while ($rows_brgy = $brgy_query->fetch_assoc()) {
              ?>

                <tr>
                  <td><?= $rows_brgy['BRGY_NAME']; ?></td>
                  <td><?= $rows_brgy['BRGY_CAPTAIN']; ?></td>
                  <td><?= $rows_brgy['BRGY_CONTACT']; ?></td>
                </tr>
              <?php } ?>
            <tbody>
          </table>
          <br>
          <br>
          <br>
          <br>
          <br>
          <br>
          <br>
          <br>
          <br>
        </div>
      </div>
    </div>
    <!-- /.content-wrapper -->
  </div><!-- End #main -->

  <!-- ======= Footer ======= -->
  <?php include "scripts.php"; ?>