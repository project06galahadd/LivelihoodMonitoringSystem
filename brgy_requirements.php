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
          <h4 class="text-white text-uppercase">List of Acceptable Valid IDs (Any of the following with one (1) photocopy)</h4>
          <ul class="list-groups list-group-flushs text-white">
            <?php
            $sql = "SELECT * FROM tbl_requirements ORDER BY REQ_NAME ASC";
            $query = $conn->query($sql);
            if ($query->num_rows > 0) {
              while ($reqrow = $query->fetch_assoc()) {
            ?>
                <li type="A" class="list-group-items"><?= $reqrow['REQ_NAME']; ?></li>
              <?php }
            } else { ?>
              <li class="list-group-item bg-warning">No requirements found</li>
            <?php } ?>
          </ul>

          <div class="callout callout-info" style="box-shadow:none">
            <h5 class="text-info">IMPORTANT REMINDER!</h5>
            <p>The Department may require additional supporting documents as may be necessary.</p>
          </div>
        </div>
        <br>
        <br>
      </div>
    </div>
    <!-- /.content-wrapper -->
  </div><!-- End #main -->

  <!-- ======= Footer ======= -->
  <?php include "scripts.php"; ?>