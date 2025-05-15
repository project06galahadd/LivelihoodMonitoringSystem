<?php
$servername='localhost';
$username="root";
$password="";
try
{
    $con=new PDO("mysql:host=$servername;dbname=gadfps_database",$username,$password);
    $con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    //echo 'connected';
}catch(PDOException $e)
{
    echo '<br>'.$e->getMessage();
}
if(isset($_GET['attributeid']) && ($_GET['lguid']) && ($_GET['lguname'])){
    $attributeid =intval($_GET['attributeid']);
    $lguid =intval($_GET['lguid']);
    $lguname =$_GET['lguname'];
        $output = "";
         
        $output .= '
                    
                  <table id="example1" class="table table-bordered table-striped table-hover" border="1">
                  <tr>
					<th style="background:#ccc;" width="100%">#</th>
                    <th style="background:#ccc;" width="100%">LGU</th>
                    <th style="background:#ccc;" width="100%">SECTOR</th>
                    <th style="background:#ccc;" width="100%">SUBSECTOR</th>
                    <th style="background:#ccc;" width="100%">ATTRIBUTE</th>
                    <th style="background:#ccc;" width="100%">DATASOURCE</th>
                    <th style="background:#ccc;" width="100%">DESCRIPTION</th>
                    <th style="background:#ccc;" width="100%">YEAR</th>
                    <th style="background:#ccc;" width="100%">FEMALE</th>
                    <th style="background:#ccc;" width="100%">MALE</th>
					<th style="background:#ccc;" width="100%">TOTAL</th>
            		</tr>
                    
                    ';
             

		$sql = "SELECT lgu.LGU_NAME,sub.SUB_ATTRI_DESCRIPTION, sub.SUB_ATTRI_YEAR, sub.SUB_ATTRI_FEMALE, sub.SUB_ATTRI_MALE, att.ATTRIBUTE_NAME,sec.SECTOR_NAME,subsec.SUBSEC_NAME,att.ATTRIBUTE_DATASOURCE FROM tbl_attributes_sub sub 
    INNER JOIN tbl_attributes att ON sub.SUB_ATTRIBUTE_ID= att.ATTRIBUTE_ID 
    INNER JOIN tbl_subsector subsec ON att.ATTRIBUTE_SUBSEC_ID=subsec.SUBSEC_ID 
    INNER JOIN tbl_sector sec ON subsec.SUB_SECTOR_ID=sec.SECTOR_ID 
    INNER JOIN tbl_lgu lgu ON sub.SUB_LGU_ID=lgu.LGU_ID 
    WHERE sub.SUB_ATTRIBUTE_ID ='$attributeid' AND sub.SUB_LGU_ID='$lguid' 
	ORDER BY lgu.LGU_NAME DESC";
			   $stmt = $con->prepare($sql);
			   $stmt->execute();
			   $data = $stmt->fetchAll(PDO::FETCH_ASSOC);   
        
			foreach($data as $key=>$value){
				$total =0;
				$overall=0;
				$total = number_format($value['SUB_ATTRI_FEMALE']) + number_format($value['SUB_ATTRI_MALE']);
				$output .= '<tr>  
							<td align="left">'.($key+1).'</td>
							<td>'.$value['LGU_NAME'].'</td>
							<td>'.$value['SECTOR_NAME'].'</td>
							<td>'.$value['SUBSEC_NAME'].'</td>
							<td>'.$value['ATTRIBUTE_NAME'].'</td>
							<td>'.$value['ATTRIBUTE_DATASOURCE'].'</td>
							<td>'.$value['SUB_ATTRI_DESCRIPTION'].'</td>  
							<td>'.$value['SUB_ATTRI_YEAR'].'</td>  
							<td>'.$value['SUB_ATTRI_FEMALE'].'</td>  
							<td>'.$value['SUB_ATTRI_MALE'].'</td>
							<td>'.$total.'</td>
							</tr>
						';  
					}
					  
					$output .= '</table>';
					
					$filename = $lguname."-".date('Y-m-d') . ".xls";         
					header("Content-Type: application/vnd.ms-excel");
					header("Content-Disposition: attachment; filename=\"$filename\"");  
					echo $output;
					exit;
}
?>