<?php
$servername='localhost';
$username="root";
$password="";
try
{
    $con=new PDO("mysql:host=$servername;dbname=norsu_qrcode",$username,$password);
    $con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    //echo 'connected';
}
catch(PDOException $e)
{
    echo '<br>'.$e->getMessage();
}

if(isset($_GET['date'])){
        $status =$_GET['date'];
        $output = "";
         
        $output .= '
                    
                  <table id="example1" class="table table-bordered table-striped table-hover" border="1">
                  <tr>
                    <th style="background:#ccc;" width="100">#</th>
                    <th style="background:#ccc;" width="200">NAME</th>
                    <th style="background:#ccc;" width="100">A.M IN</th>
                    <th style="background:#ccc;" width="100">A.M OUT</th>
                    <th style="background:#ccc;" width="100">P.M IN</th>
                    <th style="background:#ccc;" width="100">P.M OUT</th>
                    <th style="background:#ccc;" width="100">LOG DATE</th>
                  </tr>
                    
                    ';
             

					$sql = "SELECT * FROM tbl_attendance ta INNER JOIN tbl_student ts ON ta.IDNUMBER=ts.IDNUMBER ORDER by ta.AttendDate DESC";
			   $stmt = $con->prepare($sql);
			   $stmt->execute();
			   $data = $stmt->fetchAll(PDO::FETCH_ASSOC);   
        
			foreach($data as $key=>$value){
			 
				$output .= '<tr>  
							 <td align="left">'.($key+1).'</td>
							 <td>'.$value['LASTNAME'].', '.$value['FIRSTNAME'].' '.$value['MI'].'</td>
							<td>'.$value['TimeInAM'].'</td>
							<td>'.$value['TimeOutAM'].'</td>
							<td>'.$value['TimeInPM'].'</td>
							<td>'.$value['TimeOutPM'].'</td>
							<td>'.$value['AttendDate'].'</td>  
							</tr>
						';  
					}
					  
					$output .= '</table>';
					
					$filename = $status."-".date('Y-m-d') . ".xls";         
					header("Content-Type: application/vnd.ms-excel");
					header("Content-Disposition: attachment; filename=\"$filename\"");  
					echo $output;
			}else{
		$output = "";
   
		$output .= '
              
            <table id="example1" class="table table-bordered table-striped table-hover" border="1">
            <tr>
					 <th style="background:#ccc;" width="100">#</th>
                    <th style="background:#ccc;" width="200">NAME</th>
                    <th style="background:#ccc;" width="100">A.M IN</th>
                    <th style="background:#ccc;" width="100">A.M OUT</th>
                    <th style="background:#ccc;" width="100">P.M IN</th>
                    <th style="background:#ccc;" width="100">P.M OUT</th>
                    <th style="background:#ccc;" width="100">LOG DATE</th>
            </tr>
              
              ';
       

			$sql = "SELECT * FROM tbl_attendance ta INNER JOIN tbl_student ts ON ta.IDNUMBER=ts.IDNUMBER ORDER by ta.AttendDate DESC";
			$stmt = $con->prepare($sql);
			$stmt->execute();
			$data = $stmt->fetchAll(PDO::FETCH_ASSOC);   
			  
			foreach($data as $key=>$value){

			$output .= '<tr>  
							<td align="left">'.($key+1).'</td>
							<td>'.$value['LASTNAME'].', '.$value['FIRSTNAME'].' '.$value['MI'].'</td>
							<td>'.$value['TimeInAM'].'</td>
							<td>'.$value['TimeOutAM'].'</td>
							<td>'.$value['TimeInPM'].'</td>
							<td>'.$value['TimeOutPM'].'</td>
							<td>'.$value['AttendDate'].'</td>  
			  </tr>
			';  
			  }
    
		$output .= '</table>';
  
  $filename = $status."-".date('Y-m-d') . ".xls";         
  header("Content-Type: application/vnd.ms-excel");
  header("Content-Disposition: attachment; filename=\"$filename\"");  
  echo $output;

}
?>