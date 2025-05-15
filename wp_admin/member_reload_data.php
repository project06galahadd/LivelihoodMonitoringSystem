<?php
require_once "includes/conn.php";

$sql = "SELECT * FROM tbl_members WHERE STATUS != 'ARCHIVED' ORDER BY LASTNAME ASC";
$query = $conn->query($sql);
$cnt = 1;
while ($row = $query->fetch_assoc()) {
    $dob_rows = $row['DATE_OF_BIRTH'];
    $dob = new DateTime($dob_rows);
    $today   = new DateTime('today');
    $year = $dob->diff($today)->y;
    $month = $dob->diff($today)->m;
    $day = $dob->diff($today)->d;
    // echo "Age is"." ".$year."year"." ",$month."months"." ".$day."days <br>";

    if ($today >= $dob) {
        $sqlage = "UPDATE tbl_members SET AGE='$year' WHERE MEMID='" . $row['MEMID'] . "'";
        $conn->query($sqlage);
    }
?>
    <tr>
        <td><?= $cnt++; ?></td>
        <td><?= $row['RECORD_NUMBER']; ?></td>
        <td><?= $row['LASTNAME'] . ', ' . $row['FIRSTNAME'] . ' ' . $row['MIDDLENAME']; ?></td>
        <td><?= $row['GENDER']; ?></td>
        <td><?= $row['DATE_OF_BIRTH']; ?> [<?= $row['AGE']; ?>]</td>
        <td><?= $row['DATE_OF_APPLICATION']; ?></td>
        <td>
            <a href="#" data-status="<?= $row['STATUS']; ?>" data-memid="<?= $row['MEMID']; ?>" data-membername="<?= $row['LASTNAME'] . ', ' . $row['FIRSTNAME'] . ' ' . $row['MIDDLENAME']; ?>" data-memremarks="<?= $row['STATUS_REMARKS']; ?>" onclick="actionsMember(this);" data-jario="tooltip" data-placement="top" title="PLEASE TAKE ACTIONS">
                <?php
                if ($row['STATUS'] == "PENDING") {
                    echo '<span class="text-warning">PENDING</span>';
                } elseif ($row['STATUS'] == "APPROVED") {
                    echo '<span class="text-primary">APPROVED</span>';
                } elseif ($row['STATUS'] == "DEACTIVE") {
                    echo '<span class="text-danger">DEACTIVE</span>';
                } elseif ($row['STATUS'] == "REJECTED") {
                    echo '<span class="text-danger">REJECTED</span>';
                } elseif ($row['STATUS']=="ARCHIVED") {
                    echo '<span class="text-danger">ARCHIVED</span>';
                }
                ?>
            </a>
        </td>
        <td align="right">
            <div class="btn-group">
                <a href="member_info.php?member_info=<?= $row['MEMID']; ?>&record=<?= $row['RECORD_NUMBER']; ?>&name=<?= str_replace(' ', '_', $row['LASTNAME'] . ', ' . $row['FIRSTNAME'] . ' ' . $row['MIDDLENAME']); ?>" class="btn btn-info btn-sm" data-jario="tooltip" data-placement="top" title="DETAILS">
                    <i class="fa-solid fa fa-user-check"></i>
                </a>
                <button class="btn btn-primary btn-sm"
                    data-memid="<?= $row['MEMID']; ?>"
                    data-fname="<?= $row['FIRSTNAME']; ?>"
                    data-mname="<?= $row['MIDDLENAME']; ?>"
                    data-lname="<?= $row['LASTNAME']; ?>"
                    data-gender="<?= $row['GENDER']; ?>"
                    data-dob="<?= $row['DATE_OF_BIRTH']; ?>"
                    data-age="<?= $row['AGE']; ?>"
                    data-mobile="<?= $row['MOBILE']; ?>"
                    data-barangay="<?= $row['BARANGAY']; ?>"
                    data-address="<?= $row['ADDRESS']; ?>"
                    data-education="<?= $row['EDUCATIONAL_BACKGROUND']; ?>"
                    data-employment="<?= $row['EMPLOYMENT_HISTORY']; ?>"
                    data-skills="<?= $row['SKILLS_QUALIFICATION']; ?>"
                    data-desired="<?= $row['DESIRED_LIVELIHOOD_PROGRAM']; ?>"
                    data-programchosen="<?= $row['EXP_LIVELIHOOD_PROGRAM_CHOSEN']; ?>"
                    data-currentliveli="<?= $row['CURRENT_LIVELIHOOD_SITUATION']; ?>"
                    data-reqtraining="<?= $row['REQUIRED_TRAINING']; ?>"
                    data-reasoninterest="<?= $row['REASON_INTERESTED_IN_LIVELIHOOD']; ?>"
                    onclick="editMember(this);" data-jario="tooltip" data-placement="top" title="EDIT">
                    <i class="fa-solid fa fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-sm"
                    data-memid="<?= $row['MEMID']; ?>"
                    data-membername="<?= $row['LASTNAME'] . ', ' . $row['FIRSTNAME'] . ' ' . $row['MIDDLENAME']; ?>"
                    onclick="deleteMember(this);" data-jario="tooltip" data-placement="top" title="DELETE">
                    <i class="fa-solid fa fa-trash"></i>
                </button>
            </div>
        </td>
<?php
    }
?>
?>