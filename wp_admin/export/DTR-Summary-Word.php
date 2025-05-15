<style>
	span{
		font-family: Arial, Helvetica, sans-serif;
	}
table, td, th {
  border: 1px solid #ccc;
  font-size:8pt;
  /*text-align:right;*/
  font-family: Arial, Helvetica, sans-serif;
  padding:5px;
}
th{
	background:#ccc;
	color:#000;
	
}
table {
  width: 100%;
  border-collapse: collapse;
}

@media print {
  #printPageButton {
    display: none;
	
  }
}
@media print {
  table:nth-child(1) {
    display: block;
  }
}
    @page
        {
            size: auto; /* auto is the initial value */
            margin: 2mm 4mm 0mm 0mm; /* this affects the margin in the printer settings */
        }
        thead
        {
            display: table-header-group;
        }
        tfoot
        {
            display: table-footer-group;
        }
        thead
        {
            display: block;
        }
        tfoot
        {
            display: block;
        }
        h4, h3,h5{
          font-family: Arial, Helvetica, sans-serif;
        }
</style>

	<center>
	  <span>REPUBLIC OF THE PHILIPPINES</span><br>
	  <h4> DAILY TIME RECORD</h4>
	  </center>
<br>
<table border="1" style="border-collapse: collapse;width:100%" cellpadding="4">
<tr>
<th style="background:#ccc;">#</th>
<th style="background:#ccc;">NAME</th>
<th style="background:#ccc;">LOG DATE</th>
<th style="background:#ccc;">AM IN</th>
<th style="background:#ccc;">AM OUT</th>
<th style="background:#ccc;">PM IN</th>
<th style="background:#ccc;">PM OUT</th>
</tr>
				  
<tbody>

<?php  
error_reporting(0);
include "../includes/conn.php";

if(isset($_GET['date'])){
    $status = $_GET['date'];
    $sql ="SELECT * FROM tbl_attendance ta INNER JOIN tbl_student ts ON ta.IDNUMBER=ts.IDNUMBER  ORDER by ta.AttendDate DESC";
      $query = $conn->query($sql);
}else{
    $sql ="SELECT * FROM tbl_attendance ta INNER JOIN tbl_student ts ON ta.IDNUMBER=ts.IDNUMBER  ORDER by ta.AttendDate DESC";
      $query = $conn->query($sql);
}


$key=1;
while($value = $query->fetch_assoc()){
           header("Content-type: application/vnd.ms-word");  
           header("Content-Disposition: attachment;Filename=DTR-Summary-".date('Y-m-d').'-'.rand().".doc");  
           header("Pragma: no-cache");  
           header("Expires: 0");  
            echo '
            <tr>  
			 <td align="left">'.$key++.'</td>
			  <td align="left">'.$value['LASTNAME'].', '.$value['FIRSTNAME'].' '.$value['MI'].'</td>    
			 <td align="left">'.$value['TimeInAM'].'</td>    
			 <td align="left">'.$value['TimeOutAM'].'</td>    
			 <td align="left">'.$value['TimeInPM'].'</td>    
			 <td align="left">'.$value['TimeOutPM'].'</td>    
			 <td align="left">'.$value['AttendDate'].'</td>     
			</tr>';
}
 exit; 
 ?> 
  </tbody>
</table>