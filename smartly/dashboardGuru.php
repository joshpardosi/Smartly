<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
    ob_start();
include 'service/database.php';
// Pastikan pengguna telah login
if (!isset($_SESSION['teacher_id'])) {
    header("Location: index.php"); // Redirect ke halaman login jika belum login
    exit();

}

$teacher_id = $_SESSION['teacher_id'];
// echo "Hello, " . $teacher_id . "!";
// Ambil data profil dari database berdasarkan sesi pengguna yang login
$stmt = $db->prepare("SELECT * FROM login WHERE id = ?");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Smart Home</title>
</head>
<style>
    /* Style untuk container dropdown */
    .dropdown-container {
        position: relative;
        display: inline-block;
    }

    /* Style tombol dropdown */
    .dropdown-btn {
        /* background-color: #4CAF50; */
        /* color: white; */
        background: transparent;
        /* padding: 16px; */
        padding: 0;
        font-size: 16px;
        border: none;
        cursor: pointer;
    }

    /* Konten dropdown yang disembunyikan secara default */
    .dropdown-content {
        /* background: linear-gradient(180deg, rgb(44, 124, 235) 0%, rgba(0,212,255,0.5) 100%); */
        background: rgb(44, 165, 245);
        border-radius: 20px;
        /* margin-top: 35px; */
        /* top: 30px; */
        right: -43px;
        display: none;
        position: absolute;
        /* background-color: #f9f9f9; */
        /* min-width: 160px; */
        /* box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); */
        z-index: 1;
        list-style-type: none;
        padding: 0px;
        margin: 0;
    }

    /* Style untuk setiap item dalam dropdown */
    .dropdown-content li a {
        /* list-style: none; */
        color: black;
        padding: 12px 35px 12px 0px;
        margin-left: 0;
        text-align: center;
        /* padding: 12px 0; */
        text-decoration: none;
        display: block;
        font-weight: bold;
        color: white;
        /* White text */
        transition: all 0.4s ease;
        /* Animation */
    }

    .dropdown-content li a:hover {
        background: #170EAE;
        background: linear-gradient(to right, #2600ff 0%, #006aff 50%, #0073ff 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        transform: scale(1.1);
    }

    /* Tampilkan dropdown saat tombol dihover */
    .dropdown-container:hover .dropdown-content {
        display: block;
        animation: fadeIn 0.3s ease-in-out;
    }

    #loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
</style>

<body>
    <!-- <div id="loader">
        <div class="spinner"></div>
    </div> -->

    <nav class="container-fluid">
        <ul class="nav-left">
            <li><strong><i>Smartly</i></strong></li>
        </ul>
        <ul class="nav-right">
            <li><a id="tombolnavutama" href="logout.php">Logout</a></li>
            <li><a id="tombolnavutama" href="index.php#about">About</a></li>
            <li><a id="tombolnavutama" class="tombolProfil" onclick="toggleSidebar('mySidebar')">Profile</a></li>
            <li><a id="tombolnavutama" class="tombolMenu" onclick="toggleSidebar('myMenuSidebar')">Menu</a></li>
        </ul>
    </nav>

    <?php
    // Jika form ubah password di-submit


    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Ambil password saat ini dari database

        // Verifikasi apakah password lama sesuai

        if ($current_password == $_SESSION['password']) {
            // Cek apakah password baru dan konfirmasi password sesuai
            if ($new_password === $confirm_password) {
                // Hash password baru dan update di database
                // $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE login SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $new_password, $teacher_id);

                if ($stmt->execute()) {
                    echo "<div class='notificationsuccess'>
                        <span class='icon'>✔</span>
                        <span class='message'>Password Berhasil diubah!</span>
                        </div>";;
                } else {
                    echo "<div class='notificationfail'>
            <span class='icon'>⚠️</span>
            <span class='message'>Terjadi kesalahan saat mengubah password.</span> </div>";
                }
            } else {
                echo "<div class='notificationfail'>
            <span class='icon'>⚠️</span>
            <span class='message'>Konfirmasi password tidak cocok.</span> </div>";
            }
        } else {
            echo "<div class='notificationfail'>
            <span class='icon'>⚠️</span>
            <span class='message'>Password saat ini tidak valid.</span> </div>";
        }
    }


    if (isset($_POST['simpanProfil'])) {
        $bio = $_POST['bio'];
        $kontak = $_POST['kontak'];
        $user_id = $_SESSION['teacher_id']; // Gantilah dengan ID pengguna saat ini dari sesi
        $stmt = $db->prepare("UPDATE login SET bio = ?, kontak = ? WHERE id = ?");
        $stmt->bind_param("ssi", $bio, $kontak, $user_id);
        $stmt->execute();
        // Ambil detail file
        if (isset($_FILES['pp'])) {
            $file = $_FILES['pp'];
            $file_name = $file['name'];
            $file_tmp = $file['tmp_name'];
            $file_error = $file['error'];

            // Tentukan direktori untuk menyimpan foto
            $upload_dir = 'uploads/pp/';

            // Periksa apakah ada kesalahan saat mengunggah file
            if ($file_error === 0) {
                // Tentukan path tujuan untuk menyimpan file
                $file_destination = $upload_dir . $file_name;

                // Pindahkan file ke direktori tujuan
                if (move_uploaded_file($file_tmp, $file_destination)) {
                    // Simpan path file ke database
                    $user_id = $_SESSION['teacher_id']; // Gantilah dengan ID pengguna saat ini dari sesi
                    $stmt = $db->prepare("UPDATE login SET foto = ? WHERE id = ?");
                    $stmt->bind_param("si", $file_name, $user_id);

                    if ($stmt->execute()) {
                        echo "<div class='notificationsuccess'>
                        <span class='icon'>✔</span>
                        <span class='message'>Profil berhasil disimpan!</span>
                        </div>";
                    }
                }
            }
        }
    }
    if (isset($_POST['toggle_quiz_status'])) {
        $quiz_id = $_POST['quiz_id'];
        $status = $_POST['status'];

        $stmt = $db->prepare("UPDATE quizzes SET is_active = ? WHERE id = ?");
        $stmt->bind_param("ii", $status, $quiz_id);
        $stmt->execute();

        // Redirect after status change
        header("Location: dashboardGuru.php");
        exit();  // Make sure to exit after the header
    }
    if (isset($_POST['create_quiz'])) {
        $title = $_POST['title'];
        $teacher_id = $_SESSION['teacher_id'];

        // Insert quiz ke database
        $stmt = $db->prepare("INSERT INTO quizzes (title, teacher_id, is_active) VALUES (?, ?, 0)");
        $stmt->bind_param("si", $title, $teacher_id);
        $stmt->execute();

        // Redirect setelah berhasil menambah quiz
        header("Location: dashboardGuru.php");
        ob_end_flush();
        exit();
    }
    

    ?>
    <style>
        body {
            /* background: url(bg2.png) fixed center center no-repeat;
            background-size: cover; */
            background: linear-gradient(90deg, rgba(2, 2, 248, 0.1) 0%, rgba(4, 151, 253, 0.2) 50%, rgba(2, 2, 248, 0.1) 100%);
            font-family: 'Arial', sans-serif;
            /* margin: 0; */
            /* padding: 0; */
        }

        .course h2,
        .kuis h2,
        .stats h2 {
            text-align: center;
            margin-top: 50px;
            font-size: 30px;
        }

        table {
            width: 95%;
            border-collapse: collapse;
            margin: 0px auto;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        table th,
        table td {
            padding: 12px 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        
        table .padding {
            padding: 12px 10px;
            
        }
        
        table .paddingKanan{
            padding: 12px 10px 12px 0px;
        }

        table th {
            word-wrap: break-word; /* Membungkus kata */
            word-break: break-all; /* Memastikan teks panjang akan dipisah */
            white-space: normal; /* Mengizinkan pemisahan baris */
            max-width: 150px; /* Batas maksimal lebar */
            overflow-wrap: break-word; /* Membungkus teks terlalu panjang */
            background-color: #0073ff;
            color: white;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 14px;
        }

        table td {
            word-wrap: break-word; /* Membungkus kata */
            word-break: break-all; /* Memastikan teks panjang akan dipisah */
            white-space: normal; /* Mengizinkan pemisahan baris */
            overflow-wrap: break-word; /* Membungkus teks terlalu panjang */
            font-size: 14px;
            color: #555;
        }
        
        table .kuisTitle {
            max-width: 75px; /* Batas maksimal lebar */
        }

        table .kuisStatus {
            max-width: 10px; /* Batas maksimal lebar */
        }

        table .kuisSubmitted {
            max-width: 20px; /* Batas maksimal lebar */
        }

        table .kuisAttempt {
            max-width: 10px; /* Batas maksimal lebar */           
        }

        table .kuisCreated {
            max-width: 40px; /* Batas maksimal lebar */
        }

        table .kuisAction { 
            max-width: 1px; /* Batas maksimal lebar */
        }
        
        
        
        
        table .tittle {
            max-width: 50px; /* Batas maksimal lebar */
        }

        table .deskripsi {
            max-width: 50px; /* Batas maksimal lebar */
        }

        table .kategori {
            max-width: 40px; /* Batas maksimal lebar */
        }

        table .level {
            max-width: 10px; /* Batas maksimal lebar */           
        }

        table .jadwal {
            max-width: 30px; /* Batas maksimal lebar */
        }

        table .addCourse { 
            max-width: 30px; /* Batas maksimal lebar */
        }

        .kuisActive {
            margin-left: 40px;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tbody tr:hover {
            background-color: #f1f1f1;
            transition: background-color 0.3s ease;
        }

        table tbody tr td:last-child {
            text-align: center;
        }

        table td a {
            padding: 6px 10px;
            margin: 0 10px;
            text-decoration: none;
            color: #fff;
            background-color: #f39c12;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        table td a:hover {
            background-color: #e67e22;
        }

        thead {
            background-color: #2ecc71;
        }

        .tombolProfil, .tombolMenu {
            cursor: pointer;
        }



        .kuis, .stats, #addCourseModal, #addQuizModal {
            animation: slideUp 0.75s forwards;
        }

        .course{
            animation: fadeIn 0.75s ease-in-out;
        }

        html {
            scroll-behavior: smooth; /* Scroll halus untuk seluruh halaman */
        }
    </style>

    <!-- <h1> DASHBOARD GURU</h1> -->
    <section id="Course">
        <div class="course">
            <h2>Kursus</h2>
            <table class="daftarKursus">
                <tr>
                    <th class="tittle"class="padding">Tittle</th>
                    <th class="deskripsi">Deskripsi</th>
                    <th class="kategori">Kategori</th>
                    <th class="level">Level</th>
                    <th class="jadwal">Jadwal</th>
                    <th Class="addCourse"class="paddingKanan"><?php include 'dbGuru/addCourse.php'; ?></th>
                </tr>


                <?php
                // session_start();


                $data = mysqli_query($db, "SELECT * FROM courses where teacher_id = '$_SESSION[teacher_id]'");
                if (mysqli_num_rows($data) == 0) {
                    echo "<tr><td colspan='6'>No Course Found.</td></tr>";
                } else if (mysqli_num_rows($data) > 0) {
                    while ($d = mysqli_fetch_array($data)) {
                ?>
                        <tr>
                            <td class="tittle"><?php echo $d['title']; ?></td>
                            <td class="deskripsi"><?php echo $d['description']; ?></td>
                            <td class="kategori"><?php echo $d['category']; ?></td>
                            <td class="level"><?php echo $d['level']; ?></td>
                            <td class="jadwal"><?php echo date("D, d M Y / H:i", strtotime($d['jadwal'])); ?></td>
                            <td class="addCourse" class="paddingKanan"><a href="dbGuru/editCourse.php?id=<?php echo $d['id']; ?>">Edit Course</a></td>

                        </tr>
                <?php
                    }
                }
                ?>
            </table>

            <!-- <a href="dbGuru/kuis.php">kuis</a> -->
        </div>
    </section>
    <style>
        .modal-content form button {
            margin-top:20px;
        }
    </style>
    <div id="addQuizModal" class="modal">
                    <!-- Konten modal -->
                    <div class="modal-content">
                        <button class="closedQuiz">&times;</button>
                        <h2>Add Quiz</h2>
                        <form action="dashboardGuru.php" method="POST">
                            <div class="bawah">
                                <input type="text" name="title" placeholder="Quiz Title" required>
                            </div>
                            
                            <button type="submit" name="create_quiz">Create Quiz</button>
                        </form>
                    </div>
    </div>

    <section id="Quizz">
        <div class="kuis">
            <h2>Kuis</h2>
            <?php
            // Fetch all quizzes created by the teacher
            $quiz_query = mysqli_query($db, "SELECT * FROM quizzes WHERE teacher_id = {$_SESSION['teacher_id']}");
            $sumQuiz = mysqli_num_rows($quiz_query);
            $isActive_query = mysqli_query($db, "SELECT count(*) as is_active FROM quizzes WHERE teacher_id = {$_SESSION['teacher_id']} AND is_active = 1");
            $isActive = mysqli_fetch_assoc($isActive_query);
            $sumInActive = $sumQuiz - $isActive['is_active'];
            $sumActive = $isActive['is_active'];


            ?>
            <p class="kuisActive">Total Quizzes : <?php echo $sumQuiz; ?> ( Active : <?php echo $sumActive; ?> / Inactive : <?php echo $sumInActive; ?> )</p>

            <table>
                <thead>
                    <tr>
                        <th class="padding" class="kuisTitle">Title</th>
                        <th class="kuisStatus">Status</th>
                        <th class="kuisSubmitted">Submitted</th>
                        <th class="kuisAttempt">Attempt</th>
                        <th class="kuisCreated">Created At</th>
                        <th class="paddingKanan" class="kuisAction"><button id="addQuiz">Add Quiz</button></th>
                    </tr>
                </thead>
                <tbody>
            <?php
            if ($sumQuiz == 0) {
                echo "<tr><td colspan='6'>No Quiz Found</td></tr>";
            } else {
                while ($quiz = mysqli_fetch_assoc($quiz_query)) {
                    $countSubmitted_query = mysqli_query($db, "SELECT * FROM submissions WHERE quiz_id = {$quiz['id']}");
                    if (mysqli_num_rows($countSubmitted_query) == 0) {
                        $countSubmitted = 0;
                    } else {
                        $countSubmitted = mysqli_num_rows($countSubmitted_query);  
                    }?>
                    <tr>
                        <td class="kuisTitle"><?php echo $quiz['title']; ?></td>
                        <td class="kuisStatus"><?php echo ($quiz['is_active'] ? "Active" : "Inactive"); ?></td>
                        <td class="kuisSubmitted"><?php echo $countSubmitted; ?></td>
                        <td class="kuisAttempt"><?php echo $quiz['chance']; ?></td>
                        <td class="kuisCreated"><?php echo date("D, d M Y / H:i", strtotime($quiz['created_at'])); ?></td>
                        
                        <?php
                        $new_status = $quiz['is_active'] ? 0 : 1;
                        $status_button_text = $quiz['is_active'] ? "Deactivate" : "Activate";
                        ?>
                        
                        <td class="kuisAction">
                            <form method="POST" action="dashboardGuru.php" style="display:inline;">
                                <input type="hidden" name="quiz_id" value="<?php echo $quiz['id']; ?>">
                                <input type="hidden" name="status" value="<?php echo $new_status; ?>">
                                <button type="submit" name="toggle_quiz_status"><?php echo $status_button_text; ?></button>
                            </form>
                            
                            <a href="dbGuru/editSoal.php?quiz_id=<?php echo $quiz['id']; ?>">Manage Questions</a>
                        </td>
                    </tr>

                    <?php
                }
            }

            echo "</tbody>";
            echo "</table>";
            ?>
        </div>
    </section>

    </section>
    <section id="stats">
        <div class="stats">
            <h2>Statistics</h2>
            <?php include('dbGuru/stats.php'); ?>
        </div>
    </section>
    <!-- <h1>Teacher Dashboard</h1> -->
    <?php include('layout/footer.html'); ?>







    <!-- testttttttttttttttttttttttttttttt -->
    <style>
        /* Sidebar styling */
        .sidebar {
            background-color: #222; /* Warna latar belakang lebih gelap */
            color: #f1f1f1; /* Warna teks yang lebih terang */
            box-sizing: border-box;
            overflow-y: auto;
            width: 320px;
            /* background-color: #333; */
            /* background: rgba(255, 255, 255, 0.4); */
            /* Transparan */
            /* backdrop-filter: blur(3px); */
            border-radius: 0 10px 10px 0;
            padding: 40px;
            color: white;
            position: fixed;
            top: 0;
            left: -400px;
            height: 100%;
            transition: left 0.3s ease;
        }


        .sidebar.show {
            left: 0;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 24px;
            color: white;
            cursor: pointer;
        }

        .profile-picture {
            display: block;
            margin: 0 auto 20px;
            border-radius: 50%;
            width: 150px;
            height: 150px;
            border: 3px solid #007bff;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            color: white;
            margin: 40px 0 20px 0;
            color: white;
        }

        .sidebar .profile-info p {
            margin: 10px 0;
            color: white;
            font-weight: bold;
        }

        .sidebar label {
            
            display: block;
            width: 100%;
            font-family: Arial, Helvetica, sans-serif;
            color: white;
            margin-bottom: 10px;
        }
        .sidebar input,
        .sidebar textarea {
            display: block;
            width: 100%;
            font-family: Arial, Helvetica, sans-serif;
            color: black;
            margin-bottom: 10px;
        }

        .sidebar input,
        .sidebar textarea {
            padding: 8px;
            border: none;
            border-radius: 4px;
        }

        .sidebar button {
            padding: 10px;
            background-color: #28a745;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
        }

        .sidebar button:hover {
            background-color: #218838;
        }

        textarea {
            width: 100%;
        }

        /* Tambahkan tombol silang di kanan atas */
        .sidebar .close-btn {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 24px;
            color: white;
            cursor: pointer;
        }

        .profile-picture {
            display: block;
            margin: 0 auto 20px;
            border-radius: 50%;
            width: 150px;
            height: 150px;
            /* object-fit: cover; */
            border: 3px solid #007bff;
        }

        @keyframes slideIn {
            0% {
                left: -400px;
            }

            100% {
                left: 0;
            }
        }

        @keyframes slideOut {
            0% {
                left: 0;
            }

            100% {
                left: -400px;
            }
        }

        .sidebar.show {
            animation: slideIn 0.5s forwards;
        }

        .sidebar.hide {
            animation: slideOut 0.5s forwards;
        }

        /* Styling khusus untuk Sidebar Menu */
#myMenuSidebar {
    background-color: #222; /* Warna latar belakang lebih gelap */
    color: #f1f1f1; /* Warna teks yang lebih terang */
    font-family: Arial, sans-serif; /* Gaya font */
    padding-top: 40px;
    border-left: 3px solid #444; /* Garis tepi untuk mempertegas */
}

/* Gaya untuk judul menu */
#myMenuSidebar h2 {
    font-size: 24px;
    color: #eaeaea;
    text-align: center;
    padding-bottom: 15px;
    border-bottom: 1px solid #555;
    margin: 0 15px 20px;
}

