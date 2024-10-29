<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);
ob_start();
    include('layout/header.html');
    include('service/database.php');
    if (isset($_POST['register'])) {
        $usn = $_POST['usn'];
        $password = $_POST['password'];
        $nama = $_POST['nama'];
        $role = $_POST['role'];

        $stmt = $db->prepare("SELECT * FROM login WHERE usn = ?");
        $stmt->bind_param("s", $usn);
        $stmt->execute();
        $cekdata = $stmt->get_result();


        if ($cekdata->num_rows > 0) {
            echo "<div class='notificationfail'>
                <span class='icon'>⚠️</span>
                <span class='message'>Username sudah terdaftar</span>
                </div>";;
        } else {
            $stmt = $db->prepare("INSERT INTO login (usn, password, nama, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $usn, $password, $nama, $role); // Binding variabel

            if ($stmt->execute()) {
                // Jika insert berhasil, tampilkan pesan sukses
                echo "<div class='notificationsuccess'>
                        <span class='icon'>✔</span>
                        <span class='message'>Registrasi Berhasil</span>
                        </div>";
                header ('location:login.php');
                ob_end_flush();
            } else {
                // Jika ada kesalahan dalam insert, tampilkan pesan error
                echo "Error: " . $stmt->error;
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
    <title>Smartly</title>
    <style>
        body {
            background: url(bg.jpg) fixed center center no-repeat;
            background-size: cover;
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
</head>

<body>
<div id="loader">
        <div class="spinner"></div>
    </div>

    <main class="container">
        <div class="form-container">
            <h2>Register</h2>
            <form class="tombol" action="regis.php" method="POST">
                <div class="input-group">
                <input type="text" name="nama" placeholder="" required>
                <label>Nama</label>
                </div>
                <div class="input-group">
                <input type="text" name="usn" placeholder="" required>
                <label>Username</label>
                </div>
                <div class="input-group">
                <input type="password" name="password" placeholder="" required>
                <label>Password</label>
                </div>
                
                <label class="role" for="role">Daftar Sebagai:</label>
                <select name="role" id="role" required>
                    <option value="siswa">Siswa</option>
                    <option value="guru">Guru</option>
                </select>
                
                <button type="submit" name="register">Register</button>
            </form>


            <h3>Already Have Account? <a href="login.php">Login</a></h3>


        </div>
    </main>
    <?php include('layout/footer.html'); ?>
</body>
<script>
            window.addEventListener('load', function() {
            document.getElementById('loader').style.display = 'none';
        });
</script>
</html>