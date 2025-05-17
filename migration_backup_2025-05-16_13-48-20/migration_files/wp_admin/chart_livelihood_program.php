<?php @include "includes/header.php";?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- .navbar -->
  <?php @include "includes/navbar.php";?>
  <!-- /.navbar -->
  <?php @include "includes/sidebar.php";?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>CHARTS</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">HOME</a></li>
              <li class="breadcrumb-item active">CHARTS</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
        <div class="col-md-3">

            <div class="card card-secondarys card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  RANK BY LIVELIHOOD PROGRAM
                </div>
                <ul class="list-group list-group-unbordered mb-3">
                  <?php

                    $sqlchart="SELECT *,COUNT(m.DESIRED_LIVELIHOOD_PROGRAM) AS TotalProgram, b.LIVELIHOOD_NAME FROM tbl_livelihood b LEFT JOIN tbl_members m ON b.LIVELIHOOD_NAME=m.DESIRED_LIVELIHOOD_PROGRAM  GROUP BY b.LIVELIHOOD_NAME";
                    $cquery=$conn->query($sqlchart);
                    if($cquery->num_rows >0){
                      while($rowchart=$cquery->fetch_assoc()){
                        $TotalProgram=$rowchart['TotalProgram'];
                        echo ' <li class="list-group-item">
                          '.$rowchart['LIVELIHOOD_NAME'].'<br><a class="float-right"> <span class="badge badge-primary">'.$TotalProgram.'</span></a>
                          </li>';
                      }
                    }else{
                        echo '<li class="list-group-item">
                          <a>No record found</a>
                          </li>';
                    }
                  ?>
                 
                </ul>
  
              </div>
            </div>
      </div>
          <div class="col-md-9">
            <div class="card">
              <div class="card-header">
               <h3 class="card-title"> 
                 REPORTS BY LIVELIHOOD PROGRAM MEMBERS
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
              <?php 
              error_reporting(0);
                  /* Database config */
                  $db_host		= 'localhost';
                  $db_user		= 'root';
                  $db_pass		= '';
                  $db_database	= 'livelihood_database'; 

                  /* End config */

                  $db = new PDO('mysql:host='.$db_host.';dbname='.$db_database, $db_user, $db_pass);
                  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                  
                  $sql = "SELECT *,COUNT(m.DESIRED_LIVELIHOOD_PROGRAM) AS TotalProgram, b.LIVELIHOOD_NAME as Title FROM tbl_livelihood b LEFT JOIN tbl_members m ON b.LIVELIHOOD_NAME=m.DESIRED_LIVELIHOOD_PROGRAM  GROUP BY b.LIVELIHOOD_NAME";
                  $query = $db->prepare($sql); 
                  $query->execute();
                  $fetch = $query->fetchAll();
                  foreach ($fetch as $key => $value) {
                    $female[] = array('LivelihoodName'=>$value['Title'], 'TotalProg'=>$value['TotalProgram']);
                  }
                  $dyear = json_encode($female);
                  if($female==""){
                    echo "<div class='alert alert-primary text-center'> NO DATA TO OUTPUT</div>";
                  }
                  ?>
           
                <div id="chartdiv" style="height:400px"></div>

              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
 <?php include "includes/footer.php";?>
 <script src="plugins/amcharts/amcharts.js"></script>
  <script src="plugins/amcharts/animate.min.js"></script>
  <script src="plugins/amcharts/themes/light.js"></script>
  <script src="plugins/amcharts/export/export.min.js"></script>
  <script src="plugins/amcharts/themes/patterns.js"></script>
  <script type="plugins/export/export.css"></script>
  <script src="plugins/amcharts/plugins/responsive/responsive.min.js"></script>
  <script src="plugins/amcharts/serial.js"></script>
  <script src="plugins/amcharts/pie.js"></script>
  <!-- <script>
  var raw = '<?php echo $dyear; ?>';
  var data = JSON.parse(raw);
  var chart = AmCharts.makeChart( "chartdiv", {
    "type": "serial",
    "theme": "scatter",
    "dataProvider": data,
    "valueAxes": [ {
      "gridColor": "#FFFFFF",
      "gridAlpha": 0.2,
      "dashLength": 0
    } ],
    "gridAboveGraphs": true,
    "startDuration": 1,
    "graphs": [ {
      "balloonText": "Female: [[Female]] / Male: [[Male]]",
      "fillAlphas": 0.8,
      "lineAlpha": 0.2,
      "type": "column",
      "valueField": "Male"
    } ],
    "chartCursor": {
      "categoryBalloonEnabled": false,
      "cursorAlpha": 0,
      "zoomable": false
    },
    
    "categoryField": "Title",
    "categoryAxis": {
      "gridPosition": "start",
      "gridAlpha": 0,
      "tickPosition": "start",
      "tickLength": 20,
      "labelRotation": 45,
      "labelPadding": 0,
    },
    
    "export": {
      "enabled": true
    },
    "legend": {
        "maxColumns": 1,
        "useGraphSettings": true,
        
    },
  "depth3D": 20,
  "angle": 17,
  "fontFamily": "Helvetica",
  "fontSize": 13,
  "balloonText": "[[Male]]",
  "color": "#222",
"colors": ['#0D8ECF', '#222', '#222', '#222', '#222', '#222', '#222', '#CD0D74', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#CD0D74']

  } );
</script> -->
<script>
  var raw = '<?php echo $dyear; ?>';
  var data = JSON.parse(raw);
  var chart = AmCharts.makeChart( "chartdiv", {
    "type": "serial",
    "theme": "scatter",
    "dataProvider": data,
    "valueAxes": [ {
      "gridColor": "#FFFFFF",
      "gridAlpha": 0.2,
      "dashLength": 0
    } ],
    "gridAboveGraphs": true,
    "startDuration": 1,
    "graphs": [ {
      "balloonText": "Total: [[TotalProg]]",
      "fillAlphas": 0.8,
      "lineAlpha": 0.2,
      "type": "column",
      "valueField": "TotalProg"
    } ],
    "chartCursor": {
      "categoryBalloonEnabled": true,
      "cursorAlpha": 2,
      "zoomable": true
    },
    
    "categoryField": "LivelihoodName",
    "categoryAxis": {
      "gridPosition": "start",
      "gridAlpha": 0,
      "tickPosition": "end",
      "tickLength": 20,
      "labelRotation": 45
    },
    
    "export": {
      "enabled": true
    },
    // "legend": {
    //     "maxColumns": 1,
    //     "useGraphSettings": true,
        
    // },
  "depth3D": 20,
  "angle": 17,
  "fontFamily": "Helvetica",
  "fontSize": 10,
  "balloonText": "[[TotalProg]]",
  "color": "#222",
"colors": ['#0D8ECF', '#222', '#222', '#222', '#222', '#222', '#222', '#CD0D74', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#CD0D74']

  } );
</script>
</body>
</html>

