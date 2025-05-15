<?php
session_start();
require_once "includes/conn.php";

// Check if user is logged in and has admin role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header('Location: signin.php');
    exit();
}

// Get user data
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$fullname = $_SESSION['fullname'];

// Fetch news and announcements
try {
    $stmt = $conn->prepare("
        SELECT na.*, u.fullname as posted_by_name 
        FROM news_announcements na 
        JOIN tbl_users u ON na.posted_by = u.user_id 
        ORDER BY na.date_posted DESC
    ");
    $stmt->execute();
    $news_result = $stmt->get_result();
} catch (Exception $e) {
    error_log("Error fetching news: " . $e->getMessage());
    $news_result = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News & Announcements | MSWD</title>
    <link rel="icon" type="image/png" href="/LivelihoodMonitoringSystem/dist/img/Logo.png">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css">
    <style>
        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
            letter-spacing: 0.5px;
        }

        .btn-add {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-add:hover {
            background: linear-gradient(135deg, #2980b9, #2471a3);
            color: white;
            transform: translateY(-1px);
        }

        .news-image {
            width: 100px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-active {
            background-color: #2ecc71;
            color: white;
        }

        .status-inactive {
            background-color: #e74c3c;
            color: white;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include 'includes/navbar.php'; ?>

        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="page-title">NEWS & ANNOUNCEMENTS</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                                <li class="breadcrumb-item active">News & Announcements</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Manage News & Announcements</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-add" data-toggle="modal" data-target="#addNewsModal">
                                            <i class="fas fa-plus mr-2"></i>Add New
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="newsTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Image</th>
                                                <th>Title</th>
                                                <th>Content</th>
                                                <th>Posted By</th>
                                                <th>Date Posted</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($news_result && $news_result->num_rows > 0): ?>
                                                <?php while ($news = $news_result->fetch_assoc()): ?>
                                                    <tr>
                                                        <td>
                                                            <?php if (!empty($news['image'])): ?>
                                                                <img src="../uploads/news/<?php echo htmlspecialchars($news['image']); ?>" 
                                                                     class="news-image" alt="News Image">
                                                            <?php else: ?>
                                                                <span class="text-muted">No Image</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($news['title']); ?></td>
                                                        <td><?php echo substr(htmlspecialchars($news['content']), 0, 100) . '...'; ?></td>
                                                        <td><?php echo htmlspecialchars($news['posted_by_name']); ?></td>
                                                        <td><?php echo date('M d, Y h:i A', strtotime($news['date_posted'])); ?></td>
                                                        <td>
                                                            <span class="status-badge <?php echo $news['status'] === 'active' ? 'status-active' : 'status-inactive'; ?>">
                                                                <?php echo ucfirst($news['status']); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-info" onclick="viewNews(<?php echo $news['id']; ?>)">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-primary" onclick="editNews(<?php echo $news['id']; ?>)">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteNews(<?php echo $news['id']; ?>)">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Add News Modal -->
    <div class="modal fade" id="addNewsModal" tabindex="-1" role="dialog" aria-labelledby="addNewsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addNewsModalLabel">Add News or Announcement</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addNewsForm" action="process_news.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="content">Content</label>
                            <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="image">Image (Optional)</label>
                            <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit News Modal -->
    <div class="modal fade" id="editNewsModal" tabindex="-1" role="dialog" aria-labelledby="editNewsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editNewsModalLabel">Edit News or Announcement</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editNewsForm" action="process_news.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_title">Title</label>
                            <input type="text" class="form-control" id="edit_title" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_content">Content</label>
                            <textarea class="form-control" id="edit_content" name="content" rows="5" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit_image">Image (Optional)</label>
                            <input type="file" class="form-control-file" id="edit_image" name="image" accept="image/*">
                            <small class="form-text text-muted">Leave empty to keep current image</small>
                        </div>
                        <div class="form-group">
                            <label for="edit_status">Status</label>
                            <select class="form-control" id="edit_status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View News Modal -->
    <div class="modal fade" id="viewNewsModal" tabindex="-1" role="dialog" aria-labelledby="viewNewsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewNewsModalLabel">View News or Announcement</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="view_image" class="text-center mb-3"></div>
                    <h3 id="view_title" class="mb-3"></h3>
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="far fa-calendar-alt mr-1"></i>
                            <span id="view_date"></span>
                        </small>
                        <br>
                        <small class="text-muted">
                            <i class="far fa-user mr-1"></i>
                            Posted by: <span id="view_author"></span>
                        </small>
                    </div>
                    <div id="view_content" class="news-content"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css"></script>
    <script>
        $(document).ready(function() {
            $('#newsTable').DataTable({
                "responsive": true,
                "autoWidth": false
            });

            // Handle form submissions
            $('#addNewsForm, #editNewsForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        try {
                            const result = JSON.parse(response);
                            if(result.success) {
                                alert(result.message);
                                location.reload();
                            } else {
                                alert(result.message || 'Error processing request');
                            }
                        } catch(e) {
                            alert('Error processing request');
                        }
                    },
                    error: function() {
                        alert('Error processing request');
                    }
                });
            });
        });

        function viewNews(id) {
            $.ajax({
                url: 'get_news.php',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    try {
                        const result = JSON.parse(response);
                        if(result.success) {
                            const news = result.data;
                            $('#view_title').text(news.title);
                            $('#view_date').text(new Date(news.date_posted).toLocaleString());
                            $('#view_author').text(news.posted_by_name);
                            $('#view_content').html(news.content.replace(/\n/g, '<br>'));
                            
                            if(news.image) {
                                $('#view_image').html(`<img src="../uploads/news/${news.image}" class="img-fluid" alt="News Image">`);
                            } else {
                                $('#view_image').empty();
                            }
                            
                            $('#viewNewsModal').modal('show');
                        } else {
                            alert(result.message || 'Error fetching news details');
                        }
                    } catch(e) {
                        alert('Error fetching news details');
                    }
                },
                error: function() {
                    alert('Error fetching news details');
                }
            });
        }

        function editNews(id) {
            $.ajax({
                url: 'get_news.php',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    try {
                        const result = JSON.parse(response);
                        if(result.success) {
                            const news = result.data;
                            $('#edit_id').val(news.id);
                            $('#edit_title').val(news.title);
                            $('#edit_content').val(news.content);
                            $('#edit_status').val(news.status);
                            $('#editNewsModal').modal('show');
                        } else {
                            alert(result.message || 'Error fetching news details');
                        }
                    } catch(e) {
                        alert('Error fetching news details');
                    }
                },
                error: function() {
                    alert('Error fetching news details');
                }
            });
        }

        function deleteNews(id) {
            if(confirm('Are you sure you want to delete this news/announcement?')) {
                $.ajax({
                    url: 'process_news.php',
                    type: 'POST',
                    data: {
                        action: 'delete',
                        id: id
                    },
                    success: function(response) {
                        try {
                            const result = JSON.parse(response);
                            if(result.success) {
                                alert(result.message);
                                location.reload();
                            } else {
                                alert(result.message || 'Error deleting news');
                            }
                        } catch(e) {
                            alert('Error deleting news');
                        }
                    },
                    error: function() {
                        alert('Error deleting news');
                    }
                });
            }
        }
    </script>
</body>
</html> 