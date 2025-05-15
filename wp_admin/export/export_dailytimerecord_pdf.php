<?php
	include "../includes/conn.php";
	$timezone = 'Asia/Manila';date_default_timezone_set($timezone);
	function generateRow($conn){
		if(isset($_GET['date'])){
			$CURRENT_DATE =$_GET['date'];
			$contents = '';
			$sql = "SELECT * FROM tbl_attendance ta INNER JOIN tbl_student ts ON ta.IDNUMBER=ts.IDNUMBER WHERE ta.AttendDate='".$CURRENT_DATE."' ORDER by ta.AttendDate DESC";
			$query = $conn->query($sql);
			$count=1;
			while($row = $query->fetch_assoc()){
			$contents .= '
				<tr>
					<td>'.$count++.'</td>
					<td>'.$row['LASTNAME'].', '.$row['FIRSTNAME'].' '.$row['MI'].'</td>
					<td>'.$row['TimeInAM'].'</td>
					<td>'.$row['TimeOutAM'].'</td>
					<td>'.$row['TimeInPM'].'</td>
					<td>'.$row['TimeOutPM'].'</td>
					<td>'.$row['AttendDate'].'</td>
				</tr>
			';
			$cnt++;
			}
			return $contents;
		}else{
			$contents = '';
			$sql = "SELECT * FROM tbl_attendance ta INNER JOIN tbl_student ts ON ta.IDNUMBER=ts.IDNUMBER WHERE ta.AttendDate='".$CURRENT_DATE."' ORDER by ta.AttendDate DESC";
			$query = $conn->query($sql);
			$count=1;
			while($row = $query->fetch_assoc()){
			$contents .= '
				<tr>
					<td>'.$count++.'</td>
					<td>'.$row['LASTNAME'].', '.$row['FIRSTNAME'].' '.$row['MI'].'</td>
					<td>'.$row['TimeInAM'].'</td>
					<td>'.$row['TimeOutAM'].'</td>
					<td>'.$row['TimeInPM'].'</td>
					<td>'.$row['TimeOutPM'].'</td>
					<td>'.$row['AttendDate'].'</td>
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
    $pdf->SetTitle('DTR-'.$date.'');  
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
					  <span>DAILY TIME RECORD</span><br>
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
                    <th style="background:#ccc;" width="34">#</th>
                    <th style="background:#ccc;" width="200">NAME</th>
                    <th style="background:#ccc;" width="50">A.M IN</th>
                    <th style="background:#ccc;" width="50">A.M OUT</th>
                    <th style="background:#ccc;" width="50">P.M IN</th>
                    <th style="background:#ccc;" width="50">P.M OUT</th>
                    <th style="background:#ccc;" width="76">LOG DATE</th>
                  </tr>'; 
    $content .= generateRow($conn);  
    $content .= '</table>';  
    $pdf->writeHTML($content);  
	  ob_end_clean();
    $pdf->Output('daily_attendance_export-'.date('Y-m-d').'.pdf', 'I');

?>