<?php @include "includes/header.php"; ?>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php @include "includes/navbar.php"; ?>
        <?php @include "includes/sidebar.php"; ?>
        
        <div class="content-wrapper">
            <div class="preloader flex-column justify-content-center align-items-center" style="background:rgba(0,0,0,0.40)">
                <img class="animation__shake" src="../dist/img/loader-3.gif" alt="AdminLTELogo" height="60" width="60">
            </div>

            <div class="content-wrapper">
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>ARCHIVED BARANGAYS</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
                                    <li class="breadcrumb-item"><a href="archive.php">ARCHIVES</a></li>
                                    <li class="breadcrumb-item active">BARANGAYS</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="content">
                    <div class="container-fluid">
                        <?php
                        if (isset($_SESSION['success'])) {
                            echo "<div class='alert alert-success'>" . $_SESSION['success'] . "</div>";
                            unset($_SESSION['success']);
                        }
                        if (isset($_SESSION['error'])) {
                            echo "<div class='alert alert-danger'>" . $_SESSION['error'] . "</div>";
                            unset($_SESSION['error']);
                        }
                        ?>

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">List of Archived Barangays</h3>
                            </div>
                            <div class="card-body">
                                <table id="barangayArchiveTable" class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Record #</th>
                                            <th>Barangay Name</th>
                                            <th>Barangay Captain</th>
                                            <th>Contact Number</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT * FROM tbl_barangay WHERE STATUS='ARCHIVED' ORDER BY BRGY_NAME ASC";
                                        $query = $conn->query($sql);
                                        while ($row = $query->fetch_assoc()):
                                        ?>
                                            <tr id="row_<?= $row['BRGY_ID']; ?>">
                                                <td><?= $row['BRGY_ID']; ?></td>
                                                <td><?= $row['BRGY_NAME']; ?></td>
                                                <td><?= $row['BRGY_CAPTAIN']; ?></td>
                                                <td><?= $row['BRGY_CONTACT']; ?></td>
                                                <td><span class="badge badge-danger"><?= $row['STATUS']; ?></span></td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="lgu_edit.php?id=<?= $row['BRGY_ID']; ?>" class="btn btn-info btn-sm" title="View">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                        <button class="btn btn-success btn-sm" title="Unarchive" onclick="triggerArchiveModal(this)"
                                                            data-id="<?= $row['BRGY_ID']; ?>"
                                                            data-type="barangay"
                                                            data-name="<?= $row['BRGY_NAME']; ?>"
                                                            data-status="ARCHIVED">
                                                            <i class="fa fa-undo"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <?php @include "includes/footer.php"; ?>
            <?php @include "includes/archive_modal.php"; ?>
        </div>
    </div>

    <?php @include "includes/scripts.php"; ?>
    <script>
        $(function() {
            $('#barangayArchiveTable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>
    <script type="text/javascript">
    function triggerArchiveModal(btn) {
        var id = btn.getAttribute('data-id');
        var type = btn.getAttribute('data-type');
        var name = btn.getAttribute('data-name');
        var status = btn.getAttribute('data-status');
        openArchiveModal(id, type, name, status, window.location.pathname);
    }
    </script>
</body>
</html> 