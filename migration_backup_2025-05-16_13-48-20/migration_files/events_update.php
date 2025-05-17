<?php include "header.php";?>
<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<body>
  <div class="wrapper">
  <?php include "navbar.php";?>
	
	<div class="content container mt-5">
	  <section class="content-header">
    </section>
		<div class="container">
			<div class="col-md-12">
			<h4 class="text-white">NEWS AND ADVISORIES</h4>
			<div class="card">
              <div class="card-header">
                      <h3 class="card-title"> 
             <i class="fa fa-calendar-alt"></i> LISTS
              </h3>
			      	<div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table table-bordered table-striped table-sm text-sm">
                  <thead>
                  <tr>
                    <th>#</th>
                    <th>TITLE</th>
                    <th>DESCRIPTION </th>
                    <th>START TIME</th>
                    <th>END TIME</th>
                    <th>DATE POSTED</th>
                  </tr>
                  </thead>
                  <tbody>
				<?php
                    $sql = "SELECT * FROM schedule_list ORDER BY start_datetime ASC";
                    $query = $conn->query($sql);
				        	$cnt=1;
                    while($row = $query->fetch_assoc()){
                      ?>
                        <tr>
                          <td><?=$cnt++; ?></td>
                          <td><?=$row['title']; ?></td>
                          <td><?=$row['description']; ?></td>
                          <td><?= $row['start_datetime'];?></td>
                          <td><?= $row['end_datetime'];?></td>
                          <td><?= $row['date_posted'];?></td>
   
                        </tr>
                      <?php
                    }
                  ?>
                  </tbody>
                 
                </table>
              </div>
              <!-- /.card-body -->
            </div>

			</div>
		</div>
	</div>
  <!-- /.content-wrapper -->
</div><!-- End #main -->


  <!-- ======= Footer ======= -->
  <?php include "scripts.php";?>
 