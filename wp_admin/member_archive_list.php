<?php
// Query for archived members
$sql = "SELECT * FROM tbl_members WHERE STATUS = 'ARCHIVED'";
$query = $conn->query($sql);

while ($row = $query->fetch_assoc()) {
    echo "
    <tr id='row_{$row['MEMID']}'>
      <td>" . $row['MEMID'] . "</td>
      <td>" . $row['fullname'] . "</td>
      <td>" . $row['email'] . "</td>
      <td>
        <button class='btn btn-success btn-sm' onclick=\"triggerArchiveModal(this)\"
                data-id='" . $row['MEMID'] . "'
                data-type='member'
                data-name='" . $row['fullname'] . "'
                data-status='ARCHIVED'>
          <i class='fa fa-undo'></i> Unarchive
        </button>
      </td>
    </tr>
  ";
}
