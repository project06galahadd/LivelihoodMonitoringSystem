<?php
ob_start();
include 'includes/session.php';

function generateRow($conn)
{
  $contents = "";
  if (isset($_GET['attributeid'], $_GET['lguid'], $_GET['lguname'])) {
    $attributeid = intval($_GET['attributeid']);
    $lguid = intval($_GET['lguid']);

    $query = "SELECT lgu.LGU_NAME, sub.SUB_ATTRI_DESCRIPTION, sub.SUB_ATTRI_YEAR, 
                         sub.SUB_ATTRI_FEMALE, sub.SUB_ATTRI_MALE, att.ATTRIBUTE_NAME, 
                         sec.SECTOR_NAME, subsec.SUBSEC_NAME, att.ATTRIBUTE_DATASOURCE 
                  FROM tbl_attributes_sub sub 
                  INNER JOIN tbl_attributes att ON sub.SUB_ATTRIBUTE_ID = att.ATTRIBUTE_ID 
                  INNER JOIN tbl_subsector subsec ON att.ATTRIBUTE_SUBSEC_ID = subsec.SUBSEC_ID 
                  INNER JOIN tbl_sector sec ON subsec.SUB_SECTOR_ID = sec.SECTOR_ID 
                  INNER JOIN tbl_lgu lgu ON sub.SUB_LGU_ID = lgu.LGU_ID 
                  WHERE sub.SUB_ATTRIBUTE_ID = '$attributeid' AND sub.SUB_LGU_ID = '$lguid' 
                  ORDER BY sub.SUB_ATTRI_YEAR DESC";

    if (!$result = $conn->query($query)) {
      die('Query error: ' . $conn->error);
    }

    $curYear = null;
    $bOneYet = false;

    while ($row = $result->fetch_assoc()) {
      $female = (int)$row['SUB_ATTRI_FEMALE'];
      $male = (int)$row['SUB_ATTRI_MALE'];
      $total = $female + $male;

      if ($row['SUB_ATTRI_YEAR'] !== $curYear) {
        if ($bOneYet) {
          $contents .= "</tbody></table><br><br>";
        }

        $contents .= "
                <table border='1' cellpadding='3' width='100%' style='font-size:9pt'>
                <thead>
                <tr>
                    <th>DATASOURCE</th>
                    <th>DESCRIPTION</th>
                    <th>YEAR</th>
                    <th>FEMALE</th>
                    <th>MALE</th>
                    <th>TOTAL</th>
                </tr>
                </thead>
                <tbody>";

        $bOneYet = true;
        $curYear = $row['SUB_ATTRI_YEAR'];
      }

      $contents .= "
            <tr>
                <td>{$row['ATTRIBUTE_DATASOURCE']}</td>
                <td>{$row['SUB_ATTRI_DESCRIPTION']}</td>
                <td>{$row['SUB_ATTRI_YEAR']}</td>
                <td>{$female}</td>
                <td>{$male}</td>
                <td>{$total}</td>
            </tr>";
    }

    if ($bOneYet) {
      $contents .= "</tbody></table>";
    } else {
      $contents = "<table border='1' width='100%' style='font-size:9pt'><tr><td align='center'>NO DATA FOUND</td></tr></table>";
    }

    $result->free();
  }

  return $contents;
}

$sql = "SELECT * FROM tbl_attributes_sub sub 
        INNER JOIN tbl_attributes att ON sub.SUB_ATTRIBUTE_ID = att.ATTRIBUTE_ID 
        INNER JOIN tbl_subsector subsec ON att.ATTRIBUTE_SUBSEC_ID = subsec.SUBSEC_ID 
        INNER JOIN tbl_sector sec ON subsec.SUB_SECTOR_ID = sec.SECTOR_ID 
        INNER JOIN tbl_lgu lgu ON sub.SUB_LGU_ID = lgu.LGU_ID 
        WHERE sub.SUB_ATTRIBUTE_ID = '" . $_GET['attributeid'] . "' AND sub.SUB_LGU_ID = '" . $_GET['lguid'] . "'";
$query = $conn->query($sql);
$lgurow = $query->fetch_assoc();

// TCPDF Setup
require_once __DIR__ . '/../../vendor/autoload.php';

class MYPDF extends TCPDF
{
  public function Header() {}
  public function Footer()
  {
    $this->SetY(-15);
    $this->SetFont('helvetica', 'I', 8);
    // Footer content if needed
  }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('REPORT: ' . $lgurow['LGU_NAME']);
$pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->SetFont('helvetica', '', 11);
$pdf->AddPage();

$contents = '
<table width="100%">
<tr>
    <th align="center" style="font-size:12pt">
        <img src="../images/Seal-DGTE-PNG.png" width="60"><br>
        Municipality of Dumaguete City<br>
        Province of Negros Oriental
    </th>
</tr>
</table>
<hr>
<span style="font-size:10pt">LGU: ' . $lgurow['LGU_NAME'] . '</span><br>
<span>SECTOR: ' . $lgurow['SECTOR_NAME'] . '</span><br>
<span>SUBSECTOR: ' . $lgurow['SUBSEC_NAME'] . '</span><br>
<span>DESCRIPTION: ' . $lgurow['ATTRIBUTE_NAME'] . '</span>
<hr>
';

$contents .= generateRow($conn);

$pdf->writeHTML($contents, true, false, true, false, '');
ob_end_clean();
$pdf->Output('REPORT-' . date('Y-m-d') . '.pdf', 'I');
