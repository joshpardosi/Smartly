<?php 
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);
    session_start();

    include('service/database.php');
    
ob_start();
    ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Smartly</title>
    <!-- <link rel="stylesheet" href="layout/style.css"> -->
</head>
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

<body>
    <?php include('layout/header.html'); 
    
    if (isset($_POST['login'])) {
        $usn = $_POST['usn'];
        $password = $_POST['password'];

        // $results= mysqli_query($db, "SELECT * FROM login WHERE usn = '$usn' AND password = '$password'");

        $stmt = $db->prepare("SELECT * FROM login WHERE usn = ? AND password = ?");
        $stmt->bind_param("ss", $usn, $password);
        $stmt->execute();
        $results = $stmt->get_result();

        if ($results->num_rows > 0) {
            $users = mysqli_fetch_assoc($results);
            if ($users['role'] == 'siswa') {
                $_SESSION['password'] = $password;
                $_SESSION['name'] = $users['nama'];
                $_SESSION['usn'] = $usn;
                $_SESSION['role'] = $users['role'];
                $_SESSION['student_id'] = $users['id'];
                header("Location: dashboardSiswa.php");
                ob_end_flush();
            } else if ($users['role'] == 'guru') {
                $_SESSION['password'] = $users['password'];
                $_SESSION['name'] = $users['nama'];
                $_SESSION['usn'] = $usn;
                $_SESSION['role'] = $users['role'];
                $_SESSION['teacher_id'] = $users['id'];
                header("Location: dashboardGuru.php");
                ob_end_flush();
            }
        } else {
            echo "<div class='notificationfail'>
            <span class='icon'>⚠️</span>
            <span class='message'>Username atau Password salah. Silakan coba lagi!</span> </div>";
        }
        
    }
?>
<div id="loader">
        <div class="spinner"></div>
    </div>
    <main class="container">
        <div class="form-container">
            <h2>Login</h2>
            <form class="tombol" action="login.php" method="POST">
                <div class="input-group">
                <input type="text" name="usn" placeholder="" required>
                <label>Username</label>
                </div>
                <div class="input-group">
                <input type="password" name="password" placeholder="" required>
                <label>Password</label>
                </div>
                <button type="submit" name="login">Login</button>
            </form>

            <h3>Don't Have Account? <a href="regis.php">Register</a></h3>
        </div>


    </main>
    <?php 
    ob_end_flush();
    include('layout/footer.html'); ?>
</body>
<script>
            window.addEventListener('load', function() {
            document.getElementById('loader').style.display = 'none';
        });
</script>
</html>