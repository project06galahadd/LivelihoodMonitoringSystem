<?php
include "../includes/session.php";
if(isset($_GET['xls'])){
        $status =$_GET['xls'];
        $output = "";
         
        $output .= '
                    
                  <table id="example1" class="table table-bordered table-striped table-hover" border="1">
                  <tr>
                    <th style="background:#ccc;" width="50">#</th>
                    <th style="background:#ccc;" width="100">RECORD NUMBER</th>
                    <th style="background:#ccc;" width="200">NAME</th>
                    <th style="background:#ccc;" width="100">GENDER</th>
                    <th style="background:#ccc;" width="100">DOB</th>
                    <th style="background:#ccc;" width="300">AGE</th>
                    <th style="background:#ccc;" width="100">MOBILE</th>
                    <th style="background:#ccc;" width="300">BARANGAY</th>
                    <th style="background:#ccc;" width="300">ADDRESS</th>
					<th style="background:#ccc;" width="300">EDUCATIONAL_BACKGROUND</th>
					<th style="background:#ccc;" width="300">EMPLOYMENT_HISTORY</th>
					<th style="background:#ccc;" width="300">SKILLS_QUALIFICATION</th>
					<th style="background:#ccc;" width="300">DESIRED_LIVELIHOOD_PROGRAM</th>
					<th style="background:#ccc;" width="300">EXP_LIVELIHOOD_PROGRAM_CHOSEN</th>
					<th style="background:#ccc;" width="300">CURRENT_LIVELIHOOD_SITUATION</th>
					<th style="background:#ccc;" width="300">REQUIRED_TRAINING</th>
					<th style="background:#ccc;" width="300">REASON_INTERESTED_IN_LIVELIHOOD</th>
					<th style="background:#ccc;" width="300">VALID_ID_NUMBER</th>
					<th style="background:#ccc;" width="300">DATE_OF_APPLICATION</th>
					<th style="background:#ccc;" width="300">STATUS</th>
					<th style="background:#ccc;" width="300">STATUS_REMARKS</th>
                  </tr>
                    
                    ';
             

			//   $sql = "SELECT * FROM tbl_members ORDER BY LASTNAME ASC";
			//    $stmt = $conn->prepare($sql);
			//    $stmt->execute();
			//    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);   

			$sql="SELECT * FROM tbl_members ORDER BY LASTNAME ASC";
			$data = $conn->query($sql);
			foreach($data as $key=>$value){
			 
				$output .= '<tr>  
							<td align="left">'.($key+1).'</td>
							<td>'.$value['RECORD_NUMBER'].'</td>
							<td>'.$value['LASTNAME'].', '.$value['FIRSTNAME'].' '.$value['MIDDLENAME'].'</td>
							<td>'.$value['GENDER'].'</td>
							<td>'.$value['DATE_OF_BIRTH'].'</td>
							<td>'.$value['AGE'].'</td>
							<td>'.$value['MOBILE'].'</td>  
							<td>'.$value['BARANGAY'].'</td>  
							<td>'.$value['ADDRESS'].'</td>
							<td>'.$value['EDUCATIONAL_BACKGROUND'].'</td> 
							<td>'.$value['EMPLOYMENT_HISTORY'].'</td> 
							<td>'.$value['SKILLS_QUALIFICATION'].'</td> 
							<td>'.$value['DESIRED_LIVELIHOOD_PROGRAM'].'</td> 
							<td>'.$value['EXP_LIVELIHOOD_PROGRAM_CHOSEN'].'</td> 
							<td>'.$value['CURRENT_LIVELIHOOD_SITUATION'].'</td> 
							<td>'.$value['REQUIRED_TRAINING'].'</td> 
							<td>'.$value['REASON_INTERESTED_IN_LIVELIHOOD'].'</td> 
							<td>'.$value['VALID_ID_NUMBER'].'</td> 
							<td>'.$value['DATE_OF_APPLICATION'].'</td> 
							<td>'.$value['STATUS'].'</td> 
							<td>'.$value['STATUS_REMARKS'].'</td> 

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