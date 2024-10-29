<?php 
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);
// session_start();
include('service/database.php');
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../login.php"); // Redirect ke halaman login jika belum login
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <!-- Leaderboard Section -->
    <section class="leaderboard-section" id="leaderboard">
        <h2>Leaderboard Kuis</h2>
        <table class="leaderboard-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Nama Siswa</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>John Doe</td>
                    <td>95%</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Jane Smith</td>
                    <td>90%</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Michael Lee</td>
                    <td>88%</td>
                </tr>
            </tbody>
        </table>
    </section>

</body>

</html>
