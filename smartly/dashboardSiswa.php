<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include 'service/database.php';
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php"); // Redirect ke halaman login jika belum login
    exit();
}
$student_id = $_SESSION['student_id'];
// echo "Hello, " . $student_id . "!";
// Ambil data profil dari database berdasarkan sesi pengguna yang login
$stmt = $db->prepare("SELECT * FROM login WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();


// Jika form ubah password di-submit
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Ambil password saat ini dari database
    $stmt = $db->prepare("SELECT password FROM login WHERE id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verifikasi apakah password lama sesuai
    echo $_SESSION['password'];
    echo $current_password;

    if ($current_password == $_SESSION['password']) {
        // Cek apakah password baru dan konfirmasi password sesuai
        if ($new_password === $confirm_password) {
            // Hash password baru dan update di database
            // $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE login SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $new_password, $student_id);

            if ($stmt->execute()) {
                $message = "Password berhasil diubah!";
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
    $user_id = $_SESSION['student_id']; // Gantilah dengan ID pengguna saat ini dari sesi
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
                $user_id = $_SESSION['student_id']; // Gantilah dengan ID pengguna saat ini dari sesi
                $stmt = $db->prepare("UPDATE login SET foto = ? WHERE id = ?");
                $stmt->bind_param("si", $file_name, $user_id);
                $stmt->execute();
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['rating'])) {
        $rating = (int)$_POST['rating'];
        $course_id = (int)$_POST['idcourse'];

        // $course_id = (int)$_POST['idcourse'];

        // Validasi rating (1 hingga 5)
        if ($rating >= 1 && $rating <= 5) {
            // Tambahkan rating dan course_id ke database
            $stmt = $db->prepare("UPDATE enrollments SET rating=? WHERE student_id=? AND course_id=?");
            $stmt->bind_param("iii", $rating, $_SESSION['student_id'], $course_id);
            $stmt->execute();
            $stmt->close();

            $stmt = $db->prepare("SELECT sum_rating, num_rating FROM courses WHERE id = ?");
            $stmt->bind_param("i", $course_id);
            $stmt->execute();
            $stmt->bind_result($total_rating, $num_ratings);
            $stmt->fetch();
            $stmt->close();

            // Tambahkan rating baru ke total rating
            $total_rating += $rating;
            $num_ratings += 1;

            // Hitung rata-rata rating
            $avg_rating = $total_rating / $num_ratings;

            // Simpan perubahan ke database
            $update_stmt = $db->prepare("UPDATE courses SET sum_rating = ?, num_rating = ?, avg_rating = ? WHERE id = ?");
            $update_stmt->bind_param("iiii", $total_rating, $num_ratings, $avg_rating, $course_id);
            $update_stmt->execute();
            $update_stmt->close();
            // Simpan rating dan course_id ke database di sini
            exit();
        } 
    }
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
</head>

<style>
    /* General styling */
    body {
        font-family: Arial, sans-serif;
        background-color: #f7f7f7;
        padding: 0;
        background: url(bg2.png) fixed center center no-repeat;
        background-size: cover;
        margin: 0;
    }

    .container {
        max-width: 90%;
        margin: 50px auto;
        padding: 0px;
    }

    h1 {
        text-align: center;
        color: #333;
        margin-bottom: 30px;
    }

    /* Card container */
    .card-container {
        /* animation: slideUp 0.5s ease-in-out; */
        display: flex;
        flex-direction: row;
        justify-content: center;
        gap: 50px;
        flex-wrap: wrap;
    }

    /* Card styling */
    .card {
        flex: 1 1 calc(33% - 30px);
        max-width: 300px;
        box-sizing: border-box;
        background-color: #fff;
        padding: 20px;
        line-height: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;


        /* text-align: center; */
    }

    .card p,
    .card h3 {
        word-wrap: break-word;
        /* Membungkus kata panjang agar tidak keluar card */
        word-break: break-word;
        /* Memastikan kata tetap dipecah jika terlalu panjang */
        /*max-width: 50px;*/
    }



    .card:hover {
        transform: scale(1.05);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.2);
    }


    h3 {
        margin: 0;
        color: #007bff;
    }

    p {
        margin: 20px 0;
        color: #555;
    }

    /* Button styling */
    .edit-btn {
        background-color: #007bff;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
    }

    .edit-btn:hover {
        background-color: #0056b3;
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

    @keyframes fadeIn {
        0% {
            opacity: 0;
        }

        100% {
            opacity: 1;
        }
    }

    .container,
    .card-container {
        animation: fadeIn 0.5s ease-in;
    }



    /* Responsive design */
    @media screen and (max-width: 1024px) {
        .card {
            flex: 1 1 calc(33.333% - 20px);
        }
    }

    @media screen and (max-width: 768px) {
        .card {
            flex: 1 1 calc(50% - 20px);
        }
    }

    @media screen and (max-width: 480px) {
        .card {
            flex: 1 1 100%;
        }

        body {
            padding: 20px;
        }

        .container {
            max-width: 100%;
        }
    }

    .tombolProfil {
        cursor: pointer;
    }

    .container-fluid {
        margin: 10px;
    }

    /* Sidebar styling */
    .sidebar {
        box-sizing: border-box;
        overflow-y: auto;
        width: 320px;
        /* background-color: #333; */
        background: rgba(255, 255, 255, 0.4);
        /* Transparan */
        backdrop-filter: blur(3px);
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
        color: black;
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
        color: black;
    }

    .sidebar h3 {
        margin: 20px 0;
    }

    .sidebar .profile-info p {
        margin: 10px 0;
        color: black;
        font-weight: bold;
    }

    .sidebar label,
    .sidebar input,
    .sidebar textarea {
        display: block;
        width: 100%;
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
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .stars {
        display: inline-flex;
        cursor: pointer;
    }

    .star {
        font-size: 2em;
        color: #ccc;
        /* Warna default untuk bintang kosong */
        transition: color 0.2s;
    }

    .star.hover,
    .star.selected {
        color: gold;
        /* Warna untuk bintang terisi */
    }
</style>

<body>

    <div id="loader">
        <div class="spinner"></div>
    </div>
    <nav class="container-fluid">
        <ul class="nav-left">
            <li><strong><i>Smartly</i></strong></li>
        </ul>
        <ul class="nav-right">
            <li><a id="tombolnavutama" href="halKuis.php">Quiz</a></li>
            <li><a id="tombolnavutama" href="logout.php">Logout</a></li>
            <li><a id="tombolnavutama" href="index.php#about">About</a></li>
            <li><a id="tombolnavutama" href="index.php#contact">Contact</a></li>
            <li><a id="tombolnavutama" class="tombolProfil" onclick="toggleSidebar()">Profile</a></li>
        </ul>
    </nav>

    <!-- <a href="halKuis.php">lihat kuis</a> -->
    <div class="container">
        <div class="card-container">
            <?php
            // Include file koneksi database
            include('service/database.php');

            // Ambil semua kursus dari database

            // Ambil semua kursus
            $stmt = $db->prepare("SELECT * FROM courses");
            $stmt->execute();
            $resultcourses = $stmt->get_result();

            if ($resultcourses->num_rows > 0) {
                // Jika ada data kursus, tampilkan dalam bentuk card
                while ($course = $resultcourses->fetch_assoc()) {
                    echo '
        <div class="card">
            <h3>' . ($course['title']) . '</h3>
            <p>Kategori  : ' . htmlspecialchars($course['category']) . '</p>
            <p>Level     : ' . htmlspecialchars($course['level']) . '</p>
            <p>Jadwal    : ' . date("d-m-Y H:i", strtotime($course['jadwal'])) . '</p>
            <p>Rating    : ' . $course['avg_rating'] . '</p>';
            

                    // Cek apakah siswa sudah enroll dalam kursus ini
                    $query = "SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?";
                    $stmt2 = $db->prepare($query);
                    $stmt2->bind_param("ii", $_SESSION['student_id'], $course['id']);
                    $stmt2->execute();
                    $enrollment = $stmt2->get_result()->fetch_assoc();

                    // Cek jika $enrollment null
                    
                    if ($enrollment) {
                        // Siswa sudah enroll
                        if ($enrollment['enrolled_at'] !== null) {
                            // Siswa sudah selesai
                            if ($enrollment['finish_at'] !== null) {
                                if ($enrollment['rating'] !== null) {
                                    echo '<p style="color: green;">Status: Course Completed</p>';
                                    echo '<p style="color: green;">Rating Anda: ' . $enrollment['rating'] . '</p>';
                                } else {
                                // echo '<p style="color: green;">Status: Course Completed</p>';
                                echo '<a href="Course.php?id=' . $course['id'] . '">';
                                ?>
                                <div id="star-container" class="stars">
                                    <span class="star" data-value="1">&#9733;</span>
                                    <span class="star" data-value="2">&#9733;</span>
                                    <span class="star" data-value="3">&#9733;</span>
                                    <span class="star" data-value="4">&#9733;</span>
                                    <span class="star" data-value="5">&#9733;</span>
                                </div>
                                <input type="hidden" name="course_id" id="course_id" value="<?php echo $course['id']; ?>">
                                </a>
                                <?php
                            }
                            } else {
                                echo '<a style="background-color: #f39c12; text-decoration: none;" class="edit-btn" href="Course.php?id=' . $course['id'] . '">Open Course</a>';
                            }
                        }
                    } else {
                        // Siswa belum enroll, tampilkan tombol "Enroll Course"
                        echo '<a style="text-decoration: none;" class="edit-btn" href="cekEnroll.php?id=' . $course['id'] . '">Enroll Course</a>';
                    }
                    
                    echo '</div>'; // Tutup card
                }
            } else {
                echo 'Tidak ada kursus yang tersedia.';
            }
            ?>
        </div>
    </div>

    <?php
    // include('service/database.php');
    // session_start();


    ?>

    <!-- Sidebar -->
    <div class="sidebar" id="mySidebar">

        <span class="close-btn" onclick="toggleSidebar()">&times;</span>
        <h2>Profil Siswa</h2>
        <div class="profile-info">
            <img src="download.php?file=<?php echo $profile['foto']; ?>" class="profile-picture">
            <p><strong>Nama:</strong> <?php echo $_SESSION['name']; ?></p>
            <p><strong>Username:</strong> <?php echo $_SESSION['usn']; ?></p>
            <p><strong>Role:</strong> <?php echo $_SESSION['role']; ?></p>
        </div>


        <form action="dashboardSiswa.php" method="POST" enctype="multipart/form-data">

            <label for="kontak">Kontak:</label>
            <input type="text" name="kontak" id="kontak" value="<?php echo $profile['kontak']; ?>">

            <label for="bio">Bio:</label>
            <textarea name="bio" id="bio"><?php echo $profile['bio']; ?></textarea>

            <label for="pp">Pilih Foto Profil:</label>
            <input type="file" name="pp" id="pp" accept="image/*">

            <button type="submit" name="simpanProfil">simpan</button>
            <h3>Ubah Password</h3>
            <label for="current_password">Password Saat Ini:</label>
            <input type="password" name="current_password" id="current_password">

            <label for="new_password">Password Baru:</label>
            <input type="password" name="new_password" id="new_password">

            <label for="confirm_password">Konfirmasi Password Baru:</label>
            <input type="password" name="confirm_password" id="confirm_password">

            <button type="submit" name="change_password">Ubah Password</button>

        </form>

    </div>


    <!-- JavaScript untuk toggle sidebar -->
    <script>
        window.addEventListener('load', function() {
            document.getElementById('loader').style.display = 'none';
        });

        function toggleSidebar() {
            var sidebar = document.getElementById('mySidebar');
            if (sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
                sidebar.classList.add('hide');
            } else {
                sidebar.classList.remove('hide');
                sidebar.classList.add('show');
            }
        }


        const stars = document.querySelectorAll('.star');
        const idcourse = document.getElementById('course_id').value;

        stars.forEach((star) => {
            star.addEventListener('mouseover', () => {
                const value = star.getAttribute('data-value');
                highlightStars(value);
            });

            star.addEventListener('mouseout', () => {
                highlightStars(0);
            });

            // Klik untuk memilih rating dan mengirim secara otomatis
            star.addEventListener('click', () => {
                const rating = star.getAttribute('data-value');
                highlightStars(rating);

                // Kirim rating dengan AJAX
                sendRating(rating, idcourse);
            });
        });

        function highlightStars(rating) {
            stars.forEach((star) => {
                if (star.getAttribute('data-value') <= rating) {
                    star.classList.add('selected');
                } else {
                    star.classList.remove('selected');
                }
            });
        }

        function sendRating(rating, idcourse) {
            console.log("Rating: " + rating + ", Course ID: " + idcourse);

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "dashboardSiswa.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        alert("Terima kasih, rating Anda telah dikirim: " + rating + " bintang!");
                    } else {
                        console.error("Error: " + xhr.status);
                    }
                }
            };

            xhr.send("rating=" + encodeURIComponent(rating) + "&idcourse=" + encodeURIComponent(idcourse));
        }
    </script>





    <?php include('layout/footer.html'); ?>

</body>

</html>