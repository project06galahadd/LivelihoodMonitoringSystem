<?php
include "wp_admin/includes/conn.php";

if (isset($_POST["sdate"])) {
  $sdate = $conn->real_escape_string($_POST["sdate"]);

  $sql = "SELECT *, slots_date, start_time FROM tbl_slot_date WHERE slots_date = '$sdate'";
  $query = $conn->query($sql);

  if ($query && $query->num_rows > 0) {
    while ($row = $query->fetch_assoc()) {
      $selected_time = $row['start_time'];
      $total_slots = (int)$row['slots'];

      $sql_slots = "SELECT COUNT(*) as TotalBooked 
                          FROM tbl_interview 
                          WHERE SELECTED_DATE = '$sdate' AND SELECTED_TIME = '$selected_time'";
      $slot_result = $conn->query($sql_slots);
      $booked = ($slot_result && $slot_result->num_rows > 0)
        ? (int)$slot_result->fetch_assoc()['TotalBooked']
        : 0;

      $available_slots = $total_slots - $booked;

      if ($available_slots <= 0) {
        echo "
                <div class='form-check'>
                    <input class='form-check-input' type='radio' disabled>
                    <label class='text-danger form-check-label'>
                        $selected_time - Fully Booked
                    </label>
                </div>";
      } else {
        echo "
                <div class='form-check'>
                    <input type='radio' 
                           data-stime='$selected_time' 
                           onclick='getTime(this);' 
                           class='form-check-input timeCheckbox' 
                           name='SELECTED_TIME' required>
                    <label class='text-success form-check-label'>
                        $selected_time - Available: $available_slots
                    </label>
                </div>";
      }
    }
  } else {
    echo "<div class='text-muted'>No slots found for the selected date.</div>";
  }
} else {
  echo "<div class='text-danger'>Invalid request. Date is required.</div>";
}
