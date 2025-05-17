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
                                <h1>ARCHIVED LIVELIHOOD PROGRAMS</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
                                    <li class="breadcrumb-item"><a href="archive.php">ARCHIVES</a></li>
                                    <li class="breadcrumb-item active">LIVELIHOOD</li>
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
                                <h3 class="card-title">List of Archived Livelihood Programs</h3>
                            </div>
                            <div class="card-body">
                                <table id="livelihoodArchiveTable" class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>PROGRAM NAME</th>
                                            <th>DESCRIPTION</th>
                                            <th>STATUS</th>
                                            <th>ACTIONS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT * FROM tbl_livelihood WHERE STATUS='ARCHIVED' ORDER BY PROGRAM_NAME ASC";
                                        $query = $conn->query($sql);
                                        $count = 1;
                                        while ($row = $query->fetch_assoc()):
                                        ?>
                                            <tr id="row_<?= $row['LIVELIHOODID']; ?>">
                                                <td><?= $count++; ?></td>
                                                <td><?= $row['PROGRAM_NAME']; ?></td>
                                                <td><?= $row['DESCRIPTION']; ?></td>
                                                <td><span class="text-secondary"><?= $row['STATUS']; ?></span></td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="livelihood_edit.php?id=<?= $row['LIVELIHOODID']; ?>" class="btn btn-info btn-sm" title="View">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                        <button class="btn btn-success btn-sm" title="Unarchive" onclick="triggerArchiveModal(this)"
                                                            data-id="<?= $row['LIVELIHOODID']; ?>"
                                                            data-type="livelihood"
                                                            data-name="<?= $row['PROGRAM_NAME']; ?>"
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
            $('#livelihoodArchiveTable').DataTable({
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