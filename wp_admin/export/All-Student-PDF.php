<?php
	include "../includes/conn.php";
	$timezone = 'Asia/Manila';date_default_timezone_set($timezone);
	function generateRow($conn){
		if(isset($_GET['date'])){
			$status =$_GET['date'];
			$contents = '';
			$sql = "SELECT * FROM tbl_student tr 
		LEFT JOIN tbl_college tc ON tr.COLLEGE=tc.COLLEGE
		LEFT JOIN tbl_course tcc ON tr.COURSE=tcc.COURSE
		ORDER BY tr.LASTNAME ASC";
			$query = $conn->query($sql);
			$count=1;
			while($row = $query->fetch_assoc()){
			$contents .= '
				<tr>
					<td>'.$count++.'</td>
					<td>'.$row['LASTNAME'].', '.$row['FIRSTNAME'].' '.$row['MI'].'</td>
					<td>'.$row['GENDER'].'</td>
					<td>'.$row['COURSE_DESC'].'</td>
					<td>'.$row['DESCRIP'].'</td>
				</tr>
			';
			$cnt++;
			}
			return $contents;
		}else{
			$contents = '';
			$sql = "SELECT * FROM tbl_student tr 
		LEFT JOIN tbl_college tc ON tr.COLLEGE=tc.COLLEGE
		LEFT JOIN tbl_course tcc ON tr.COURSE=tcc.COURSE
		ORDER BY tr.LASTNAME ASC";
			$query = $conn->query($sql);
			$count=1;
			while($row = $query->fetch_assoc()){
			$contents .= '
				<tr>
					<td>'.$count++.'</td>
					<td>'.$row['LASTNAME'].', '.$row['FIRSTNAME'].' '.$row['MI'].'</td>
					<td>'.$row['GENDER'].'</td>
					<td>'.$row['COURSE_DESC'].'</td>
					<td>'.$row['DESCRIP'].'</td>
				</tr>
			';
			$cnt++;
			}
			return $contents;
		}
	}
		
	$date = date('F d, Y');

	require_once('../../tcpdf/tcpdf.php');  
    $pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);  
	
    $pdf->SetCreator(PDF_CREATOR);  
    $pdf->SetTitle('ALL Students-'.$date.'');  
    $pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
    $pdf->SetDefaultMonospacedFont('helvetica');  
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
    $pdf->SetMargins(PDF_MARGIN_LEFT, '10', PDF_MARGIN_RIGHT);  
    $pdf->setPrintHeader(false);  
    $pdf->setPrintFooter(false);  
    $pdf->SetAutoPageBreak(TRUE, 10);  
    $pdf->SetFont('helvetica', '', 11);  
    $pdf->AddPage();  
    $content = '';  
    $content .= '
			<table width="100%" style="margin-bottom:30px" border="0">
				  <thead>
				  <tr>
					<td align="left" width="20%">
				
					</td>
					  <td align="center" width="60%">
					  <span>LIST OF STUDENT</span><br>
					  <br>
					  </td>
					  <td width="20%">
					  	
					  </td>
				  </tr>
				  </thead>
				</table>
      	<table border="1" cellspacing="0" cellpadding="3" width="100%" style="font-size:9pt;margin-top:10px">
			<tr border="0">
				<th colspan="6" style="background-color:#ccc">DTR As of '.date('Y-m-d H:i:s A').'</th>
			</tr>
                  <tr>
                    <th style="background:#ccc;" width="25">#</th>
                    <th style="background:#ccc;" width="170">NAME</th>
                    <th style="background:#ccc;" width="50">GENDER</th>
                    <th style="background:#ccc;" width="132">GRADE LEVEL</th>
                    <th style="background:#ccc;" width="133">SECTION</th>
                  </tr>'; 
    $content .= generateRow($conn);  
    $content .= '</table>';  
    $pdf->writeHTML($content);  
	  ob_end_clean();
    $pdf->Output('Allstudent-Summary-'.date('Y-m-d').'.pdf', 'I');

?>