/* Gaya untuk setiap tautan menu */
#myMenuSidebar ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

#myMenuSidebar ul li {
}

#myMenuSidebar ul li a {
    padding: 20px 20px;
    border-radius: 10px;
    color: #ddd;
    font-size: 18px;
    text-decoration: none;
    display: block;
    transition: background-color 0.3s, color 0.3s;
}

#myMenuSidebar ul li a:hover {
    background-color: #333;
    color: #fff;
}

/* Gaya tombol tutup */
#myMenuSidebar .close-btn {
    font-size: 30px;
    color: #f1f1f1;
    cursor: pointer;
    position: absolute;
    top: 10px;
    right: 20px;
    transition: color 0.3s;
}

#myMenuSidebar .close-btn:hover {
    color: #bbb;
}

    </style>




    <!-- Sidebar -->
    <div class="sidebar" id="mySidebar">
        <span class="close-btn" onclick="toggleSidebar('mySidebar')">&times;</span>
        <h2>Profil Guru</h2>
        <div class="profile-info">
            <img src="download.php?file=<?php echo $profile['foto']; ?>" class="profile-picture">
            <p><strong>Nama:</strong> <?php echo $_SESSION['name']; ?></p>
            <p><strong>Username:</strong> <?php echo $_SESSION['usn']; ?></p>
            <p><strong>Role:</strong> <?php echo $_SESSION['role']; ?></p>
        </div>
        <!-- Form ubah password -->
        <form action="dashboardGuru.php" method="POST" enctype="multipart/form-data">
            <label for="kontak">Kontak:</label>
            <input type="text" name="kontak" id="kontak" value="<?php echo $profile['kontak']; ?>">
            <label for="bio">Bio:</label>
            <textarea class="bio" name="bio" id="bio"><?php echo $profile['bio']; ?></textarea>
            <label for="pp">Pilih Foto Profil:</label>
            <input type="file" name="pp" id="pp" accept="image/*">
            <button type="submit" name="simpanProfil">Simpan</button>
            <h2>Ubah Password</h2>
            <label for="current_password">Password Saat Ini:</label>
            <input type="password" name="current_password" id="current_password">
            <label for="new_password">Password Baru:</label>
            <input type="password" name="new_password" id="new_password">
            <label for="confirm_password">Konfirmasi Password Baru:</label>
            <input type="password" name="confirm_password" id="confirm_password">
            <button type="submit" name="change_password">Ubah Password</button>
        </form>
    </div>

    <div class="sidebar" id="myMenuSidebar">
        <span class="close-btn" onclick="toggleSidebar('myMenuSidebar')">&times;</span>
        <h2>Menu</h2>
        <ul>
            <li><a href="#Course">Course</a></li>
            <li><a href="#Quiz">Quiz</a></li>
            <li><a href="#Statistics">Statistics</a></li>
        </ul>
    </div>

    <!-- JavaScript untuk toggle sidebar -->
    <script>
        // script.js

