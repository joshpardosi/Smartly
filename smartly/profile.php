<?php
// ini_set('display_errors',1);
// ini_set('display_startup_errors',1);
// error_reporting(E_ALL);
session_start();
// Sertakan file koneksi ke database
include('service/database.php');

// Pastikan bahwa ID guru dikirim melalui URL (misalnya, profile.php?id=1)
if (isset($_GET['id'])) {
    $teacher_id = $_GET['id'];
    // Query untuk mendapatkan data guru berdasarkan ID
    $stmt = $db->prepare("SELECT * FROM login WHERE id = ?");
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Periksa apakah guru ditemukan
    if ($result->num_rows > 0) {
        $teacher = $result->fetch_assoc();
    } else {
        echo "Guru tidak ditemukan.";
        exit();
    }
} else {
    echo "ID guru tidak disediakan.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Guru - <?php echo $teacher['nama']; ?></title>
    <style>
    @keyframes fadeIn {
        0% {
            opacity: 0;
            transform: translateY(20px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes zoomIn {
        0% {
            transform: scale(0.5);
            opacity: 0;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    body {
        font-family: Arial, sans-serif;
        background: rgb(23,14,174);
        background: linear-gradient(90deg, rgba(2,2,248,0.5) 0%,  rgba(4,151,253,1) 50%,  rgba(2,2,248,0.5)100%);
        margin: 0;
        padding: 20px;
    }

    .container {
        max-width: 800px;
        margin: auto;
        /* background-color: black; */
        /* background: rgba(0, 0, 0, 0.4); */
        backdrop-filter: blur(3px);
        background: rgba(255, 255, 255, 0.4);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        animation: fadeIn 1s ease-in-out;
    }

    h1 {
        text-align: center;
        color: #333;
        font-size: 2.5em;
        animation: fadeIn 1s ease-in-out;
    }

    .profile-picture {
        display: block;
        margin: 0 auto 20px;
        border-radius: 50%;
        width: 150px;
        height: 150px;
        object-fit: cover;
        border: 3px solid #007bff;
        animation: zoomIn 1s ease-in-out;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px auto;
        animation: fadeIn 1.2s ease-in-out;
    }

    th,
    td {
        max-width: 600px;
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
        color: #555;
        word-wrap: break-word;
    }

    th {
        background-color: #007bff;
        color: white;
    }

    .back-link {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 15px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s, transform 0.3s;
        text-align: center;
    }

    .back-link:hover {
        background-color: #0056b3;
        transform: scale(1.05);
    }

    .contact-icon {
        width: 20px;
        vertical-align: middle;
        margin-right: 5px;
    }
</style>
</head>

<body>
    <div class="container">
        <h1><?php echo $teacher['nama']; ?></h1>
        <img src="download.php?file=<?php echo $teacher['foto']; ?>" class="profile-picture">

        <table>
            <colgroup>
                <col style="width: 30%;">
                <col style="width: 70%;">
            </colgroup>
            <tr>
                <th>Informasi</th>
                <th>Detail</th>
            </tr>
            <tr>
                <td>Nama</td>
                <td><?php echo $teacher['nama']; ?></td>
            </tr>
            <tr>
                <td>Username</td>
                <td><?php echo $teacher['usn']; ?></td>
            </tr>
            <tr>
                <td>Role</td>
                <td><?php echo $teacher['role']; ?></td>
            </tr>
            <tr>
                <td>Bio</td>
                <td><?php echo nl2br($teacher['bio']); ?></td>
            </tr>
            <tr>
                <td>Kontak</td>
                <td>
                    <a href="https://wa.me/<?php echo $teacher['kontak']; ?>" target="_blank">
                        <img src="wa.png" alt="WhatsApp" class="contact-icon">
                        <?php echo $teacher['kontak']; ?>
                    </a>
                </td>
            </tr>
        </table>

        <a href="dashboardSiswa.php?id=<?php echo $_SESSION['student_id']; ?>" class="back-link">Kembali ke Dashboard</a>
    </div>

</body>


</html>