<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('service/database.php');

// Pastikan hanya guru yang bisa menambah quiz
// session_start();
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['role'] !== 'guru') {
    header("Location: ../login.php");
    exit();
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
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Quiz Modal</title>
    <style>
        /* Gaya modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            /* Background semi-transparent */
            overflow: auto;
        }

        /* Gaya konten modal */
        .modal-content {
            border-radius: 15px;
            background-color: #fff;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Tombol closed */
        .closed {
            color: #fff;
            float: right;
            font-size: 24px;
            font-weight: bold;
            background-color: red;
            border: none;
            cursor: pointer;
            padding: 5px 10px;
        }

        .closed:hover {
            background-color: darkred;
        }

        /* Form styling */
        .bawah {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        input {
            padding: 10px;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        button {
            padding: 12px;
            background-color: #0073ff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0083ff;
        }
    </style>
</head>

<body>
    <!-- Tombol untuk membuka modal -->
    <button id="addQuiz">Add Quiz</button>

    <!-- Modal -->
    <div id="addQuizModal" class="modal">
        <!-- Konten modal -->
        <div class="modal-content">
            <button class="closed">&times;</button>
            <h2>Add Quiz</h2>
            <form action="addQuiz.php" method="POST">
                <div class="bawah">
                    <input type="text" name="title" placeholder="Quiz Title" required>
                </div>
                <button type="submit" name="create_quiz">Create Quiz</button>
            </form>
        </div>
    </div>

    <script>
        // Dapatkan elemen modal
        var modal = document.getElementById("addQuizModal");

        // Dapatkan tombol yang membuka modal
        var btn = document.getElementById("addQuiz");

        // Dapatkan tombol closed
        var closeBtn = document.querySelector(".closed");

        // Ketika tombol diklik, buka modal
        btn.onclick = function() {
            modal.style.display = "block";
        }

        // Ketika tombol closed diklik, tutup modal
        closeBtn.onclick = function() {
            modal.style.display = "none";
        }

        // Jika pengguna mengklik di luar modal, tutup modal
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>

</html>
