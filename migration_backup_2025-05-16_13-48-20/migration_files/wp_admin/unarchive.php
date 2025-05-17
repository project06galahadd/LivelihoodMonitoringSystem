<?php @include "includes/header.php"; ?>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <?php @include "includes/navbar.php"; ?>
        <?php @include "includes/sidebar.php"; ?>
        <div class="content-wrapper">
            <div class="preloader flex-column justify-content-center align-items-center" style="background:rgba(0,0,0,0.40)">
                <img class="animation__shake" src="../dist/img/loader-3.gif" alt="AdminLTELogo" height="60" width="60">
            </div>

            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>UNARCHIVED MEMBERS</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
                                <li class="breadcrumb-item active">UNARCHIVED</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">

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
                                    <h3 class="card-title">List of Active Members</h3>
                                </div>
                                <div class="card-body">
                                    <table id="unarchiveTable" class="table table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>RECORD NO</th>
                                                <th>NAME</th>
                                                <th>SEX</th>
                                                <th>DOB</th>
                                                <th>STATUS</th>
                                                <th>ACTIONS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT * FROM tbl_members WHERE STATUS='ACTIVE' ORDER BY LASTNAME ASC";
                                            $query = $conn->query($sql);
                                            $count = 1;
                                            while ($row = $query->fetch_assoc()):
                                            ?>
                                                <tr>
                                                    <td><?= $count++; ?></td>
                                                    <td><?= $row['RECORD_NUMBER']; ?></td>
                                                    <td><?= $row['LASTNAME'] . ', ' . $row['FIRSTNAME'] . ' ' . $row['MIDDLENAME']; ?></td>
                                                    <td><?= $row['GENDER']; ?></td>
                                                    <td><?= $row['DATE_OF_BIRTH']; ?></td>
                                                    <td><span class="text-success"><?= $row['STATUS']; ?></span></td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="member_info.php?member_info=<?= $row['MEMID']; ?>" class="btn btn-info btn-sm" title="View">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                            <a href="action_archive.php?memid=<?= $row['MEMID']; ?>" class="btn btn-danger btn-sm" title="Archive">
                                                                <i class="fa fa-archive"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php @include "includes/footer.php"; ?>
    </div>

    <?php @include "includes/scripts.php"; ?>
</body>

</html>