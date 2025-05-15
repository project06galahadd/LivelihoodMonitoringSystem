<?php include "header.php"?>

<?php
    $sql="SELECT * FROM tbl_members";
    $query=$conn->query($sql);
    while($row=$query->fetch_assoc()){
?>
<img src="data:image/jpg;charset=utf8;base64,<?=base64_encode($row['UPLOAD_ID']); ?>" width="250" height="250" class="img-thumbnail">
<img src="data:image/jpg;charset=utf8;base64,<?=base64_encode($row['UPLOAD_WITH_SELFIE']); ?>" width="250" height="250" class="img-thumbnail">
<?php } ?>