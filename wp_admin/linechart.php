<?php @include "includes/header.php";?>

<section class="col-lg-6">
      <div class="card">
      <div class="card-header">
                <h3 class="card-title">
                  <i class="far fa-chart-bar"></i>
                  Yearly Downloads Analytics Reports
                </h3>
              </div>
            <!-- /.card-header -->
            <div class="card-body">
              <?php 
                  /* Database config */
                  $db_host		= 'localhost';
                  $db_user		= 'root';
                  $db_pass		= '';
                  $db_database	= 'livelihood_database'; 

                  /* End config */

                  $db = new PDO('mysql:host='.$db_host.';dbname='.$db_database, $db_user, $db_pass);
                  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                  $sql = "SELECT *, SUB_ATTRI_YEAR as DYEAR, SUM(SUB_ATTRI_MALE) as MALE, SUM(SUB_ATTRI_FEMALE) as FEMALE FROM tbl_attributes_sub WHERE SUB_LGU_ID=1 AND SUB_ATTRIBUTE_ID=95 GROUP BY SUB_ATTRI_YEAR";
                  $query = $db->prepare($sql); 
                  $query->execute();
                  $fetch = $query->fetchAll();
                  foreach ($fetch as $key => $value) {
                    $female[] = array('title'=>$value['DYEAR'], 'female'=>$value['FEMALE'], 'male'=>$value['MALE']);
                  }
                  $dyear = json_encode($female);
                  ?>
           
              <div id="chartdiv" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></div>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
		  
          </section>
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
              "balloonText": "Female: [[female]] / Male: [[male]]",
              "fillAlphas": 0.8,
              "lineAlpha": 0.2,
              "type": "column",
              "valueField": "male"
            } ],
            "chartCursor": {
              "categoryBalloonEnabled": false,
              "cursorAlpha": 0,
              "zoomable": false
            },
            "categoryField": "title",
            "categoryAxis": {
              "gridPosition": "start",
              "gridAlpha": 0,
              "tickPosition": "start",
              "tickLength": 20
            },
            "export": {
              "enabled": true
            },
          "depth3D": 20,
          "angle": 17,
          "fontFamily": "Helvetica",
          "fontSize": 13,
          "balloonText": "[[male]]",
          "color": "#FF0000",
        "colors": ['#FF6602', '#222', '#222', '#222', '#222', '#222', '#222', '#CD0D74', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#222', '#CD0D74']
        //"color": ['#FF6600', '#FCD202', '#B0DE09', '#0D8ECF', '#2A0CD0', '#CD0D74', '#CC0000', '#00CC00', '#0000CC', '#DDDDDD', '#999999', '#222333', '#990000']

          } );
        </script>