<?php
// ini_set('display_errors',1);
// ini_set('display_startup_errors',1);
// error_reporting(E_ALL);
// Sertakan file koneksi ke database
include('service/database.php');
session_start();
if (!isset($_SESSION['student_id'])) {
        header("Location: index.php"); // Redirect ke halaman login jika belum login
        exit();
    }

$stmt = $db->prepare("SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?");
$stmt->bind_param("ii", $_SESSION['student_id'], $_GET['id']);
$stmt->execute();
$result = $stmt->get_result();
$enrollment = $result->fetch_assoc();

if ($enrollment) {
    if ($enrollment['finish_at'] != NULL) {
        header("Location: dashboardSiswa.php?id=" . $_SESSION['student_id']);
        exit();
    }
}
// Pastikan bahwa ID course dikirim melalui URL
if (isset($_GET['id'])) {
    $course_id = $_GET['id'];
    
    // Query untuk mendapatkan data course berdasarkan ID
    $stmt = $db->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Periksa apakah course ditemukan
    if ($result->num_rows > 0) {
        $course = $result->fetch_assoc();
    } else {
        echo "Course tidak ditemukan.";
        exit();
    }
} else {
    echo "ID course tidak disediakan.";
    exit();
}

if (isset($_POST['finishCourse'])) {
    $course_id = $_GET['id'];
    $stmt = $db->prepare("UPDATE TABLE enrollments (student_id, course_id   ) values (?, ?) ON DUPLICATE KEY UPDATE");
    $stmt->bind_param("ii", $_SESSION['student_id'], $course_id);
    $stmt->execute();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Details</title>
    <style>
        /* Global Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url("bg2.png") fixed center center no-repeat;
            background-size: cover;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .contains {
            max-width: 80%;
            margin-top: 75px;
            margin-bottom: 75px;
            background: rgba(255, 255, 255, 0.2); /* Transparan */
            backdrop-filter: blur(3px); 
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.1);
            padding: 30px;
            border-radius: 15px;
            width: 100%;
            animation: fadeIn 1s ease-in-out;
        }

        h1, h2 {
            color: #333;
            margin-bottom: 20px;
        }

        h1 {
                        word-wrap: break-word; /* Memecah kata panjang */
  word-break: break-word; /* Memecah kata agar tetap di dalam card */
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
            font-size: 2.5em;
            letter-spacing: 1.5px;
        }

        a {
            text-decoration: none;
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            transition: all 0.3s ease;
            text-transform: uppercase;
        }

        a:hover {
            background-color: #0056b3;
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .course-info {
            margin: 20px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
        }

        th {
            text-align: left;
            background-color: #007bff;
            color: white;
            border-radius: 5px 0 0 5px;
        }

        td {
                        word-wrap: break-word; /* Memecah kata panjang */
  word-break: break-word; /* Memecah kata agar tetap di dalam card */
            max-width: 400px;
            background-color: #f9f9f9;
            border-radius: 0 5px 5px 0;
        }

        .content {
            margin-top: 30px;
            line-height: 1.7;
            color: #555;
        }

        .file-link a, .video-link iframe {
            display: block;
            margin-top: 10px;
            /* padding: 10px 15px; */
            text-align: center;
            background-color: #28a745;
            color: white;
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .file-link a:hover {
            background-color: #218838;
            transform: scale(1.05);
        }
      
        iframe {
            width: 50%;
            height: 300px;
            border-radius: 10px;
            /* border: 1px solid #007bff; */
            transition: transform 0.5s ease;
        }

        iframe:hover {
            transform: scale(1.01);
            /* box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);  */
        }

        .back-link {
            margin: 20px 0;
            display: inline-block;
            /* margin-top: 20px; */
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .back-link:hover {
            background-color: #0056b3;
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        /* Animations */
        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: scale(0.9);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes buttonHover {
            0% {
                transform: translateY(0);
            }
            100% {
                transform: translateY(-5px);
            }
        }

        a:hover {
            animation: buttonHover 0.3s forwards;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .contains {
                padding: 20px;
            }

            table, th, td {
                font-size: 0.9em;
            }

            h1 {
                font-size: 2em;
            }

            iframe {
                height: 300px;
            }
        }

        @media (max-width: 480px) {
            .contains {
                padding: 15px;
            }

            table, th, td {
                font-size: 0.8em;
            }

            h1 {
                font-size: 1.7em;
            }

            iframe {
                height: 250px;
            }

            a, .back-link {
                padding: 8px 16px;
                font-size: 0.9em;
            }
        }
    </style>
</head>

<body>

    <div class="contains">
        <h1><?php echo $course['title']; ?></h1>
        <a href="profile.php?id=<?php echo $course['teacher_id']; ?>" class="back-link">Profil Guru</a>

        <div class="course-info">
            <table cellpadding="5" cellspacing="10">
                <colgroup>
                    <col style="width: 20%;">
                    <col style="width: 80%;">
                </colgroup>
                <tr>
                    <th>Kategori</th>
                    <td><?php echo $course['category']; ?></td>
                </tr>
                <tr>
                    <th>Level</th>
                    <td><?php echo $course['level']; ?></td>
                </tr>
                <tr>
                    <th>Jadwal</th>
                    <td><?php echo date("D, d M Y / H:i", strtotime($course['jadwal'])); ?></td>
                </tr>
                <tr>
                    <th>Dibuat Pada</th>
                    <td><?php echo date("D, d M Y / H:i", strtotime($course['created_at'])); ?></td>
                </tr>
                <tr>
                    <th>Deskripsi</th>
                    <td><?php echo $course['description']; ?></td>
                </tr>
            </table>
        </div>

        <div class="content">
            <h2>Informasi Pembelajaran</h2>
            <p><?php echo nl2br($course['content']); ?></p>

            <div class="file-link">
                <h2>Link Materi</h2>
                <?php if (!empty($course['file_url'])): ?>
                    <a href="downloadfile.php?file=<?php echo $course['file_url']; ?>" download>Download Materi</a>
                <?php else: ?>
                    <p>File materi tidak tersedia.</p>
                <?php endif; ?>
            </div>

            <div class="video-link">
                <h2>Link Video Pembelajaran</h2>
                <?php if (!empty($course['video_url'])): ?>
                    <iframe src="<?php echo $course['video_url']; ?>" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                <?php else: ?>
                    <p>Video pembelajaran tidak tersedia.</p>
                <?php endif; ?>
            </div>
        </div>

        <a href="dashboardSiswa.php" class="back-link">Kembali ke Dashboard</a>
        <br>
        <a style="background-color: green"; href="finishCourse.php?id=<?php echo $course['id']; ?>" class="back-link" >Finish</a>
    </div>

</body>
</html>
