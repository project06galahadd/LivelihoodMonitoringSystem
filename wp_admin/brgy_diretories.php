<?php include "scripts.php"; ?>
<?php include "wp_admin/includes/archive_modal.php"; ?>

<script type="text/javascript">
function triggerArchiveModal(btn) {
  var id = btn.getAttribute('data-brgyid');
  var name = btn.getAttribute('data-brgyname');
  var status = btn.getAttribute('data-status');
  openArchiveModal(id, 'barangay', name, status, window.location.pathname);
}
</script>

<tbody>
<?php
$brgy = "SELECT * FROM tbl_barangay ORDER BY BRGY_NAME ASC";
$brgy_query = $conn->query($brgy);
while ($rows_brgy = $brgy_query->fetch_assoc()) {
?>
<tr id="row_<?= $rows_brgy['BRGY_ID']; ?>">
  <td><?= $rows_brgy['BRGY_NAME']; ?></td>
  <td><?= $rows_brgy['BRGY_CAPTAIN']; ?></td>
  <td><?= $rows_brgy['BRGY_CONTACT']; ?></td>
  <td>
    <button class="btn btn-sm" onclick="triggerArchiveModal(this)"
      data-brgyid="<?= $rows_brgy['BRGY_ID']; ?>"
      data-brgyname="<?= $rows_brgy['BRGY_NAME']; ?>"
      data-status="<?= $rows_brgy['STATUS'] ?? 'ACTIVE'; ?>"
      style="background-color: <?= (isset($rows_brgy['STATUS']) && $rows_brgy['STATUS'] == 'ARCHIVED') ? '#d9534f' : '#f0ad4e'; ?>; color: white;">
      <i class="fa <?= (isset($rows_brgy['STATUS']) && $rows_brgy['STATUS'] == 'ARCHIVED') ? 'fa-undo' : 'fa-archive'; ?>"></i>
      <?= (isset($rows_brgy['STATUS']) && $rows_brgy['STATUS'] == 'ARCHIVED') ? 'Unarchive' : 'Archive'; ?>
    </button>
  </td>
</tr>
<?php } ?> 