function toggleSidebar(id) {
    var sidebar = document.getElementById(id);
    var content = document.getElementById('mainContent');

    // Tutup semua sidebar lainnya sebelum membuka sidebar yang baru
    document.querySelectorAll('.sidebar').forEach(function(sb) {
        if (sb.id !== id) {
            sb.classList.remove('show');
        }
    });

    // Toggle kelas 'show' pada sidebar yang dipilih
    sidebar.classList.toggle('show');

    // Toggle kelas 'shifted' pada konten utama
    if (sidebar.classList.contains('show')) {
        content.classList.add('shifted');
    } else {
        content.classList.remove('shifted');
    }
}


        // Select the dropdown button and content
        // const dropdownModal = document.querySelector('.dropdown-container');
        // const dropdownBtn = document.querySelector('.dropdown-btn');
        // const dropdownContent = document.querySelector('.dropdown-content');

        // // Toggle the dropdown visibility on button click
        // dropdownBtn.onclick = function() {
        //     if (dropdownContent.style.display === 'block') {
        //         dropdownContent.style.display = 'none';
        //     } else {
        //         dropdownContent.style.display = 'block';
        //     }
        // };

        // Close dropdown if clicking outside
        // window.onclick = function(event) {
        //     if (!event.target.matches('.dropdown-btn')) {

        //         if (event.target == modalc) {
        //         modalc.style.display = "none";
        //       }
        //     }
        // };


        // Dapatkan elemen modal
        var quizModal = document.getElementById("addQuizModal");

        // Dapatkan tombol yang membuka modal
        var quizBtn = document.getElementById("addQuiz");

        // Dapatkan tombol closed
        var quizCloseBtn = document.querySelector(".closedQuiz");

        // Ketika tombol "Add Quiz" diklik, buka modal
        quizBtn.onclick = function() {
            quizModal.style.display = "block";
        }

        // Ketika tombol closed diklik, tutup modal
        quizCloseBtn.onclick = function() {
            quizModal.style.display = "none";
        }

        // Jika pengguna mengklik di luar modal, tutup modal
        window.onclick = function(event) {
            if (event.target == quizModal) {
                quizModal.style.display = "none";
            }
            if (event.target == courseModal) {
                courseModal.style.display = "none";
            }
            if (event.target == dropdownModal) {
                dropdownContent.style.display = "none";
            }
        }

        var courseModal = document.getElementById("addCourseModal");

        // Dapatkan tombol yang membuka modal
        var courseBtn = document.getElementById("addCourse");

        // Dapatkan tombol closed
        var courseCloseBtn = document.querySelector(".closed");

        // Ketika tombol "Add Course" diklik, buka modal
        courseBtn.onclick = function() {
            courseModal.style.display = "block";
        }

        // Ketika tombol closed diklik, tutup modal
        courseCloseBtn.onclick = function() {
            courseModal.style.display = "none";
        }

        window.addEventListener('load', function() {
            document.getElementById('loader').style.display = 'none';
        });
        // Jika pengguna mengklik di luar modal, tutup modal
        // window.onclick = function (event) {

        // }
    </script>



</body>


</html>